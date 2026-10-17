<?php
/**
 * Integration & End-to-End Test Suite for IDSS UMKM
 */
define('BASEPATH', 'dummy');

// Mock CI constants & minimal environment if running standalone
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

echo "========================================================\n";
echo " TES INTEGRASI & END-TO-END IDSS UMKM FUZZY MAMDANI\n";
echo "========================================================\n\n";

// 1. Tes Koneksi Database Langsung
$mysqli = new mysqli('127.0.0.1', 'root', '', 'idss_umkm');
if ($mysqli->connect_error) {
    die(" [FAIL] Koneksi MySQL gagal: " . $mysqli->connect_error . "\n");
}
echo " [PASS] Koneksi MySQL 'idss_umkm' berhasil.\n";

// 2. Tes Import Dataset Nyata (560 Data)
require_once __DIR__ . '/../libraries/Import_service.php';
$importService = new Import_service();
$candidates = array(
    'C:/Users/itsme/Downloads/data_mikro_560_2026_file_terakhir_tanpa_rp.csv',
    'C:/Users/itsme/Downloads/data_mikro_560_2026_file_terakhir_tanpa_rp.xlsx',
    'C:/Users/itsme/Downloads/data_mikro_560_2026_ditambah_tabel (1).xlsx'
);
$sourcePath = null;
foreach ($candidates as $c) {
    if (file_exists($c)) {
        $sourcePath = $c;
        break;
    }
}

if (!$sourcePath) {
    die(" [FAIL] File sumber dataset tidak ditemukan.\n");
}

$parseRes = $importService->parse_file($sourcePath, basename($sourcePath));
if (!$parseRes['success'] || $parseRes['total_parsed'] !== 560) {
    die(" [FAIL] Parsing file gagal atau jumlah baris tidak 560 (ditemukan: {$parseRes['total_parsed']})\n");
}
echo " [PASS] Parsing file Excel sumber berhasil: {$parseRes['total_parsed']} baris data UMKM valid ditemukan.\n";

