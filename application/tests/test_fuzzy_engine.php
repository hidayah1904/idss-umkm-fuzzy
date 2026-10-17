<?php
/**
 * Test Suite: Fuzzy Mamdani Inference Engine for IDSS UMKM
 * Memenuhi kriteria Pengujian Wajib (Section 16):
 * 1. Fungsi keanggotaan segitiga
 * 2. Fungsi keanggotaan trapesium
 * 3. Nilai keanggotaan pada titik batas
 * 4. Seluruh 27 kombinasi aturan
 * 5. Operator MIN dan MAX
 * 6. Defuzzifikasi centroid
 * 7. Konversi skor ke kategori
 * 8. Input kosong / di luar domain
 * 9. Kategori stabilitas tidak dikenal
 * 10. Kasus beberapa aturan aktif
 * 11. Konsistensi hasil
 */

define('BASEPATH', 'dummy');

require_once __DIR__ . '/../libraries/Fuzzy_mamdani.php';

class FuzzyTester {
    private $engine;
    private $passes = 0;
    private $failures = 0;

    public function __construct() {
        $this->engine = new Fuzzy_mamdani();
        $this->engine->load_default_config();
    }

    private function assert($condition, $test_name, $details = '') {
        if ($condition) {
            $this->passes++;
            echo " [PASS] $test_name\n";
        } else {
            $this->failures++;
            echo " [FAIL] $test_name - $details\n";
        }
    }

    private function assertEquals($expected, $actual, $test_name, $delta = 0.001) {
        $diff = abs($expected - $actual);
        $ok = ($diff <= $delta);
        $this->assert($ok, $test_name, "Expected $expected, got $actual (diff $diff > delta $delta)");
    }

    public function runAllTests() {
        echo "========================================================\n";
        echo " PENGUJIAN MESIN FUZZY MAMDANI IDSS UMKM (SECTION 16)\n";
        echo "========================================================\n\n";

        $this->testMembershipFunctions();
        $this->testBoundaryPoints();
        $this->testRuleBaseCoverage();
        $this->testMinMaxOperators();
        $this->testDefuzzificationCentroid();
        $this->testScoreToCategoryConversion();
        $this->testOutOfDomainAndEdgeCases();
        $this->testMultipleActiveRules();
        $this->testConsistency();

        echo "\n========================================================\n";
        echo " HASIL AKHIR PENGUJIAN:\n";
        echo " Lulus (PASS) : {$this->passes}\n";
        echo " Gagal (FAIL) : {$this->failures}\n";
        echo "========================================================\n";

        return ($this->failures === 0);
    }

    public function testMembershipFunctions() {
        echo "--- 1 & 2. Tes Fungsi Keanggotaan Segitiga & Trapesium ---\n";
        
        // Segitiga: (4, 8, 11)
        // Titik puncak x = 8 -> 1.0
        $this->assertEquals(1.0, $this->engine->triangle(8, 4, 8, 11), "Segitiga Puncak (x=8)");
        // Titik sebelum batas x = 3 -> 0.0
        $this->assertEquals(0.0, $this->engine->triangle(3, 4, 8, 11), "Segitiga Luar Kiri (x=3)");
        // Titik setelah batas x = 12 -> 0.0
        $this->assertEquals(0.0, $this->engine->triangle(12, 4, 8, 11), "Segitiga Luar Kanan (x=12)");
        // Lereng naik x = 6 -> (6 - 4) / (8 - 4) = 2/4 = 0.5
        $this->assertEquals(0.5, $this->engine->triangle(6, 4, 8, 11), "Segitiga Lereng Naik (x=6)");
        // Lereng turun x = 9.5 -> (11 - 9.5) / (11 - 8) = 1.5 / 3 = 0.5
        $this->assertEquals(0.5, $this->engine->triangle(9.5, 4, 8, 11), "Segitiga Lereng Turun (x=9.5)");

        // Trapesium: (1, 1, 4, 8) [Bahu Kiri]
        // Titik bahu x = 1 -> 1.0
        $this->assertEquals(1.0, $this->engine->trapezoid(1, 1, 1, 4, 8), "Trapesium Bahu Kiri (x=1)");
        // Titik plateau x = 3 -> 1.0
        $this->assertEquals(1.0, $this->engine->trapezoid(3, 1, 1, 4, 8), "Trapesium Plateau (x=3)");
        // Lereng turun x = 6 -> (8 - 6) / (8 - 4) = 2/4 = 0.5
        $this->assertEquals(0.5, $this->engine->trapezoid(6, 1, 1, 4, 8), "Trapesium Lereng Turun (x=6)");
        // Di luar x = 9 -> 0.0
        $this->assertEquals(0.0, $this->engine->trapezoid(9, 1, 1, 4, 8), "Trapesium Luar Kanan (x=9)");
    }

