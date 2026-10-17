<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'laporan';
        $this->page_title = 'Laporan IDSS';
    }

    public function index()
    {
        $type = $this->input->get('type', true);
        if (!$type) {
            $type = 'rekap_kategori';
        }

        $params = array(
            'start_date' => $this->input->get('start_date', true),
            'end_date' => $this->input->get('end_date', true),
            'category' => $this->input->get('category', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'fuzzy_model_id' => $this->input->get('fuzzy_model_id', true),
            'umkm_id' => $this->input->get('umkm_id', true)
        );

        $report_data = array();

        switch ($type) {
            case 'rekap_kategori':
                $report_data = $this->Report_model->get_category_recap($params);
                break;
            case 'bidang_usaha':
                $report_data = $this->Report_model->get_sector_report($params);
                break;
            case 'kecamatan':
                $report_data = $this->Report_model->get_district_report($params);
                break;
            case 'evaluasi_periode':
                $report_data = $this->Evaluation_model->get_all($params);
                break;
            case 'data_umkm':
                $report_data = $this->Umkm_model->get_all($params);
                break;
            case 'riwayat_umkm':
                if (!empty($params['umkm_id'])) {
                    $report_data['umkm'] = $this->Umkm_model->get_by_id($params['umkm_id']);
                    $report_data['evaluations'] = $this->Evaluation_model->get_history_by_umkm($params['umkm_id']);
                }
                break;
            default:
                $type = 'rekap_kategori';
                $report_data = $this->Report_model->get_category_recap($params);
                break;
        }

        $data = array(
            'page_title' => 'Pusat Laporan & Rekapitulasi IDSS',
            'active_menu' => 'laporan',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Laporan', 'url' => '')
            ),
            'report_type' => $type,
            'filters' => $params,
            'report_data' => $report_data,
            'kecamatan_list' => $this->Umkm_model->get_distinct_kecamatan(),
            'bidang_usaha_list' => $this->Umkm_model->get_distinct_bidang_usaha(),
            'models_list' => $this->Fuzzy_model->get_all()
        );

        $this->render('laporan/index', $data);
    }

    public function cetak()
    {
        $type = $this->input->get('type', true);
        if (!$type) $type = 'rekap_kategori';

        $params = array(
            'start_date' => $this->input->get('start_date', true),
            'end_date' => $this->input->get('end_date', true),
            'category' => $this->input->get('category', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'fuzzy_model_id' => $this->input->get('fuzzy_model_id', true),
            'umkm_id' => $this->input->get('umkm_id', true)
        );

        $report_data = array();

        switch ($type) {
            case 'rekap_kategori':
                $report_data = $this->Report_model->get_category_recap($params);
                break;
            case 'bidang_usaha':
                $report_data = $this->Report_model->get_sector_report($params);
                break;
            case 'kecamatan':
                $report_data = $this->Report_model->get_district_report($params);
                break;
            case 'evaluasi_periode':
                $report_data = $this->Evaluation_model->get_all($params);
                break;
            case 'data_umkm':
                $report_data = $this->Umkm_model->get_all($params);
                break;
            case 'riwayat_umkm':
                if (!empty($params['umkm_id'])) {
                    $report_data['umkm'] = $this->Umkm_model->get_by_id($params['umkm_id']);
                    $report_data['evaluations'] = $this->Evaluation_model->get_history_by_umkm($params['umkm_id']);
                }
                break;
        }

        $data = array(
            'page_title' => 'Cetak Laporan — IDSS UMKM',
            'report_type' => $type,
            'filters' => $params,
            'report_data' => $report_data,
            'print_date' => date('d/m/Y H:i:s'),
            'printed_by' => $this->session->userdata('user_session')['name']
        );

        $this->load->view('laporan/cetak', $data);
    }

    public function export_csv()
    {
        $type = $this->input->get('type', true);
        if (!$type) $type = 'rekap_kategori';

        $params = array(
            'start_date' => $this->input->get('start_date', true),
            'end_date' => $this->input->get('end_date', true),
            'category' => $this->input->get('category', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true)
        );

        $filename = 'laporan_' . $type . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        if ($type === 'rekap_kategori') {
            $data = $this->Report_model->get_category_recap($params);
            fputcsv($output, array('Kategori Kelayakan', 'Jumlah Evaluasi', 'Persentase (%)', 'Rata-rata Skor', 'Skor Minimum', 'Skor Maksimum'));
            foreach ($data['items'] as $it) {
                fputcsv($output, array($it['category'], $it['total'], $it['percentage'] . '%', $it['avg_score'], $it['min_score'], $it['max_score']));
            }
        } elseif ($type === 'bidang_usaha') {
            $data = $this->Report_model->get_sector_report($params);
            fputcsv($output, array('Bidang Usaha', 'Total Evaluasi', 'Layak', 'Cukup Layak', 'Tidak Layak', 'Rata-rata Skor'));
            foreach ($data as $it) {
                fputcsv($output, array($it['bidang_usaha'], $it['total_evaluasi'], $it['total_layak'], $it['total_cukup_layak'], $it['total_tidak_layak'], round($it['avg_score'], 2)));
            }
        } elseif ($type === 'kecamatan') {
            $data = $this->Report_model->get_district_report($params);
            fputcsv($output, array('Kecamatan', 'Total Evaluasi', 'Layak', 'Cukup Layak', 'Tidak Layak', 'Rata-rata Skor'));
            foreach ($data as $it) {
                fputcsv($output, array($it['kecamatan'], $it['total_evaluasi'], $it['total_layak'], $it['total_cukup_layak'], $it['total_tidak_layak'], round($it['avg_score'], 2)));
            }
        } elseif ($type === 'evaluasi_periode') {
            $data = $this->Evaluation_model->get_all($params);
            fputcsv($output, array('ID Evaluasi', 'ID UMKM', 'Nama UMKM', 'Bidang Usaha', 'Kecamatan', 'Modal (Rp)', 'Lama Usaha', 'Stabilitas', 'Skor', 'Kategori', 'Tanggal'));
            foreach ($data as $it) {
                fputcsv($output, array($it['id'], $it['id_umkm'], $it['nama_umkm'], $it['bidang_usaha'], $it['kecamatan'], $it['input_modal'], $it['input_lama_usaha'], $it['input_stabilitas'], $it['score'], $it['category'], $it['evaluated_at']));
            }
        }

        fclose($output);
        exit;
    }
}