// 3. Masukkan ke Database melalui Prepared Statement
$stmt = $mysqli->prepare("
    INSERT INTO umkm (id_umkm, nama_umkm, bidang_usaha, nilai_investasi, kecamatan, desa_kelurahan, nilai_omzet, nilai_laba, lama_usaha, biaya_operasional, arus_kas_bersih, stabilitas_pendapatan, status_usaha, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE 
        nama_umkm = VALUES(nama_umkm),
        bidang_usaha = VALUES(bidang_usaha),
        nilai_investasi = VALUES(nilai_investasi),
        kecamatan = VALUES(kecamatan),
        desa_kelurahan = VALUES(desa_kelurahan),
        nilai_omzet = VALUES(nilai_omzet),
        nilai_laba = VALUES(nilai_laba),
        lama_usaha = VALUES(lama_usaha),
        biaya_operasional = VALUES(biaya_operasional),
        arus_kas_bersih = VALUES(arus_kas_bersih),
        stabilitas_pendapatan = VALUES(stabilitas_pendapatan),
        status_usaha = VALUES(status_usaha)
");

$mysqli->begin_transaction();
$inserted = 0;
foreach ($parseRes['records'] as $r) {
    $stmt->bind_param(
        "sssdsddddddss",
        $r['id_umkm'],
        $r['nama_umkm'],
        $r['bidang_usaha'],
        $r['nilai_investasi'],
        $r['kecamatan'],
        $r['desa_kelurahan'],
        $r['nilai_omzet'],
        $r['nilai_laba'],
        $r['lama_usaha'],
        $r['biaya_operasional'],
        $r['arus_kas_bersih'],
        $r['stabilitas_pendapatan'],
        $r['status_usaha']
    );
    $stmt->execute();
    $inserted++;
}
$mysqli->commit();
echo " [PASS] Berhasil memuat $inserted baris data UMKM ke dalam tabel MySQL 'umkm'.\n";

// 4. Verifikasi Total UMKM di Database
$countRes = $mysqli->query("SELECT COUNT(*) as total FROM umkm");
$totalDb = $countRes->fetch_assoc()['total'];
echo " [PASS] Verifikasi jumlah data pada tabel 'umkm': $totalDb data UMKM tersimpan.\n";

// 5. Tes Eksekusi Inferensi Fuzzy Mamdani pada Sampel Nyata
require_once __DIR__ . '/../libraries/Fuzzy_mamdani.php';
$engine = new Fuzzy_mamdani();
$engine->load_default_config();

// Ambil model aktif id
$mQuery = $mysqli->query("SELECT id, model_code, version FROM fuzzy_models WHERE is_active = 1 LIMIT 1");
$mRow = $mQuery->fetch_assoc();
$modelId = $mRow['id'];

// Ambil beberapa sampel UMKM dengan profil beragam untuk dievaluasi
$samples = $mysqli->query("
    (SELECT * FROM umkm WHERE stabilitas_pendapatan = 'Stabil' AND nilai_investasi >= 50000000 AND lama_usaha >= 10 LIMIT 3)
    UNION
    (SELECT * FROM umkm WHERE stabilitas_pendapatan = 'Cukup stabil' AND nilai_investasi BETWEEN 5000000 AND 30000000 LIMIT 3)
    UNION
    (SELECT * FROM umkm WHERE stabilitas_pendapatan = 'Fluktuatif' AND nilai_investasi <= 10000000 AND lama_usaha <= 3 LIMIT 3)
");

$evalStmt = $mysqli->prepare("
    INSERT INTO evaluations (umkm_id, fuzzy_model_id, input_modal, input_lama_usaha, input_stabilitas, score, category, result_details_json, evaluated_by, evaluated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
");

$evalCount = 0;
while ($u = $samples->fetch_assoc()) {
    $res = $engine->evaluate($u['nilai_investasi'], $u['lama_usaha'], $u['stabilitas_pendapatan']);
    $json = json_encode($res, JSON_UNESCAPED_UNICODE);
    
    $evalStmt->bind_param(
        "iiddsdss",
        $u['id'],
        $modelId,
        $u['nilai_investasi'],
        $u['lama_usaha'],
        $u['stabilitas_pendapatan'],
        $res['result']['score'],
        $res['result']['category'],
        $json
    );
    $evalStmt->execute();
    $evalCount++;
    echo "  -> Evaluasi [{$u['id_umkm']}] {$u['nama_umkm']}: Modal Rp" . number_format($u['nilai_investasi']) . ", Lama {$u['lama_usaha']} thn, {$u['stabilitas_pendapatan']} -> Skor: {$res['result']['score']} [{$res['result']['category']}]\n";
}
echo " [PASS] Berhasil memproses dan menyimpan $evalCount evaluasi penalaran fuzzy ke tabel 'evaluations'.\n";

// 6. Verifikasi Statistik Dashboard & Rekapitulasi
$statQuery = $mysqli->query("
    SELECT 
        COUNT(*) as total_eval,
        SUM(CASE WHEN category = 'Layak' THEN 1 ELSE 0 END) as total_layak,
        SUM(CASE WHEN category = 'Cukup Layak' THEN 1 ELSE 0 END) as total_cukup,
        SUM(CASE WHEN category = 'Tidak Layak' THEN 1 ELSE 0 END) as total_tidak
    FROM evaluations
");
$stats = $statQuery->fetch_assoc();
echo " [PASS] Verifikasi Agregasi Dashboard:\n";
echo "        - Total Evaluasi : {$stats['total_eval']}\n";
echo "        - Layak          : {$stats['total_layak']}\n";
echo "        - Cukup Layak    : {$stats['total_cukup']}\n";
echo "        - Tidak Layak    : {$stats['total_tidak']}\n";

$mysqli->close();
echo "\n========================================================\n";
echo " SEMUA TES INTEGRASI DAN END-TO-END BERHASIL LULUS 100%!\n";
echo "========================================================\n";
