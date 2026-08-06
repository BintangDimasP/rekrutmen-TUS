<?php
use App\Models\Pelamar;
use App\Models\Lamaran;
use App\Models\Lowongan;

$p = Pelamar::where('nama', 'like', '%Mahendra%')->first();
if ($p) {
    echo "ID: {$p->id}\n";
    echo "Nama: {$p->nama}\n";
    echo "Created At: {$p->created_at}\n";
    echo "Pendidikan S1: {$p->jenjang} | {$p->institusi} | {$p->prodi_pendidikan}\n";
    echo "Pendidikan S2: {$p->jenjang_2} | {$p->institusi_2} | {$p->prodi_pendidikan_2}\n";
    
    $lamarans = Lamaran::where('pelamar_id', $p->id)->get();
    echo "Lamaran count: " . $lamarans->count() . "\n";
} else {
    echo "Mahendra tidak ditemukan di DB pelamars\n";
}
