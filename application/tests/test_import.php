<?php
define('BASEPATH', 'dummy');
require_once __DIR__ . '/../libraries/Import_service.php';

$service = new Import_service();
$filePath = 'C:/Users/itsme/Downloads/data_mikro_560_2026_id_umkm_setelah_nomor.xlsx';
$res = $service->parse_file($filePath, 'data_mikro_560_2026_id_umkm_setelah_nomor.xlsx');

echo "Success: " . ($res['success'] ? 'YES' : 'NO') . PHP_EOL;
echo "Total Parsed: " . $res['total_parsed'] . PHP_EOL;
echo "Header Row: " . $res['header_row'] . PHP_EOL;
echo "Skipped Empty: " . $res['skipped_empty'] . PHP_EOL;
echo "Count: " . count($res['records']) . PHP_EOL;
echo "Record 559: " . json_encode($res['records'][559], JSON_PRETTY_PRINT) . PHP_EOL;
if (isset($res['records'][560])) {
    echo "Record 560: " . json_encode($res['records'][560], JSON_PRETTY_PRINT) . PHP_EOL;
}
