<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use App\Models\User;
use App\Rules\NotDosenInternalDomain;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Step 1
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                'unique:'.User::class,
                new NotDosenInternalDomain(),
            ],
            'password' => ['required', Rules\Password::defaults()],

            // Step 2
            'nik'               => [
                'required', 'string', 'digits:16',
                // Validasi unique via PHP karena kolom dienkripsi di DB
                function ($attr, $value, $fail) {
                    $exists = \App\Models\Pelamar::all()->first(fn($p) => $p->nik === $value);
                    if ($exists) $fail('NIK sudah terdaftar. Pastikan Anda belum pernah mendaftar sebelumnya.');
                },
            ],
            'nama'              => ['required', 'string', 'max:255'],
            'tempat_lahir'      => ['required', 'string', 'max:255'],
            'tanggal_lahir'     => ['required', 'date'],
            'no_telepon'        => [
                'required', 'string', 'regex:/^08[0-9]{6,13}$/', 'max:15',
                function ($attr, $value, $fail) {
                    $exists = \App\Models\Pelamar::all()->first(fn($p) => $p->no_telepon === $value);
                    if ($exists) $fail('No. telepon sudah terdaftar. Gunakan nomor telepon yang berbeda.');
                },
            ],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'kewarganegaraan'   => ['required', 'string', 'max:255'],
            'status_pernikahan' => ['required', 'string', 'max:255'],
            'alamat_domisili'   => ['required', 'string'],
            'alamat_ktp'        => ['required', 'string'],
            
            // Step 3
            'jenjang' => ['nullable', 'in:S1,S2,S3'],
            'institusi' => ['nullable', 'string', 'max:255'],
            'prodi_pendidikan' => ['nullable', 'string', 'max:255'],
            'akreditas' => ['nullable', 'in:A,B,C,Unggul,Baik Sekali,Baik,Tidak Terakreditasi'],
            'no_ijazah' => ['nullable', 'string', 'max:255'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ijazah' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'transkrip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            // Step 4
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'pas_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'ktp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'kategori_sertifikat' => ['nullable', 'in:kompetensi,keahlian_khusus'],
            'sertifikat_kompetensi' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'jenis_tes_bahasa' => ['nullable', 'in:PBT,TOEFL_ITP,EPrT,CBT,IBT,IELTS,AcEPT'],
            'skor_bahasa' => ['nullable', 'numeric'],
            'tanggal_tes_bahasa' => ['nullable', 'date'],
            'sertifikat_bahasa' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Step 5
            'nidn' => ['nullable', 'string', 'digits_between:1,10'],
            'homebase' => ['nullable', 'string', 'max:255'],
            'jabatan_akademik' => ['nullable', 'in:asisten_ahli,lektor,lektor_kepala,guru_besar,non_jabatan'],
            'minat_riset' => ['nullable', 'string'],
            'h_index' => ['nullable', 'integer', 'min:0'],
            'kartu_dosen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pelamar',
        ]);

        $pelamarData = $request->only([
            'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'no_telepon', 'jenis_kelamin', 
            'kewarganegaraan', 'status_pernikahan', 'alamat_domisili', 'alamat_ktp',
            'jenjang', 'institusi', 'prodi_pendidikan', 'akreditas', 'no_ijazah', 'ipk',
            'kategori_sertifikat', 'jenis_tes_bahasa', 'skor_bahasa', 'tanggal_tes_bahasa',
            'nidn', 'homebase', 'jabatan_akademik', 'minat_riset', 'h_index'
        ]);

        $pelamarData['user_id'] = $user->id;

        // Process file uploads
        $fileFields = [
            'ijazah' => 'file_ijazah',
            'transkrip' => 'file_transkrip',
            'cv' => 'file_cv',
            'pas_foto' => 'file_pas_foto',
            'ktp' => 'file_ktp',
            'sertifikat_kompetensi' => 'file_sertifikat',
            'sertifikat_bahasa' => 'file_sertifikat_bahasa',
            'kartu_dosen' => 'file_kartu_dosen'
        ];

        foreach ($fileFields as $requestKey => $dbKey) {
            if ($request->hasFile($requestKey)) {
                $pelamarData[$dbKey] = $request->file($requestKey)->store("pelamar/{$user->id}", 'public');
            }
        }

        \App\Models\Pelamar::create($pelamarData);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Check if email is already registered (AJAX) — Step 1.
     */
    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => [
                    'required', 'string', 'lowercase', 'email', 'max:255',
                    'unique:'.User::class,
                    new NotDosenInternalDomain(),
                ],
            ]);

            return response()->json(['valid' => true]);
        } catch (ValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->validator->errors()->first('email'),
            ], 422);
        }
    }

    /**
     * Check if NIK / No. Telepon sudah terdaftar (AJAX) — Step 2.
     * Data dienkripsi, jadi pengecekan dilakukan via PHP bukan SQL.
     */
    public function checkIdentity(Request $request)
    {
        $nik       = $request->input('nik');
        $noTelepon = $request->input('no_telepon');

        if ($nik) {
            // Validasi format dulu
            if (!preg_match('/^\d{16}$/', $nik)) {
                return response()->json(['valid' => false, 'field' => 'nik', 'message' => 'NIK harus terdiri dari 16 digit angka.'], 422);
            }
            $exists = \App\Models\Pelamar::all()->first(fn($p) => $p->nik === $nik);
            if ($exists) {
                return response()->json(['valid' => false, 'field' => 'nik', 'message' => 'NIK sudah terdaftar. Pastikan Anda belum pernah mendaftar sebelumnya.'], 422);
            }
        }

        if ($noTelepon) {
            if (!preg_match('/^08[0-9]{6,13}$/', $noTelepon)) {
                return response()->json(['valid' => false, 'field' => 'no_telepon', 'message' => 'Format No. Telepon harus diawali "08" dan berisi 8–15 digit.'], 422);
            }
            $exists = \App\Models\Pelamar::all()->first(fn($p) => $p->no_telepon === $noTelepon);
            if ($exists) {
                return response()->json(['valid' => false, 'field' => 'no_telepon', 'message' => 'No. telepon sudah terdaftar. Gunakan nomor telepon yang berbeda.'], 422);
            }
        }

        return response()->json(['valid' => true]);
    }
}
