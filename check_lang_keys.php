<?php
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PelamarImport;

$arr = Excel::toArray(new PelamarImport, 'Book1.xlsx');
$keys = array_keys($arr[0][0]);

echo "=== ALL KEYS IN BOOK1.XLSX ===\n";
foreach ($keys as $k) {
    if (str_contains(strtolower($k), 'bahasa') || str_contains(strtolower($k), 'inggris') || str_contains(strtolower($k), 'tes') || str_contains(strtolower($k), 'toefl')) {
        echo "  KEY: $k\n";
        echo "  SAMPLE VALS:\n";
        for ($i = 0; $i < 5; $i++) {
            echo "    Row " . ($i+2) . ": " . var_export($arr[0][$i][$k] ?? null, true) . "\n";
        }
    }
}