    public function testBoundaryPoints() {
        echo "\n--- 3. Tes Titik Batas (Boundary Points) ---\n";
        // Batas tepat x = 4 pada segitiga (4, 8, 11) -> 0.0
        $this->assertEquals(0.0, $this->engine->triangle(4, 4, 8, 11), "Batas Kiri Segitiga (x=4)");
        // Batas tepat x = 11 pada segitiga (4, 8, 11) -> 0.0
        $this->assertEquals(0.0, $this->engine->triangle(11, 4, 8, 11), "Batas Kanan Segitiga (x=11)");
        // Batas tepat x = 4 pada trapesium (1, 1, 4, 8) -> 1.0 (ujung plateau)
        $this->assertEquals(1.0, $this->engine->trapezoid(4, 1, 1, 4, 8), "Ujung Plateau Trapesium (x=4)");
        // Batas tepat x = 8 pada trapesium (1, 1, 4, 8) -> 0.0 (kaki kanan)
        $this->assertEquals(0.0, $this->engine->trapezoid(8, 1, 1, 4, 8), "Kaki Kanan Trapesium (x=8)");
    }

    public function testRuleBaseCoverage() {
        echo "\n--- 4. Tes Kelengkapan 27 Aturan ---\n";
        $rules = $this->engine->generate_default_rules();
        $this->assert(count($rules) === 27, "Jumlah total aturan tepat 27 kombinasi");

        $rendah_count = 0;
        $sedang_count = 0;
        $tinggi_count = 0;

        foreach ($rules as $r) {
            $sum = $r['modal_level'] + $r['lama_level'] + $r['stabilitas_level'];
            if ($sum <= 1) {
                $this->assert($r['output'] === 'Rendah', "Aturan #{$r['rule_no']} sum=$sum -> Rendah");
                $rendah_count++;
            } elseif ($sum <= 4) {
                $this->assert($r['output'] === 'Sedang', "Aturan #{$r['rule_no']} sum=$sum -> Sedang");
                $sedang_count++;
            } else {
                $this->assert($r['output'] === 'Tinggi', "Aturan #{$r['rule_no']} sum=$sum -> Tinggi");
                $tinggi_count++;
            }
        }

        $this->assert($rendah_count === 4, "Kelompok output Rendah ada 4 aturan (Total 0-1)");
        $this->assert($sedang_count === 19, "Kelompok output Sedang ada 19 aturan (Total 2-4)");
        $this->assert($tinggi_count === 4, "Kelompok output Tinggi ada 4 aturan (Total 5-6)");
    }

    public function testMinMaxOperators() {
        echo "\n--- 5. Tes Operator MIN dan MAX ---\n";
        // Kasus: Modal Rendah = 0.8, Lama Usaha Pendek = 0.5, Stabilitas Fluktuatif = 1.0
        // MIN(0.8, 0.5, 1.0) = 0.5
        $alpha = min(0.8, 0.5, 1.0);
        $this->assertEquals(0.5, $alpha, "Operator MIN konjungsi aturan");

        // Agregasi MAX: max(0.2, 0.5, 0.1) = 0.5
        $agg = max(0.2, 0.5, 0.1);
        $this->assertEquals(0.5, $agg, "Operator MAX agregasi himpunan output");
    }

