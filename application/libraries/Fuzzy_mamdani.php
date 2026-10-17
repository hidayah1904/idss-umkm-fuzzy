<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fuzzy Mamdani Inference Engine for IDSS UMKM
 * Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM
 * 
 * Specifications:
 * - 3 Inputs: Modal/Investasi, Lama Usaha, Stabilitas Pendapatan
 * - 1 Output: Skor Kelayakan Usaha (0 - 100)
 * - 27 Explicit Rules (MIN-MAX composition)
 * - Defuzzification: Centroid (Center of Gravity)
 * - Output Classification: Tidak Layak (<40), Cukup Layak (40 - <70), Layak (70 - 100)
 */
class Fuzzy_mamdani {

    protected $CI;
    protected $config_data;
    protected $rule_base;
    protected $model_info;

    public function __construct($params = array())
    {
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
        if (!empty($params)) {
            $this->init($params);
        }
    }

    /**
     * Inisialisasi engine dengan konfigurasi dan basis aturan
     */
    public function init($params = array())
    {
        if (isset($params['model'])) {
            $this->model_info = $params['model'];
            $this->config_data = is_array($params['model']['configuration_json']) 
                ? $params['model']['configuration_json'] 
                : json_decode($params['model']['configuration_json'], true);
            $this->rule_base = is_array($params['model']['rule_base_json']) 
                ? $params['model']['rule_base_json'] 
                : json_decode($params['model']['rule_base_json'], true);
        } else {
            $this->load_default_config();
        }
    }

    /**
     * Load default model configuration if not provided
     */
    public function load_default_config()
    {
        if (isset($this->CI->db)) {
            $query = $this->CI->db->get_where('fuzzy_models', array('is_active' => 1), 1);
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $this->model_info = $row;
                $this->config_data = json_decode($row['configuration_json'], true);
                $this->rule_base = json_decode($row['rule_base_json'], true);
                return;
            }
        }

        // Fallback default in-memory config if DB not ready
        $this->model_info = array(
            'id' => 1,
            'model_code' => 'MAMDANI-V1.0',
            'model_name' => 'Model Penalaran Fuzzy Mamdani Evaluasi Kelayakan Usaha UMKM v1.0',
            'version' => '1.0.0',
            'method' => 'Mamdani'
        );

        $this->config_data = array(
            'domains' => array(
                'modal' => array('min' => 500000, 'max' => 900000000),
                'lama_usaha' => array('min' => 1, 'max' => 15),
                'stabilitas' => array('values' => array('Fluktuatif', 'Cukup stabil', 'Stabil')),
                'output_skor' => array('min' => 0, 'max' => 100)
            ),
            'membership_functions' => array(
                'modal' => array(
                    'rendah' => array('type' => 'trapezoid', 'params' => array(500000, 500000, 5000000, 14000000)),
                    'sedang' => array('type' => 'triangle', 'params' => array(5000000, 14000000, 50000000)),
                    'tinggi' => array('type' => 'trapezoid', 'params' => array(14000000, 50000000, 900000000, 900000000))
                ),
                'lama_usaha' => array(
                    'pendek' => array('type' => 'trapezoid', 'params' => array(1, 1, 4, 8)),
                    'menengah' => array('type' => 'triangle', 'params' => array(4, 8, 11)),
                    'panjang' => array('type' => 'trapezoid', 'params' => array(8, 11, 15, 15))
                ),
                'output' => array(
                    'rendah' => array('type' => 'trapezoid', 'params' => array(0, 0, 30, 50)),
                    'sedang' => array('type' => 'triangle', 'params' => array(30, 50, 70)),
                    'tinggi' => array('type' => 'trapezoid', 'params' => array(50, 70, 100, 100))
                )
            ),
            'operators' => array(
                'and' => 'MIN',
                'implication' => 'MIN',
                'aggregation' => 'MAX',
                'defuzzification' => 'Centroid',
                'discretization_step' => 0.1
            ),
            'categories' => array(
                array('label' => 'Tidak Layak', 'min' => 0, 'max' => 40, 'color' => 'danger'),
                array('label' => 'Cukup Layak', 'min' => 40, 'max' => 70, 'color' => 'warning'),
                array('label' => 'Layak', 'min' => 70, 'max' => 100, 'color' => 'success')
            )
        );

