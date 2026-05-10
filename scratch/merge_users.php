<?php

use App\Models\User;
use App\Models\Dosen;

// Load Laravel - Correct path
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting User merge...\n";

$dosens = Dosen::where('is_kaprodi', true)->where('is_penguji', true)->get();

foreach ($dosens as $dosen) {
    $users = User::where('dosen_id', $dosen->id)->get();
    
    if ($users->count() > 1) {
        $kaprodiUser = $users->where('role', 'kaprodi')->first();
        $pengujiUser = $users->where('role', 'penguji')->first();
        
        if ($kaprodiUser && $pengujiUser) {
            $kaprodiUser->update([
                'secondary_email' => $pengujiUser->email
            ]);
            $pengujiUser->delete();
            echo "Merged users for {$dosen->nama}\n";
        }
    }
}

echo "Done.\n";