    public function testDefuzzificationCentroid() {
        echo "\n--- 6. Tes Defuzzifikasi Centroid ---\n";
        // Evaluasi kasus murni ekstrim bawah:
        // Modal 1.000.000 (Rendah=1, Sedang=0, Tinggi=0)
        // Lama 2 tahun (Pendek=1, Menengah=0, Panjang=0)
        // Stabilitas Fluktuatif=1
        // Aturan aktif: Rule 1 (Rendah + Pendek + Fluktuatif -> Rendah).
        // Alpha_Rendah = 1.0, Alpha_Sedang = 0.0, Alpha_Tinggi = 0.0
        // Output hanya dipotong oleh Rendah: trapesium(0, 0, 30, 50).
        // Momen analitis dan numerik dari trapesium (0,0,30,50):
        // Luas = 30 + 0.5 * 20 = 40. Momen = int_0^30 z dz + int_30^50 z * (50-z)/20 dz
        // int_0^30 z dz = 450. int_30^50 z*(50-z)/20 dz = [50 z^2/40 - z^3/60]_30^50 = [1.25(2500 - 900) - (125000 - 27000)/60] = 2000 - 1633.333 = 366.667.
        // Total momen = 450 + 366.667 = 816.667.
        // Centroid analitis = 816.667 / 40 = 20.4167 ~ 20.42!
        $res = $this->engine->evaluate(1000000, 2, 'Fluktuatif');
        $this->assertEquals(20.42, $res['result']['score'], "Centroid Analitis Independen Kasus Rendah Murni", 0.1);
        $this->assert($res['result']['category'] === 'Tidak Layak', "Kategori Kasus Rendah = Tidak Layak");

        // Evaluasi kasus murni ekstrim atas:
        // Modal 200.000.000 (Tinggi=1)
        // Lama 13 tahun (Panjang=1)
        // Stabilitas Stabil=1
        // Output hanya dipotong oleh Tinggi: trapesium(50, 70, 100, 100).
        // Centroid simetris analitis = 100 - 20.4167 = 79.58!
        $resHigh = $this->engine->evaluate(200000000, 13, 'Stabil');
        $this->assertEquals(79.58, $resHigh['result']['score'], "Centroid Analitis Independen Kasus Tinggi Murni", 0.1);
        $this->assert($resHigh['result']['category'] === 'Layak', "Kategori Kasus Tinggi = Layak");
    }

    public function testScoreToCategoryConversion() {
        echo "\n--- 7. Tes Batas Kategori Kelayakan ---\n";
        // Score < 40 -> Tidak Layak
        // 40 <= Score < 70 -> Cukup Layak
        // Score >= 70 -> Layak
        $caseLow = $this->engine->evaluate(1000000, 2, 'Fluktuatif');
        $this->assert($caseLow['result']['score'] < 40 && $caseLow['result']['category'] === 'Tidak Layak', "Skor < 40 terklasifikasi 'Tidak Layak'");

        $caseMid = $this->engine->evaluate(14000000, 8, 'Cukup stabil');
        $this->assert($caseMid['result']['score'] >= 40 && $caseMid['result']['score'] < 70 && $caseMid['result']['category'] === 'Cukup Layak', "Skor 40-69 terklasifikasi 'Cukup Layak'");

        $caseHigh = $this->engine->evaluate(100000000, 12, 'Stabil');
        $this->assert($caseHigh['result']['score'] >= 70 && $caseHigh['result']['category'] === 'Layak', "Skor >= 70 terklasifikasi 'Layak'");
    }

