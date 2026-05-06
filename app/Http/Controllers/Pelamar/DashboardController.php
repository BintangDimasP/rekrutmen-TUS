<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Lowongan;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pelamar = auth()->user()->pelamar;

        $totalLamaran    = 0;
        $lamaranAktif    = 0;
        $lamaranDiterima = 0;
        $lamaranDitolak  = 0;
        $recentLamarans  = collect();

        // Hanya tampilkan popup sekali setelah login
        $showProfileModal = false;
        $incompleteSections = [];

        if ($pelamar) {
            $lamarans = Lamaran::where('pelamar_id', $pelamar->id)->get();
            $totalLamaran    = $lamarans->count();
            $lamaranAktif    = $lamarans->whereIn('status', ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2'])->count();
            $lamaranDiterima = $lamarans->where('status', 'diterima')->count();
            $lamaranDitolak  = $lamarans->where('status', 'ditolak')->count();

            $recentLamarans = Lamaran::with('lowongan.prodi')
                ->where('pelamar_id', $pelamar->id)
                ->latest()
                ->take(5)
                ->get();

            // Cek kelengkapan profil hanya saat pertama login
            if ($request->session()->pull('show_profile_reminder', false)) {
                $showProfileModal = true;

                // 1. Data Diri
                $dataDiriFields = ['nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'no_telepon', 'jenis_kelamin', 'alamat'];
                foreach ($dataDiriFields as $field) {
                    if (empty($pelamar->$field)) {
                        $incompleteSections[] = 'Data Diri';
                        break;
                    }
                }

                // 2. Riwayat Pendidikan
                $pendidikanFields = ['jenjang', 'institusi', 'file_ijazah', 'file_transkrip'];
                foreach ($pendidikanFields as $field) {
                    if (empty($pelamar->$field)) {
                        $incompleteSections[] = 'Riwayat Pendidikan';
                        break;
                    }
                }

                // 3. Dokumen & Sertifikat
                $dokumenFields = ['file_cv', 'file_pas_foto', 'file_ktp'];
                foreach ($dokumenFields as $field) {
                    if (empty($pelamar->$field)) {
                        $incompleteSections[] = 'Dokumen & Sertifikat';
                        break;
                    }
                }

                // 4. Data Akademik
                $akademikFields = ['nidn', 'homebase', 'jabatan_akademik'];
                foreach ($akademikFields as $field) {
                    if (empty($pelamar->$field)) {
                        $incompleteSections[] = 'Data Akademik';
                        break;
                    }
                }

                // 5. Dokumen Pelamar Ber-Homebase (hanya jika NIDN terisi)
                if (!empty($pelamar->nidn)) {
                    $homebaseFields = [
                        'file_kartu_dosen', 'file_jad', 'file_pak',
                        'file_registrasi_dosen', 'file_inpassing',
                        'file_serdik', 'file_skpp_serdos', 'file_pernyataan_lolos_butuh',
                    ];
                    foreach ($homebaseFields as $field) {
                        if (empty($pelamar->$field)) {
                            $incompleteSections[] = 'Dokumen Pelamar Ber-Homebase';
                            break;
                        }
                    }
                }

                // Jika semuanya lengkap, tidak perlu tampilkan modal
                if (empty($incompleteSections)) {
                    $showProfileModal = false;
                }
            }
        }

        $lowonganCount = Lowongan::where('status', 'aktif')->count();

        return view('pelamar.dashboard', compact(
            'pelamar',
            'totalLamaran',
            'lamaranAktif',
            'lamaranDiterima',
            'lamaranDitolak',
            'recentLamarans',
            'lowonganCount',
            'showProfileModal',
            'incompleteSections'
        ));
    }
}
