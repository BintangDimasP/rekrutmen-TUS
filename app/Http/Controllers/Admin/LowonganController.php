<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Prodi;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    private const DEFAULT_DESKRIPSI = "Dokumen yang perlu dipersiapkan :
- Pas Photo Formal Berwarna Berlatar Abu-Abu;
- Scan KTP;
- Surat Lamaran dan Curiculum Vitae/Resume/Riwayat Hidup;
- Sertifikat Kemampuan Bahasa Inggris (PBT/TOEFL/EPrT/CBT/IBT/IELST/AcEPT);
- Scan Ijazah dan Transkrip lengkap, dan SK Penyetaraan bagi lulusan Luar Negeri (dapat mendafarkan melalui link: piln.kemdikbud.go.id);
- Sertifikat Kompetensi/Keahlian Khusus;
- Contoh karya ilmiah yang relevan dan telah dipublikasikan.
- Surat Pernyataan bersedia untuk mengurus Surat Pemberhentian (Format pada link: tel-u.ac.id/suratpernyataanpemberhentian)(apabila bekerja di Instansi Lain)

Dokumen tambahan bagi pelamar yang sudah memiliki homebase:
- SK Jabatan Akademik Dosen (JAD) (apabila ada);
- SK Penetapan Angka Kredit (PAK) (apabila ada);
- Bukti Registrasi Dosen;
- SK Penyetaraan Pangkat/Inpassing (apabila ada);
- Sertifikat Pendidik (apabila ada);
- Surat Keterangan Pemberhentian Pembayaran / SKPP Serdos (saat pemberkasan);
- Surat Pernyataan bersedia untuk mengurus Surat Lolos Butuh (Format pada link: bit.ly/Surat-Pernyataan-Lolos-Butuh)";

    /** Daftar lowongan */
    public function index()
    {
        $lowongans = Lowongan::with('prodi')->latest()->get();
        return view('admin.lowongan.index', compact('lowongans'));
    }

    /** Form buat lowongan baru */
    public function create()
    {
        $prodis            = Prodi::orderBy('nama')->get();
        $defaultDeskripsi  = self::DEFAULT_DESKRIPSI;

        $prodiPrioritasOptions = [
            'Sistem Informasi', 'Teknik Informatika', 'Teknik Elektro',
            'Teknik Industri', 'Manajemen', 'Akuntansi', 'Desain Komunikasi Visual',
            'Ilmu Komunikasi', 'Administrasi Bisnis', 'Teknik Telekomunikasi',
        ];

        $skillOptions = [
            'IoT (Internet of Things)', 'Machine Learning', 'Deep Learning',
            'Data Science', 'Cloud Computing', 'Cybersecurity', 'Blockchain',
            'Mobile Development', 'Web Development', 'Embedded Systems',
            'Computer Networks', 'Artificial Intelligence', 'Robotics',
            'Business Intelligence', 'UI/UX Design',
        ];

        return view('admin.lowongan.create', compact(
            'prodis', 'defaultDeskripsi', 'prodiPrioritasOptions', 'skillOptions'
        ));
    }

    /** Simpan lowongan baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_posisi'      => 'required|string|max:255',
            'prodi_id'         => 'required|exists:prodis,id',
            'jenjang_minimal'  => 'required|in:D3,S1,S2,S3',
            'minimal_ipk'      => 'required|numeric|min:0|max:4',
            'prodi_prioritas'  => 'nullable|string|max:255',
            'skill_dibutuhkan' => 'nullable|string|max:255',
            'kuota'            => 'required|integer|min:1',
            'tanggal_tutup'    => 'required|date|after:today',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|in:aktif,ditutup,draft',
        ]);

        Lowongan::create($validated);

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $validated['nama_posisi'] . '" berhasil dibuat.');
    }

    /** Detail lowongan (beserta daftar pelamar) */
    public function show(Lowongan $lowongan)
    {
        $lowongan->load(['prodi', 'lamarans.pelamar.user']);
        return view('admin.lowongan.show', compact('lowongan'));
    }

    /** Form edit lowongan */
    public function edit(Lowongan $lowongan)
    {
        $prodis = Prodi::orderBy('nama')->get();

        $prodiPrioritasOptions = [
            'Sistem Informasi', 'Teknik Informatika', 'Teknik Elektro',
            'Teknik Industri', 'Manajemen', 'Akuntansi', 'Desain Komunikasi Visual',
            'Ilmu Komunikasi', 'Administrasi Bisnis', 'Teknik Telekomunikasi',
        ];

        $skillOptions = [
            'IoT (Internet of Things)', 'Machine Learning', 'Deep Learning',
            'Data Science', 'Cloud Computing', 'Cybersecurity', 'Blockchain',
            'Mobile Development', 'Web Development', 'Embedded Systems',
            'Computer Networks', 'Artificial Intelligence', 'Robotics',
            'Business Intelligence', 'UI/UX Design',
        ];

        return view('admin.lowongan.edit', compact('lowongan', 'prodis', 'prodiPrioritasOptions', 'skillOptions'));
    }

    /** Update lowongan */
    public function update(Request $request, Lowongan $lowongan)
    {
        $validated = $request->validate([
            'nama_posisi'      => 'required|string|max:255',
            'prodi_id'         => 'required|exists:prodis,id',
            'jenjang_minimal'  => 'required|in:D3,S1,S2,S3',
            'minimal_ipk'      => 'required|numeric|min:0|max:4',
            'prodi_prioritas'  => 'nullable|string|max:255',
            'skill_dibutuhkan' => 'nullable|string|max:255',
            'kuota'            => 'required|integer|min:1',
            'tanggal_tutup'    => 'required|date',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|in:aktif,ditutup,draft',
        ]);

        $lowongan->update($validated);

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $lowongan->nama_posisi . '" berhasil diperbarui.');
    }

    /** Hapus lowongan */
    public function destroy(Lowongan $lowongan)
    {
        $nama = $lowongan->nama_posisi;
        $lowongan->delete();

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $nama . '" berhasil dihapus.');
    }
}