        $this->rule_base = $this->generate_default_rules();
    }

    /**
     * Generate 27 default rules according to specification
     */
    public function generate_default_rules()
    {
        $modal_arr = array(
            array('name' => 'Rendah', 'lvl' => 0),
            array('name' => 'Sedang', 'lvl' => 1),
            array('name' => 'Tinggi', 'lvl' => 2)
        );
        $lama_arr = array(
            array('name' => 'Pendek', 'lvl' => 0),
            array('name' => 'Menengah', 'lvl' => 1),
            array('name' => 'Panjang', 'lvl' => 2)
        );
        $stab_arr = array(
            array('name' => 'Fluktuatif', 'lvl' => 0),
            array('name' => 'Cukup stabil', 'lvl' => 1),
            array('name' => 'Stabil', 'lvl' => 2)
        );

        $rules = array();
        $rule_no = 1;
        foreach ($modal_arr as $m) {
            foreach ($lama_arr as $l) {
                foreach ($stab_arr as $s) {
                    $total = $m['lvl'] + $l['lvl'] + $s['lvl'];
                    if ($total <= 1) {
                        $out = 'Rendah';
                    } elseif ($total <= 4) {
                        $out = 'Sedang';
                    } else {
                        $out = 'Tinggi';
                    }

                    $rules[] = array(
                        'rule_no' => $rule_no++,
                        'modal' => $m['name'],
                        'modal_level' => $m['lvl'],
                        'lama_usaha' => $l['name'],
                        'lama_level' => $l['lvl'],
                        'stabilitas' => $s['name'],
                        'stabilitas_level' => $s['lvl'],
                        'total_level' => $total,
                        'output' => $out
                    );
                }
            }
        }
        return $rules;
    }

    /**
     * Membership Function: Trapezoid (Trapesium)
     * Parameters: [a, b, c, d]
     */
    public function trapezoid($x, $a, $b, $c, $d)
    {
        $x = (float)$x;
        $a = (float)$a;
        $b = (float)$b;
        $c = (float)$c;
        $d = (float)$d;

        // Shoulder left (a == b)
        if ($a == $b && $x <= $a) {
            return 1.0;
        }

        // Shoulder right (c == d)
        if ($c == $d && $x >= $c) {
            return 1.0;
        }

        // Outside trapezoid boundaries
        if ($x <= $a || $x >= $d) {
            return 0.0;
        }

        // Plateau (flat top)
        if ($x >= $b && $x <= $c) {
            return 1.0;
        }

        // Left slope (ascending)
        if ($x > $a && $x < $b) {
            return ($b - $a) > 0 ? ($x - $a) / ($b - $a) : 1.0;
        }

        // Right slope (descending)
        if ($x > $c && $x < $d) {
            return ($d - $c) > 0 ? ($d - $x) / ($d - $c) : 1.0;
        }

        return 0.0;
    }

    /**
     * Membership Function: Triangle (Segitiga)
     * Parameters: [a, b, c]
     */
    public function triangle($x, $a, $b, $c)
    {
        $x = (float)$x;
        $a = (float)$a;
        $b = (float)$b;
        $c = (float)$c;

        if ($x <= $a || $x >= $c) {
            return 0.0;
        }

        if ($x == $b) {
            return 1.0;
        }

        if ($x > $a && $x < $b) {
            return ($b - $a) > 0 ? ($x - $a) / ($b - $a) : 0.0;
        }

        if ($x > $b && $x < $c) {
            return ($c - $b) > 0 ? ($c - $x) / ($c - $b) : 0.0;
        }

        return 0.0;
    }

    /**
     * Membership Function: Categorical Crisp Singleton
     */
    public function categorical($val, $target_val)
    {
        $val_norm = mb_strtolower(trim((string)$val));
        $target_norm = mb_strtolower(trim((string)$target_val));
        return ($val_norm === $target_norm) ? 1.0 : 0.0;
    }

    /**
     * Evaluasi kelayakan usaha menggunakan Fuzzy Mamdani
     * 
     * @param float $modal Nilai investasi/modal
     * @param float $lama_usaha Lama usaha dalam tahun
     * @param string $stabilitas Stabilitas pendapatan ('Fluktuatif', 'Cukup stabil', 'Stabil')
     * @return array Hasil inferensi lengkap dan diagnostik
     */
    public function evaluate($modal, $lama_usaha, $stabilitas)
    {
        if (empty($this->config_data) || empty($this->rule_base)) {
            $this->load_default_config();
        }

        $modal = (float)$modal;
        $lama_usaha = (float)$lama_usaha;
        $stabilitas = trim((string)$stabilitas);

        // 1. Validasi Input dan Peringatan Batas Domain
        $warnings = array();
        $domain_modal = isset($this->config_data['domains']['modal']) ? $this->config_data['domains']['modal'] : array('min' => 500000, 'max' => 900000000);
        $domain_lama = isset($this->config_data['domains']['lama_usaha']) ? $this->config_data['domains']['lama_usaha'] : array('min' => 1, 'max' => 15);
        $valid_stab = array('Fluktuatif', 'Cukup stabil', 'Stabil');

        if ($modal < $domain_modal['min'] || $modal > $domain_modal['max']) {
            $warnings[] = sprintf('Nilai modal (Rp%s) berada di luar domain prototipe (Rp%s - Rp%s). Fungsi keanggotaan bahu/trapesium mempertahankan nilai batas.',
                number_format($modal, 0, ',', '.'),
                number_format($domain_modal['min'], 0, ',', '.'),
                number_format($domain_modal['max'], 0, ',', '.')
            );
        }

        if ($lama_usaha < $domain_lama['min'] || $lama_usaha > $domain_lama['max']) {
            $warnings[] = sprintf('Lama usaha (%s tahun) berada di luar domain prototipe (%s - %s tahun). Fungsi keanggotaan bahu/trapesium mempertahankan nilai batas.',
                $lama_usaha, $domain_lama['min'], $domain_lama['max']
            );
        }

        $stab_found = false;
        foreach ($valid_stab as $vs) {
            if (strcasecmp($stabilitas, $vs) === 0) {
                $stabilitas = $vs; // Normalisasi penulisan
                $stab_found = true;
                break;
            }
        }
        if (!$stab_found) {
            $warnings[] = sprintf('Kategori stabilitas "%s" tidak dikenal. Valid: %s. Default diarahkan ke "Fluktuatif".',
                $stabilitas, implode(', ', $valid_stab)
            );
            $stabilitas = 'Fluktuatif';
        }

        // 2. Fuzzifikasi (Derajat Keanggotaan Setiap Input)
        $mf_modal = $this->config_data['membership_functions']['modal'];
        $mf_lama  = $this->config_data['membership_functions']['lama_usaha'];

        $mu_modal = array(
            'Rendah' => $this->trapezoid($modal, $mf_modal['rendah']['params'][0], $mf_modal['rendah']['params'][1], $mf_modal['rendah']['params'][2], $mf_modal['rendah']['params'][3]),
            'Sedang' => $this->triangle($modal, $mf_modal['sedang']['params'][0], $mf_modal['sedang']['params'][1], $mf_modal['sedang']['params'][2]),
            'Tinggi' => $this->trapezoid($modal, $mf_modal['tinggi']['params'][0], $mf_modal['tinggi']['params'][1], $mf_modal['tinggi']['params'][2], $mf_modal['tinggi']['params'][3])
        );

        $mu_lama = array(
            'Pendek'   => $this->trapezoid($lama_usaha, $mf_lama['pendek']['params'][0], $mf_lama['pendek']['params'][1], $mf_lama['pendek']['params'][2], $mf_lama['pendek']['params'][3]),
            'Menengah' => $this->triangle($lama_usaha, $mf_lama['menengah']['params'][0], $mf_lama['menengah']['params'][1], $mf_lama['menengah']['params'][2]),
            'Panjang'  => $this->trapezoid($lama_usaha, $mf_lama['panjang']['params'][0], $mf_lama['panjang']['params'][1], $mf_lama['panjang']['params'][2], $mf_lama['panjang']['params'][3])
        );

        $mu_stabilitas = array(
            'Fluktuatif'   => $this->categorical($stabilitas, 'Fluktuatif'),
            'Cukup stabil' => $this->categorical($stabilitas, 'Cukup stabil'),
            'Stabil'       => $this->categorical($stabilitas, 'Stabil')
        );

        // 3. Evaluasi Basis Aturan (Rule Base Evaluation) - 27 Aturan
        $rules_evaluated = array();
        $active_rules = array();
        $alpha_cuts = array(
            'Rendah' => 0.0,
            'Sedang' => 0.0,
            'Tinggi' => 0.0
        );

        foreach ($this->rule_base as $rule) {
            $r_no = $rule['rule_no'];
            $r_modal = $rule['modal'];
            $r_lama  = $rule['lama_usaha'];
            $r_stab  = $rule['stabilitas'];
            $r_out   = $rule['output'];

            $mu_m = isset($mu_modal[$r_modal]) ? $mu_modal[$r_modal] : 0.0;
            $mu_l = isset($mu_lama[$r_lama]) ? $mu_lama[$r_lama] : 0.0;
            $mu_s = isset($mu_stabilitas[$r_stab]) ? $mu_stabilitas[$r_stab] : 0.0;

            // Operator AND: MIN
            $alpha = min($mu_m, $mu_l, $mu_s);

            $rule_item = array(
                'rule_no' => $r_no,
                'modal' => $r_modal,
                'lama_usaha' => $r_lama,
                'stabilitas' => $r_stab,
                'output' => $r_out,
                'mu_modal' => round($mu_m, 4),
                'mu_lama_usaha' => round($mu_l, 4),
                'mu_stabilitas' => round($mu_s, 4),
                'alpha' => round($alpha, 4),
                'is_active' => ($alpha > 0),
                'rule_text' => sprintf('R%02d: JIKA Modal [%s (%.2f)] DAN Lama Usaha [%s (%.2f)] DAN Stabilitas [%s (%.2f)] MAKA Kelayakan [%s] -> α = %.4f',
                    $r_no, $r_modal, $mu_m, $r_lama, $mu_l, $r_stab, $mu_s, $r_out, $alpha
                )
            );

            $rules_evaluated[] = $rule_item;

            if ($alpha > 0) {
                $active_rules[] = $rule_item;
            }

            // Agregasi MAX per kelompok output
            if ($alpha > $alpha_cuts[$r_out]) {
                $alpha_cuts[$r_out] = $alpha;
            }
        }

        // 4. Implikasi & Agregasi (MAX-MIN)
        $mf_out = $this->config_data['membership_functions']['output'];
        $step = isset($this->config_data['operators']['discretization_step']) 
            ? (float)$this->config_data['operators']['discretization_step'] 
            : 0.1;

        if ($step <= 0) $step = 0.1;

        // 5. Defuzzifikasi Centroid (Center of Gravity)
        $sum_moment = 0.0;
        $sum_area = 0.0;

        // Kurva agregasi untuk Chart.js (sampel setiap interval 1 untuk ringkas & visual tajam)
        $chart_samples = array();
        $sample_interval = 1.0;

        $z_start = 0.0;
        $z_end = 100.0;
        $total_points = (int)round(($z_end - $z_start) / $step) + 1;

        for ($i = 0; $i < $total_points; $i++) {
            $z = $z_start + ($i * $step);
            if ($z > $z_end) $z = $z_end;

            // Hitung derajat keanggotaan output konsekuen
            $mu_out_rendah = $this->trapezoid($z, $mf_out['rendah']['params'][0], $mf_out['rendah']['params'][1], $mf_out['rendah']['params'][2], $mf_out['rendah']['params'][3]);
            $mu_out_sedang = $this->triangle($z, $mf_out['sedang']['params'][0], $mf_out['sedang']['params'][1], $mf_out['sedang']['params'][2]);
            $mu_out_tinggi = $this->trapezoid($z, $mf_out['tinggi']['params'][0], $mf_out['tinggi']['params'][1], $mf_out['tinggi']['params'][2], $mf_out['tinggi']['params'][3]);

            // Implikasi MIN: min(alpha_cut, mu_out)
            $cut_rendah = min($alpha_cuts['Rendah'], $mu_out_rendah);
            $cut_sedang = min($alpha_cuts['Sedang'], $mu_out_sedang);
            $cut_tinggi = min($alpha_cuts['Tinggi'], $mu_out_tinggi);

            // Agregasi MAX: max(cut_rendah, cut_sedang, cut_tinggi)
            $mu_aggregated = max($cut_rendah, $cut_sedang, $cut_tinggi);

            // Integral momen & luas (metode numerik penjumlahan partisi diskret)
            $sum_moment += ($z * $mu_aggregated * $step);
            $sum_area += ($mu_aggregated * $step);

            // Simpan poin untuk chart jika z mendekati bilangan bulat
            if (abs($z - round($z)) < 0.0001) {
                $chart_samples[] = array(
                    'z' => (float)$z,
                    'mu_aggregated' => round($mu_aggregated, 4),
                    'cut_rendah' => round($cut_rendah, 4),
                    'cut_sedang' => round($cut_sedang, 4),
                    'cut_tinggi' => round($cut_tinggi, 4)
                );
            }
        }

        // Skor Centroid
        if ($sum_area > 0) {
            $score = $sum_moment / $sum_area;
        } else {
            // Jika tidak ada aturan aktif sama sekali
            $score = 50.0;
            $warnings[] = 'Tidak ada aturan yang aktif (derajat aktivasi 0). Skor dialihkan ke titik tengah.';
        }

        $score = round($score, 2);
        // Pastikan skor berada pada rentang domain 0 - 100
        $score = max(0.0, min(100.0, $score));

        // 6. Penentuan Kategori Kelayakan
        $category = 'Tidak Layak';
        $badge_class = 'badge-danger';
        $category_color = '#EF4444';

        if ($score < 40.0) {
            $category = 'Tidak Layak';
            $badge_class = 'badge-danger';
            $category_color = '#EF4444';
        } elseif ($score < 70.0) {
            $category = 'Cukup Layak';
            $badge_class = 'badge-warning';
            $category_color = '#F59E0B';
        } else {
            $category = 'Layak';
            $badge_class = 'badge-success';
            $category_color = '#10B981';
        }

        // 7. Interpretasi Hasil Evaluasi
        $interpretation = $this->generate_interpretation($score, $category, $modal, $lama_usaha, $stabilitas, $alpha_cuts, $active_rules);

        return array(
            'success' => true,
            'model' => array(
                'id' => isset($this->model_info['id']) ? $this->model_info['id'] : 1,
                'code' => isset($this->model_info['model_code']) ? $this->model_info['model_code'] : 'MAMDANI-V1.0',
                'name' => isset($this->model_info['model_name']) ? $this->model_info['model_name'] : 'Model Fuzzy Mamdani v1.0',
                'version' => isset($this->model_info['version']) ? $this->model_info['version'] : '1.0.0',
                'method' => 'Fuzzy Mamdani (MIN-MAX-Centroid)'
            ),
            'inputs' => array(
                'modal' => $modal,
                'lama_usaha' => $lama_usaha,
                'stabilitas' => $stabilitas
            ),
            'fuzzification' => array(
                'modal' => array_map(function($v) { return round($v, 4); }, $mu_modal),
                'lama_usaha' => array_map(function($v) { return round($v, 4); }, $mu_lama),
                'stabilitas' => array_map(function($v) { return round($v, 4); }, $mu_stabilitas)
            ),
            'rules' => array(
                'total' => count($rules_evaluated),
                'all' => $rules_evaluated,
                'active' => $active_rules,
                'active_count' => count($active_rules)
            ),
            'aggregation' => array(
                'alpha_cuts' => array(
                    'Rendah' => round($alpha_cuts['Rendah'], 4),
                    'Sedang' => round($alpha_cuts['Sedang'], 4),
                    'Tinggi' => round($alpha_cuts['Tinggi'], 4)
                ),
                'sum_moment' => round($sum_moment, 4),
                'sum_area' => round($sum_area, 4),
                'chart_samples' => $chart_samples
            ),
            'result' => array(
                'score' => $score,
                'category' => $category,
                'badge_class' => $badge_class,
                'category_color' => $category_color,
                'interpretation' => $interpretation
            ),
            'warnings' => $warnings,
            'timestamp' => date('Y-m-d H:i:s')
        );
    }

    /**
     * Membangun narasi interpretasi hasil penalaran fuzzy yang transparan
     */
    protected function generate_interpretation($score, $category, $modal, $lama, $stab, $alpha_cuts, $active_rules)
    {
        $active_names = array();
        foreach ($active_rules as $ar) {
            $active_names[] = sprintf('R%d (α=%.2f, Konsekuen: %s)', $ar['rule_no'], $ar['alpha'], $ar['output']);
        }
        $rules_str = !empty($active_names) ? implode(', ', $active_names) : 'Tidak ada aturan dominan';

        $notes = sprintf(
            'Usaha dievaluasi dengan modal Rp%s, masa operasional %s tahun, dan pendapatan %s. ' .
            'Hasil penalaran menghasilkan aktivasi kekuatan potong: Rendah = %.2f, Sedang = %.2f, Tinggi = %.2f. ' .
            'Terdapat %d aturan aktif (%s). Melalui defuzzifikasi Centroid, diperoleh indeks kelayakan %.2f dari skala 0–100 sehingga masuk ke dalam kategori "%s".',
            number_format($modal, 0, ',', '.'),
            $lama,
            $stab,
            $alpha_cuts['Rendah'],
            $alpha_cuts['Sedang'],
            $alpha_cuts['Tinggi'],
            count($active_rules),
            $rules_str,
            $score,
            $category
        );

        if ($category === 'Layak') {
            $notes .= ' Rekomendasi: Usaha menunjukkan profil stabilitas, modal, dan durasi operasional yang kuat untuk dipertimbangkan dalam pembinaan atau fasilitasi pembiayaan usaha.';
        } elseif ($category === 'Cukup Layak') {
            $notes .= ' Rekomendasi: Usaha memiliki prospek namun memerlukan penguatan pada aspek kesinambungan pendapatan atau efisiensi permodalan sebelum pengajuan pembiayaan bernilai besar.';
        } else {
            $notes .= ' Rekomendasi: Usaha masih memerlukan pendampingan intensif, perbaikan pencatatan keuangan, dan penguatan model bisnis sebelum dinilai layak untuk pembiayaan eksternal.';
        }

        return $notes;
    }
}
