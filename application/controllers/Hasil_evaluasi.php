<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hasil_evaluasi extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'hasil_evaluasi';
        $this->page_title = 'Hasil & Riwayat Evaluasi';
    }

    public function index()
    {
        $params = array(
            'search' => $this->input->get('search', true),
            'category' => $this->input->get('category', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'fuzzy_model_id' => $this->input->get('fuzzy_model_id', true),
            'start_date' => $this->input->get('start_date', true),
            'end_date' => $this->input->get('end_date', true),
            'order_by' => $this->input->get('order_by', true) ? $this->input->get('order_by', true) : 'e.evaluated_at',
            'order_dir' => $this->input->get('order_dir', true) ? $this->input->get('order_dir', true) : 'desc'
        );

        $limit = $this->input->get('limit', true);
        $limit = ($limit && is_numeric($limit) && $limit > 0) ? (int)$limit : 20;
        $page = $this->input->get('page', true);
        $offset = ($page && is_numeric($page) && $page > 1) ? ($page - 1) * $limit : 0;

        $total_rows = $this->Evaluation_model->count_all($params);

        $params['limit'] = $limit;
        $params['offset'] = $offset;
        $evaluations = $this->Evaluation_model->get_all($params);

        $data = array(
            'page_title' => 'Riwayat & Hasil Evaluasi Kelayakan',
            'active_menu' => 'hasil_evaluasi',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Hasil Evaluasi', 'url' => '')
            ),
            'evaluations' => $evaluations,
            'total_rows' => $total_rows,
            'limit' => $limit,
            'offset' => $offset,
            'current_page' => ($offset / $limit) + 1,
            'total_pages' => ceil($total_rows / $limit),
            'filters' => $params,
            'kecamatan_list' => $this->Umkm_model->get_distinct_kecamatan(),
            'bidang_usaha_list' => $this->Umkm_model->get_distinct_bidang_usaha(),
            'models_list' => $this->Fuzzy_model->get_all()
        );

        $this->render('hasil_evaluasi/index', $data);
    }

    public function detail($id)
    {
        $evaluation = $this->Evaluation_model->get_by_id($id);
        if (!$evaluation) {
            $this->session->set_flashdata('error', 'Data evaluasi tidak ditemukan.');
            redirect('hasil_evaluasi');
        }

        $details = json_decode($evaluation['result_details_json'], true);

        // Ambil riwayat evaluasi lain dari UMKM yang sama untuk perbandingan
        $other_evaluations = $this->Evaluation_model->get_history_by_umkm($evaluation['umkm_id']);

        $data = array(
            'page_title' => 'Rincian Hasil Evaluasi #' . $evaluation['id'],
            'active_menu' => 'hasil_evaluasi',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Hasil Evaluasi', 'url' => base_url('hasil_evaluasi')),
                array('label' => '#' . $evaluation['id'], 'url' => '')
            ),
            'eval' => $evaluation,
            'details' => $details,
            'other_evaluations' => $other_evaluations
        );

        $this->render('hasil_evaluasi/detail', $data);
    }

    public function cetak($id)
    {
        $evaluation = $this->Evaluation_model->get_by_id($id);
        if (!$evaluation) {
            show_404();
        }

        $details = json_decode($evaluation['result_details_json'], true);

        $data = array(
            'page_title' => 'Cetak Evaluasi #' . $evaluation['id'],
            'eval' => $evaluation,
            'details' => $details
        );

        $this->load->view('hasil_evaluasi/cetak', $data);
    }

    public function bandingkan()
    {
        $id1 = $this->input->get('id1', true);
        $id2 = $this->input->get('id2', true);

        if (!$id1 || !$id2) {
            $this->session->set_flashdata('error', 'Pilih dua evaluasi untuk dibandingkan.');
            redirect('hasil_evaluasi');
        }

        $eval1 = $this->Evaluation_model->get_by_id($id1);
        $eval2 = $this->Evaluation_model->get_by_id($id2);

        if (!$eval1 || !$eval2) {
            $this->session->set_flashdata('error', 'Salah satu atau kedua evaluasi tidak ditemukan.');
            redirect('hasil_evaluasi');
        }

        $details1 = json_decode($eval1['result_details_json'], true);
        $details2 = json_decode($eval2['result_details_json'], true);

        $data = array(
            'page_title' => 'Perbandingan Evaluasi #' . $id1 . ' vs #' . $id2,
            'active_menu' => 'hasil_evaluasi',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Hasil Evaluasi', 'url' => base_url('hasil_evaluasi')),
                array('label' => 'Perbandingan', 'url' => '')
            ),
            'eval1' => $eval1,
            'eval2' => $eval2,
            'details1' => $details1,
            'details2' => $details2
        );

        $this->render('hasil_evaluasi/bandingkan', $data);
    }

    public function export($format = 'csv')
    {
        $params = array(
            'search' => $this->input->get('search', true),
            'category' => $this->input->get('category', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'fuzzy_model_id' => $this->input->get('fuzzy_model_id', true),
            'start_date' => $this->input->get('start_date', true),
            'end_date' => $this->input->get('end_date', true)
        );

        $evaluations = $this->Evaluation_model->get_all($params);

        $filename = 'hasil_evaluasi_fuzzy_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, array(
            'ID Evaluasi',
            'ID UMKM',
            'Nama UMKM',
            'Bidang Usaha',
            'Kecamatan',
            'Desa/Kelurahan',
            'Modal / Investasi (Rp)',
            'Lama Usaha (Tahun)',
            'Stabilitas Pendapatan',
            'Skor Fuzzy (0-100)',
            'Kategori Kelayakan',
            'Versi Model',
            'Waktu Evaluasi',
            'Evaluator'
        ));

        foreach ($evaluations as $e) {
            fputcsv($output, array(
                $e['id'],
                $e['id_umkm'],
                $e['nama_umkm'],
                $e['bidang_usaha'],
                $e['kecamatan'],
                $e['desa_kelurahan'],
                $e['input_modal'],
                $e['input_lama_usaha'],
                $e['input_stabilitas'],
                $e['score'],
                $e['category'],
                $e['model_version'] ? 'v' . $e['model_version'] : 'v1.0.0',
                $e['evaluated_at'],
                $e['evaluator_name'] ? $e['evaluator_name'] : 'Sistem'
            ));
        }

        fclose($output);
        exit;
    }
}
