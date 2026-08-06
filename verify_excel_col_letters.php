<?php
use Maatwebsite\Excel\Facades\Excel;

$targetFile = base_path('batch7.xlsx');
$data = Excel::toArray(new stdClass(), $targetFile);
$rows = $data[0] ?? [];
$headers = $rows[0] ?? [];

function getExcelColName($num) {
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return getExcelColName($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

echo "=== MAPPING EXCEL COLUMN LETTERS TO HEADERS ===\n";
foreach ($headers as $idx => $title) {
    $letter = getExcelColName($idx);
    if (str_contains($letter, 'AH') || str_contains($letter, 'AI') || str_contains($letter, 'AJ') || str_contains($letter, 'AP') || str_contains($letter, 'AQ') || str_contains($letter, 'AR') || str_contains(strtolower($title), 'perguruan') || str_contains(strtolower($title), 'ijazah') || str_contains(strtolower($title), 'scan')) {
        echo "Col [$idx] (Excel Column $letter) => '$title'\n";
    }
}