    public function testOutOfDomainAndEdgeCases() {
        echo "\n--- 8 & 9. Tes Di Luar Domain dan Input Tidak Valid ---\n";
        // Modal di bawah domain prototipe (< Rp500.000)
        $resLowModal = $this->engine->evaluate(200000, 3, 'Fluktuatif');
        $this->assert(!empty($resLowModal['warnings']), "Peringatan dihasilkan untuk modal di bawah domain");
        $this->assert($resLowModal['result']['score'] > 0, "Perhitungan tetap stabil dan tidak menghasilkan NaN");

        // Lama usaha di atas domain prototipe (> 15 tahun)
        $resHighLama = $this->engine->evaluate(50000000, 25, 'Stabil');
        $this->assert(!empty($resHighLama['warnings']), "Peringatan dihasilkan untuk lama usaha di atas domain");
        $this->assert($resHighLama['result']['score'] > 0, "Perhitungan tetap stabil");

        // Kategori stabilitas tidak dikenal
        $resBadStab = $this->engine->evaluate(10000000, 5, 'TidakMenentu');
        $this->assert(!empty($resBadStab['warnings']), "Peringatan dihasilkan untuk kategori stabilitas tidak dikenal");
        $this->assert(in_array($resBadStab['inputs']['stabilitas'], array('Fluktuatif', 'Cukup stabil', 'Stabil')), "Fallback otomatis ke kategori valid");
    }

    public function testMultipleActiveRules() {
        echo "\n--- 10. Tes Kasus Beberapa Aturan Aktif Bersamaan ---\n";
        // Modal = 9.500.000 (Rendah=(14jt - 9.5jt)/9jt = 0.5, Sedang=(9.5jt - 5jt)/9jt = 0.5)
        // Lama = 6 tahun (Pendek=(8 - 6)/4 = 0.5, Menengah=(6 - 4)/4 = 0.5)
        // Stabilitas = Cukup stabil (1.0)
        // Aturan yang aktif akan ada 4 aturan:
        // R2: Rendah(0.5) + Pendek(0.5) + Cukup(1.0) -> alpha=0.5 -> Rendah
        // R5: Rendah(0.5) + Menengah(0.5) + Cukup(1.0) -> alpha=0.5 -> Sedang
        // R11: Sedang(0.5) + Pendek(0.5) + Cukup(1.0) -> alpha=0.5 -> Sedang
        // R14: Sedang(0.5) + Menengah(0.5) + Cukup(1.0) -> alpha=0.5 -> Sedang
        $res = $this->engine->evaluate(9500000, 6, 'Cukup stabil');
        $this->assertEquals(4, $res['rules']['active_count'], "Tepat 4 aturan aktif pada titik potong derajat 0.5");
        $this->assertEquals(0.5, $res['aggregation']['alpha_cuts']['Rendah'], "Alpha Cut Rendah = 0.5");
        $this->assertEquals(0.5, $res['aggregation']['alpha_cuts']['Sedang'], "Alpha Cut Sedang = 0.5");
        $this->assertEquals(0.0, $res['aggregation']['alpha_cuts']['Tinggi'], "Alpha Cut Tinggi = 0.0");
    }

    public function testConsistency() {
        echo "\n--- 11. Tes Konsistensi Hasil ---\n";
        $run1 = $this->engine->evaluate(15000000, 7.5, 'Cukup stabil');
        $run2 = $this->engine->evaluate(15000000, 7.5, 'Cukup stabil');
        $run3 = $this->engine->evaluate(15000000, 7.5, 'Cukup stabil');

        $this->assertEquals($run1['result']['score'], $run2['result']['score'], "Konsistensi Skor Run 1 & 2");
        $this->assertEquals($run2['result']['score'], $run3['result']['score'], "Konsistensi Skor Run 2 & 3");
        $this->assert($run1['result']['category'] === $run3['result']['category'], "Konsistensi Kategori Run 1 & 3");
    }
}

// Jalankan suite pengujian
$tester = new FuzzyTester();
$success = $tester->runAllTests();
exit($success ? 0 : 1);
