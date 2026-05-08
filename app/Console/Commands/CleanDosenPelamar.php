<?php

namespace App\Console\Commands;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Console\Command;

class CleanDosenPelamar extends Command
{
    protected $signature = 'dosen:clean-pelamar';
    protected $description = 'Hapus akun user pelamar yang sebenarnya adalah dosen (hasil import lama yang salah)';

    public function handle()
    {
        $emails = Dosen::pluck('email')->toArray();
        $count = User::where('role', 'pelamar')->whereIn('email', $emails)->delete();
        $this->info("Berhasil menghapus {$count} akun pelamar yang salah role.");
    }
}
