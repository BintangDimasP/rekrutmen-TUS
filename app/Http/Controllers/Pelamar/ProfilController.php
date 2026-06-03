<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $pelamar = auth()->user()->pelamar;
        return view('pelamar.profil.index', compact('pelamar'));
    }

    public function update(Request $request)
    {
        $pelamar = auth()->user()->pelamar;

        $request->validate([
            // Data Diri
            'email'         => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'nik'           => 'required|string|size:16|unique:pelamars,nik,' . $pelamar->id,
            'nama'          => 'required|string|max:255',
            'tempat_lahir'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'no_telepon'    => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'kewarganegaraan' => 'required|string|max:255',
            'status_pernikahan' => 'required|string|max:255',
            'alamat_domisili' => 'required|string',
            'alamat_ktp'      => 'required|string',

            // Pendidikan Level 1
            'jenjang'           => 'nullable|in:S1,S2,S3',
            'institusi'         => 'nullable|string|max:255',
            'prodi_pendidikan'  => 'nullable|string|max:255',
            'akreditas'         => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Tidak Terakreditasi',
            'no_ijazah'         => 'nullable|string|max:255',
            'ipk'               => 'nullable|numeric|min:0|max:4',
            'file_ijazah'       => 'nullable|file|mimes:pdf|max:5120',
            'file_transkrip'    => 'nullable|file|mimes:pdf|max:5120',

            // Pendidikan Level 2
            'jenjang_2'           => 'nullable|in:S1,S2,S3',
            'institusi_2'         => 'nullable|string|max:255',
            'prodi_pendidikan_2'  => 'nullable|string|max:255',
            'akreditas_2'         => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Tidak Terakreditasi',
            'no_ijazah_2'         => 'nullable|string|max:255',
            'ipk_2'               => 'nullable|numeric|min:0|max:4',
            'file_ijazah_2'       => 'nullable|file|mimes:pdf|max:5120',
            'file_transkrip_2'    => 'nullable|file|mimes:pdf|max:5120',

            // Pendidikan Level 3
            'jenjang_3'           => 'nullable|in:S1,S2,S3',
            'institusi_3'         => 'nullable|string|max:255',
            'prodi_pendidikan_3'  => 'nullable|string|max:255',
            'akreditas_3'         => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Tidak Terakreditasi',
            'no_ijazah_3'         => 'nullable|string|max:255',
            'ipk_3'               => 'nullable|numeric|min:0|max:4',
            'file_ijazah_3'       => 'nullable|file|mimes:pdf|max:5120',
            'file_transkrip_3'    => 'nullable|file|mimes:pdf|max:5120',

            // Dokumen Pendukung
            'file_cv'                => 'nullable|file|mimes:pdf|max:5120',
            'file_pas_foto'          => 'nullable|file|mimes:jpg,jpeg|max:2048',
            'file_ktp'               => 'nullable|file|mimes:jpg,jpeg|max:2048',
            'kategori_sertifikat'    => 'nullable|in:kompetensi,keahlian_khusus',
            'file_sertifikat'        => 'nullable|file|mimes:pdf|max:5120',
            'jenis_tes_bahasa'       => 'nullable|in:PBT,TOEFL_ITP,EPrT,CBT,IBT,IELTS,AcEPT',
            'skor_bahasa'            => 'nullable|numeric',
            'tanggal_tes_bahasa'     => 'nullable|date',
            'file_sertifikat_bahasa' => 'nullable|file|mimes:pdf|max:5120',

            // Riwayat Akademik Dasar
            'nidn'              => 'nullable|string|max:255',
            'homebase'          => 'nullable|string|max:255',
            'jabatan_akademik'  => 'nullable|in:asisten_ahli,lektor,lektor_kepala,guru_besar,non_jabatan',
            'minat_riset'       => 'nullable|string',
            'h_index'           => 'nullable|integer|min:0',
            'file_kartu_dosen'  => 'nullable|file|mimes:pdf|max:5120',

            // Dokumen Homebase Tambahan
            'file_jad'                      => 'nullable|file|mimes:pdf|max:5120',
            'file_pak'                      => 'nullable|file|mimes:pdf|max:5120',
            'file_registrasi_dosen'         => 'nullable|file|mimes:pdf|max:2048',
            'file_inpassing'                => 'nullable|file|mimes:pdf|max:5120',
            'file_serdik'                   => 'nullable|file|mimes:pdf|max:5120',
            'file_skpp_serdos'              => 'nullable|file|mimes:pdf|max:5120',
            'file_pernyataan_lolos_butuh'   => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->except([
            'file_ijazah', 'file_transkrip', 
            'file_ijazah_2', 'file_transkrip_2',
            'file_ijazah_3', 'file_transkrip_3',
            'file_cv', 'file_pas_foto', 'file_ktp', 
            'file_sertifikat', 'file_sertifikat_bahasa', 'file_kartu_dosen',
            'file_jad', 'file_pak', 'file_registrasi_dosen', 'file_inpassing',
            'file_serdik', 'file_skpp_serdos', 'file_pernyataan_lolos_butuh'
        ]);

        // List all dynamic file fields
        $fileFields = [
            'file_ijazah', 'file_transkrip', 
            'file_ijazah_2', 'file_transkrip_2',
            'file_ijazah_3', 'file_transkrip_3',
            'file_cv', 'file_pas_foto', 'file_ktp', 
            'file_sertifikat', 'file_sertifikat_bahasa', 'file_kartu_dosen',
            'file_jad', 'file_pak', 'file_registrasi_dosen', 'file_inpassing',
            'file_serdik', 'file_skpp_serdos', 'file_pernyataan_lolos_butuh'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($pelamar->$field) {
                    Storage::disk('public')->delete($pelamar->$field);
                }
                $data[$field] = $request->file($field)->store("pelamar/" . $pelamar->user_id, 'public');
            }
        }

        $pelamar->update($data);

        // Sync name and email to user table
        $user = auth()->user();
        $user->name = $request->nama;
        
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null; // Reset verification status for new email
            $user->save();
            // Automatically send new verification email
            $user->sendEmailVerificationNotification();
        } else {
            $user->save();
        }

        return redirect()->route('pelamar.profil.index')->with('success', 'Profil berhasil diperbarui.');
    }
}
