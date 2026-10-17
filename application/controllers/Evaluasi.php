<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluasi extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'evaluasi';
        $this->page_title = 'Evaluasi Fuzzy Mamdani';
    }

    /**
     * Halaman Formulir Evaluasi
     */
    public function index()
    {
        $selected_umkm_id = $this->input->get('umkm_id', true);
        $selected_umkm = null;

        if ($selected_umkm_id && is_numeric($selected_umkm_id)) {
            $selected_umkm = $this->Umkm_model->get_by_id($selected_umkm_id);
        }

        $active_model = $this->Fuzzy_model->get_active_model();

        $data = array(
            'page_title' => 'Form Evaluasi Kelayakan — Fuzzy Mamdani',
            'active_menu' => 'evaluasi',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Evaluasi Fuzzy', 'url' => '')
            ),
            'selected_umkm' => $selected_umkm,
            'active_model' => $active_model
        );

        $this->render('evaluasi/form', $data);
    }

    /**
     * Memproses Inferensi Fuzzy Mamdani
     */
    public function proses()
    {
        if ($this->input->method() !== 'post') {
            redirect('evaluasi');
        }

        $this->form_validation->set_rules('modal', 'Nilai Investasi/Modal', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('lama_usaha', 'Lama Usaha', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('stabilitas', 'Stabilitas Pendapatan', 'required|in_list[Fluktuatif,Cukup stabil,Stabil]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<div>', '</div>'));
            $umkm_id = $this->input->post('umkm_id', true);
            $redirect_url = $umkm_id ? 'evaluasi?umkm_id=' . $umkm_id : 'evaluasi';
            redirect($redirect_url);
            return;
        }

        $modal = (float)$this->input->post('modal', true);
        $lama_usaha = (float)$this->input->post('lama_usaha', true);
        $stabilitas = $this->input->post('stabilitas', true);
        $umkm_id = $this->input->post('umkm_id', true);
        $simpan = $this->input->post('simpan_hasil', true);

        // Load active model & initialize engine
        $active_model = $this->Fuzzy_model->get_active_model();
        $this->fuzzy_mamdani->init(array('model' => $active_model));

        // Eksekusi inferensi Mamdani
        $eval_result = $this->fuzzy_mamdani->evaluate($modal, $lama_usaha, $stabilitas);

        $umkm_data = null;
        $saved_id = null;

        if (!empty($umkm_id) && is_numeric($umkm_id)) {
            $umkm_data = $this->Umkm_model->get_by_id($umkm_id);
            if ($umkm_data) {
                $eval_result['umkm'] = $umkm_data;

                // Jika pengguna memilih untuk menyimpan hasil ke database
                if ($simpan == 1) {
                    $insert_eval = array(
                        'umkm_id' => $umkm_data['id'],
                        'fuzzy_model_id' => $active_model['id'],
                        'input_modal' => $modal,
                        'input_lama_usaha' => $lama_usaha,
                        'input_stabilitas' => $stabilitas,
                        'score' => $eval_result['result']['score'],
                        'category' => $eval_result['result']['category'],
                        'result_details_json' => json_encode($eval_result, JSON_UNESCAPED_UNICODE),
                        'evaluated_by' => $this->session->userdata('user_id'),
                        'evaluated_at' => date('Y-m-d H:i:s')
                    );

                    $saved_id = $this->Evaluation_model->save_evaluation($insert_eval);
                    $this->Activity_log_model->log('EVALUATE_UMKM', "Evaluasi kelayakan UMKM: {$umkm_data['nama_umkm']} ({$umkm_data['id_umkm']}) -> Skor {$eval_result['result']['score']} ({$eval_result['result']['category']})", 'evaluations', $saved_id);

                    $this->session->set_flashdata('success', "Evaluasi berhasil diproses dan disimpan ke riwayat UMKM (ID Evaluasi #{$saved_id}).");
                }
            }
        }

        // Tampilkan halaman hasil evaluasi yang transparan dan lengkap
        $data = array(
            'page_title' => 'Hasil Evaluasi Kelayakan — Fuzzy Mamdani',
            'active_menu' => 'evaluasi',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Evaluasi Fuzzy', 'url' => base_url('evaluasi')),
                array('label' => 'Hasil Evaluasi', 'url' => '')
            ),
            'eval_result' => $eval_result,
            'umkm' => $umkm_data,
            'saved_id' => $saved_id,
            'is_simulation' => empty($umkm_data)
        );

        $this->render('evaluasi/hasil', $data);
    }

    /**
     * Halaman Evaluasi Massal Otomatis (Batch Evaluation)
     */
    public function massal()
    {
        $total_umkm = $this->Umkm_model->count_all();
        $evaluated_ids = $this->Evaluation_model->get_evaluated_umkm_ids();
        $total_evaluated_unique = count($evaluated_ids);
        $total_pending = max(0, $total_umkm - $total_evaluated_unique);
        $total_all_evaluations = $this->Evaluation_model->count_all();
        $active_model = $this->Fuzzy_model->get_active_model();

        $data = array(
            'page_title' => 'Evaluasi Massal UMKM — Fuzzy Mamdani',
            'active_menu' => 'evaluasi_massal',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Evaluasi Fuzzy', 'url' => base_url('evaluasi')),
                array('label' => 'Evaluasi Massal', 'url' => '')
            ),
            'total_umkm' => $total_umkm,
            'total_evaluated_unique' => $total_evaluated_unique,
            'total_pending' => $total_pending,
            'total_all_evaluations' => $total_all_evaluations,
            'active_model' => $active_model,
            'batch_summary' => $this->session->flashdata('batch_summary')
        );

        $this->render('evaluasi/massal', $data);
    }

    /**
     * Eksekusi Proses Evaluasi Massal (Batch Fuzzy Mamdani)
     */
    public function proses_massal()
    {
        if ($this->input->method() !== 'post') {
            redirect('evaluasi/massal');
        }

        $scope = $this->input->post('scope', true);
        if (!in_array($scope, array('reset_all', 'all', 'pending_only'))) {
            $scope = 'reset_all';
        }

        $start_time = microtime(true);

        // Ambil data UMKM target
        $evaluated_ids = $this->Evaluation_model->get_evaluated_umkm_ids();
        $all_umkm = $this->Umkm_model->get_all(array('limit' => 5000));

        if (empty($all_umkm)) {
            $this->session->set_flashdata('error', 'Tidak ada data UMKM yang terdaftar di database.');
            redirect('evaluasi/massal');
            return;
        }

        $target_umkm = array();
        if ($scope === 'pending_only') {
            foreach ($all_umkm as $u) {
                if (!in_array((int)$u['id'], $evaluated_ids)) {
                    $target_umkm[] = $u;
                }
            }
        } else {
            $target_umkm = $all_umkm;
        }

        if (empty($target_umkm)) {
            $this->session->set_flashdata('info', 'Semua data UMKM (' . count($all_umkm) . ' usaha) telah memiliki riwayat evaluasi.');
            redirect('evaluasi/massal');
            return;
        }

        // Load active model & inisialisasi fuzzy engine
        $active_model = $this->Fuzzy_model->get_active_model();
        $this->fuzzy_mamdani->init(array('model' => $active_model));

        $user_id = $this->session->userdata('user_id');
        $batch_data = array();
        $cat_counts = array('Tidak Layak' => 0, 'Cukup Layak' => 0, 'Layak' => 0);
        $scores = array();

        foreach ($target_umkm as $u) {
            $modal = (float)$u['nilai_investasi'];
            $lama_usaha = (float)$u['lama_usaha'];
            $stabilitas = $u['stabilitas_pendapatan'];

            // Inferensi Fuzzy Mamdani murni tanpa pemaksaan distribusi
            $res = $this->fuzzy_mamdani->evaluate($modal, $lama_usaha, $stabilitas);
            $score = $res['result']['score'];
            $category = $res['result']['category'];

            $cat_counts[$category]++;
            $scores[] = $score;

            $batch_data[] = array(
                'umkm_id' => $u['id'],
                'fuzzy_model_id' => $active_model['id'],
                'input_modal' => $modal,
                'input_lama_usaha' => $lama_usaha,
                'input_stabilitas' => $stabilitas,
                'score' => $score,
                'category' => $category,
                'result_details_json' => json_encode($res, JSON_UNESCAPED_UNICODE),
                'evaluated_by' => $user_id,
                'evaluated_at' => date('Y-m-d H:i:s')
            );
        }

        // Eksekusi transaksi database
        $this->db->trans_start();

        if ($scope === 'reset_all') {
            $this->Evaluation_model->clear_all_evaluations();
        }

        $this->Evaluation_model->save_batch($batch_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat menyimpan hasil evaluasi massal ke database.');
            redirect('evaluasi/massal');
            return;
        }

        $elapsed = round(microtime(true) - $start_time, 3);
        $total_processed = count($batch_data);
        $avg_score = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
        $min_score = count($scores) > 0 ? min($scores) : 0;
        $max_score = count($scores) > 0 ? max($scores) : 0;

        $log_desc = sprintf(
            'Evaluasi massal %d UMKM selesai dalam %s detik. Hasil: %d Layak, %d Cukup Layak, %d Tidak Layak. Skor Rata-rata: %s.',
            $total_processed, $elapsed, $cat_counts['Layak'], $cat_counts['Cukup Layak'], $cat_counts['Tidak Layak'], $avg_score
        );
        $this->Activity_log_model->log('BATCH_EVALUATE', $log_desc, 'evaluations', null);

        $batch_summary = array(
            'total_processed' => $total_processed,
            'elapsed_seconds' => $elapsed,
            'cat_counts' => $cat_counts,
            'avg_score' => $avg_score,
            'min_score' => $min_score,
            'max_score' => $max_score,
            'scope' => $scope
        );

        $this->session->set_flashdata('batch_summary', $batch_summary);
        $this->session->set_flashdata('success', "Evaluasi massal {$total_processed} UMKM berhasil diproses dan disimpan ke database dalam {$elapsed} detik!");

        redirect('evaluasi/massal');
    }
}
