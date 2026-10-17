<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Umkm extends MY_Controller {

    public $import_service;
    public $Import_service;

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'umkm';
        $this->page_title = 'Data UMKM';
        $this->load->library('Import_service', NULL, 'import_service');
        $this->Import_service = $this->import_service;
    }

    public function index()
    {
        $params = array(
            'search' => $this->input->get('search', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'desa_kelurahan' => $this->input->get('desa_kelurahan', true),
            'stabilitas_pendapatan' => $this->input->get('stabilitas_pendapatan', true),
            'status_usaha' => $this->input->get('status_usaha', true),
            'order_by' => $this->input->get('order_by', true) ? $this->input->get('order_by', true) : 'id',
            'order_dir' => $this->input->get('order_dir', true) ? $this->input->get('order_dir', true) : 'asc'
        );

        $limit = $this->input->get('limit', true);
        $limit = ($limit && is_numeric($limit) && $limit > 0) ? (int)$limit : 20;
        $page = $this->input->get('page', true);
        $offset = ($page && is_numeric($page) && $page > 1) ? ($page - 1) * $limit : 0;

        $total_rows = $this->Umkm_model->count_all($params);

        $params['limit'] = $limit;
        $params['offset'] = $offset;
        $umkm_list = $this->Umkm_model->get_all($params);

        $data = array(
            'page_title' => 'Kelola Data UMKM',
            'active_menu' => 'umkm',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Data UMKM', 'url' => '')
            ),
            'umkm_list' => $umkm_list,
            'total_rows' => $total_rows,
            'limit' => $limit,
            'offset' => $offset,
            'current_page' => ($offset / $limit) + 1,
            'total_pages' => ceil($total_rows / $limit),
            'filters' => $params,
            'kecamatan_list' => $this->Umkm_model->get_distinct_kecamatan(),
            'bidang_usaha_list' => $this->Umkm_model->get_distinct_bidang_usaha()
        );

        $this->render('umkm/index', $data);
    }

    public function detail($id)
    {
        $umkm = $this->Umkm_model->get_by_id($id);
        if (!$umkm) {
            $this->session->set_flashdata('error', 'Data UMKM tidak ditemukan.');
            redirect('umkm');
        }

        // Riwayat evaluasi fuzzy untuk UMKM ini
        $evaluations = $this->Evaluation_model->get_history_by_umkm($id);

        $data = array(
            'page_title' => 'Detail UMKM — ' . $umkm['nama_umkm'],
            'active_menu' => 'umkm',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Data UMKM', 'url' => base_url('umkm')),
                array('label' => $umkm['id_umkm'], 'url' => '')
            ),
            'umkm' => $umkm,
            'evaluations' => $evaluations
        );

        $this->render('umkm/detail', $data);
    }

    public function tambah()
    {
        if ($this->input->method() === 'post') {
            $this->validate_umkm_form();

            if ($this->form_validation->run() === TRUE) {
                $id_umkm = trim($this->input->post('id_umkm', true));
                if ($this->Umkm_model->check_duplicate_id($id_umkm)) {
                    $this->session->set_flashdata('error', "ID UMKM '{$id_umkm}' sudah digunakan. Harap gunakan ID yang unik.");
                } else {
                    $insert_data = array(
                        'id_umkm' => $id_umkm,
                        'nama_umkm' => trim($this->input->post('nama_umkm', true)),
                        'bidang_usaha' => trim($this->input->post('bidang_usaha', true)),
                        'nilai_investasi' => (float)$this->input->post('nilai_investasi', true),
                        'kecamatan' => trim($this->input->post('kecamatan', true)),
                        'desa_kelurahan' => trim($this->input->post('desa_kelurahan', true)),
                        'nilai_omzet' => (float)$this->input->post('nilai_omzet', true),
                        'nilai_laba' => (float)$this->input->post('nilai_laba', true),
                        'lama_usaha' => (float)$this->input->post('lama_usaha', true),
                        'biaya_operasional' => (float)$this->input->post('biaya_operasional', true),
                        'arus_kas_bersih' => (float)$this->input->post('arus_kas_bersih', true),
                        'stabilitas_pendapatan' => $this->input->post('stabilitas_pendapatan', true),
                        'status_usaha' => $this->input->post('status_usaha', true) ? $this->input->post('status_usaha', true) : 'Aktif'
                    );

                    $insert_id = $this->Umkm_model->create($insert_data);
                    $this->Activity_log_model->log('CREATE_UMKM', "Menambahkan UMKM baru: {$insert_data['nama_umkm']} ({$insert_data['id_umkm']})", 'umkm', $insert_id);

                    $this->session->set_flashdata('success', "Data UMKM '{$insert_data['nama_umkm']}' berhasil ditambahkan.");
                    redirect('umkm');
                }
            }
        }

        $data = array(
            'page_title' => 'Tambah Data UMKM Baru',
            'active_menu' => 'umkm',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Data UMKM', 'url' => base_url('umkm')),
                array('label' => 'Tambah Baru', 'url' => '')
            ),
            'kecamatan_list' => $this->Umkm_model->get_distinct_kecamatan()
        );

        $this->render('umkm/form', $data);
    }

    public function ubah($id)
    {
        $umkm = $this->Umkm_model->get_by_id($id);
        if (!$umkm) {
            $this->session->set_flashdata('error', 'Data UMKM tidak ditemukan.');
            redirect('umkm');
        }

        if ($this->input->method() === 'post') {
            $this->validate_umkm_form($id);

            if ($this->form_validation->run() === TRUE) {
                $id_umkm = trim($this->input->post('id_umkm', true));
                if ($this->Umkm_model->check_duplicate_id($id_umkm, $id)) {
                    $this->session->set_flashdata('error', "ID UMKM '{$id_umkm}' sudah digunakan oleh data lain.");
                } else {
                    $update_data = array(
                        'id_umkm' => $id_umkm,
                        'nama_umkm' => trim($this->input->post('nama_umkm', true)),
                        'bidang_usaha' => trim($this->input->post('bidang_usaha', true)),
                        'nilai_investasi' => (float)$this->input->post('nilai_investasi', true),
                        'kecamatan' => trim($this->input->post('kecamatan', true)),
                        'desa_kelurahan' => trim($this->input->post('desa_kelurahan', true)),
                        'nilai_omzet' => (float)$this->input->post('nilai_omzet', true),
                        'nilai_laba' => (float)$this->input->post('nilai_laba', true),
                        'lama_usaha' => (float)$this->input->post('lama_usaha', true),
                        'biaya_operasional' => (float)$this->input->post('biaya_operasional', true),
                        'arus_kas_bersih' => (float)$this->input->post('arus_kas_bersih', true),
                        'stabilitas_pendapatan' => $this->input->post('stabilitas_pendapatan', true),
                        'status_usaha' => $this->input->post('status_usaha', true)
                    );

                    $this->Umkm_model->update($id, $update_data);
                    $this->Activity_log_model->log('UPDATE_UMKM', "Memperbarui data UMKM: {$update_data['nama_umkm']} ({$update_data['id_umkm']})", 'umkm', $id);

                    $this->session->set_flashdata('success', "Data UMKM '{$update_data['nama_umkm']}' berhasil diperbarui.");
                    redirect('umkm/detail/' . $id);
                }
            }
        }

        $data = array(
            'page_title' => 'Ubah Data UMKM — ' . $umkm['nama_umkm'],
            'active_menu' => 'umkm',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Data UMKM', 'url' => base_url('umkm')),
                array('label' => $umkm['nama_umkm'], 'url' => base_url('umkm/detail/' . $id)),
                array('label' => 'Ubah', 'url' => '')
            ),
            'umkm' => $umkm,
            'kecamatan_list' => $this->Umkm_model->get_distinct_kecamatan()
        );

        $this->render('umkm/form', $data);
    }

    public function hapus($id)
    {
        $umkm = $this->Umkm_model->get_by_id($id);
        if (!$umkm) {
            $this->session->set_flashdata('error', 'Data UMKM tidak ditemukan.');
            redirect('umkm');
        }

        $nama = $umkm['nama_umkm'];
        $id_umkm = $umkm['id_umkm'];

        $this->Umkm_model->delete($id);
        $this->Activity_log_model->log('DELETE_UMKM', "Menghapus UMKM: {$nama} ({$id_umkm})", 'umkm', $id);

        $this->session->set_flashdata('success', "Data UMKM '{$nama}' ({$id_umkm}) berhasil dihapus beserta riwayat terkait.");
        redirect('umkm');
    }

    public function import()
    {
        if ($this->input->method() === 'post') {
            if (empty($_FILES['file_dataset']['name'])) {
                $this->session->set_flashdata('error', 'Silakan pilih file dataset (.csv atau .xlsx) untuk diunggah.');
                redirect('umkm/import');
            }

            $strategy = $this->input->post('duplicate_strategy', true);
            if (!in_array($strategy, array('skip', 'update'))) {
                $strategy = 'skip';
            }

            $config['upload_path'] = sys_get_temp_dir();
            $config['allowed_types'] = 'csv|xlsx|xls';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file_dataset')) {
                $this->session->set_flashdata('error', 'Gagal mengunggah file: ' . $this->upload->display_errors('', ''));
                redirect('umkm/import');
            }

            $upload_data = $this->upload->data();
            $file_path = $upload_data['full_path'];
            $orig_name = !empty($upload_data['orig_name']) 
                ? $upload_data['orig_name'] 
                : (!empty($upload_data['client_name']) 
                    ? $upload_data['client_name'] 
                    : (!empty($_FILES['file_dataset']['name']) 
                        ? $_FILES['file_dataset']['name'] 
                        : $upload_data['file_name']));

            $import_service = isset($this->import_service) ? $this->import_service : (isset($this->Import_service) ? $this->Import_service : new Import_service());
            $parsed = $import_service->parse_file($file_path, $orig_name);

            // Hapus file temporer
            if (file_exists($file_path)) {
                @unlink($file_path);
            }

            if (!$parsed['success']) {
                $this->session->set_flashdata('error', 'Gagal memproses file: ' . $parsed['message']);
                redirect('umkm/import');
            }

            $records = $parsed['records'];
            if (empty($records)) {
                $this->session->set_flashdata('warning', 'Tidak ditemukan baris data yang valid dalam file tersebut.');
                redirect('umkm/import');
            }

            // Jalankan transaksi database untuk integritas dataset
            $this->db->trans_start();

            $inserted = 0;
            $updated = 0;
            $skipped = 0;
            $failed = 0;
            $error_logs = array();

            foreach ($records as $r) {
                $id_umkm = $r['id_umkm'];
                $existing = $this->Umkm_model->get_by_id_umkm($id_umkm);

                $data_row = array(
                    'id_umkm' => $id_umkm,
                    'nama_umkm' => $r['nama_umkm'],
                    'bidang_usaha' => $r['bidang_usaha'],
                    'nilai_investasi' => $r['nilai_investasi'],
                    'kecamatan' => $r['kecamatan'],
                    'desa_kelurahan' => $r['desa_kelurahan'],
                    'nilai_omzet' => $r['nilai_omzet'],
                    'nilai_laba' => $r['nilai_laba'],
                    'lama_usaha' => $r['lama_usaha'],
                    'biaya_operasional' => $r['biaya_operasional'],
                    'arus_kas_bersih' => $r['arus_kas_bersih'],
                    'stabilitas_pendapatan' => $r['stabilitas_pendapatan'],
                    'status_usaha' => $r['status_usaha']
                );

                if ($existing) {
                    if ($strategy === 'update') {
                        $this->Umkm_model->update($existing['id'], $data_row);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $insert_id = $this->Umkm_model->create($data_row);
                    if ($insert_id) {
                        $inserted++;
                    } else {
                        $failed++;
                        $error_logs[] = "Baris {$r['row_num']}: Gagal menyimpan ID {$id_umkm}.";
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat menyimpan data impor ke database.');
                redirect('umkm/import');
            }

            $this->Activity_log_model->log('IMPORT_UMKM', "Impor dataset '{$orig_name}': {$inserted} baru, {$updated} diperbarui, {$skipped} dilewati, {$failed} gagal.", 'umkm', null);

            $summary_msg = sprintf(
                'Impor dataset selesai! Berhasil ditambahkan: %d | Diperbarui: %d | Dilewati (duplikat): %d | Gagal: %d.',
                $inserted, $updated, $skipped, $failed
            );

            $this->session->set_flashdata('success', $summary_msg);
            redirect('umkm');
        }

        $data = array(
            'page_title' => 'Impor Dataset UMKM',
            'active_menu' => 'umkm',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Data UMKM', 'url' => base_url('umkm')),
                array('label' => 'Impor Dataset', 'url' => '')
            )
        );

        $this->render('umkm/import', $data);
    }

    public function export($format = 'csv')
    {
        $params = array(
            'search' => $this->input->get('search', true),
            'bidang_usaha' => $this->input->get('bidang_usaha', true),
            'kecamatan' => $this->input->get('kecamatan', true),
            'stabilitas_pendapatan' => $this->input->get('stabilitas_pendapatan', true),
            'status_usaha' => $this->input->get('status_usaha', true)
        );

        $rows = $this->Umkm_model->get_all($params);

        $filename = 'dataset_umkm_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar Excel dapat menampilkan aksara dengan benar
        fputs($output, "\xEF\xBB\xBF");

        // Header kolom eksplisit
        fputcsv($output, array(
            'NO',
            'ID UMKM',
            'Nama UMKM / Usaha',
            'Bidang Usaha',
            'Nilai Investasi/Modal (Rp)',
            'Kecamatan',
            'Desa/Kelurahan',
            'Nilai Omzet (Rp)',
            'Nilai Laba (Rp)',
            'Lama Usaha (Tahun)',
            'Biaya Operasional (Rp)',
            'Arus Kas Bersih (Rp)',
            'Stabilitas Pendapatan',
            'Status Usaha Administratif'
        ));

        $no = 1;
        foreach ($rows as $r) {
            fputcsv($output, array(
                $no++,
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
            ));
        }

        fclose($output);
        exit;
    }

    public function ajax_search()
    {
        $term = $this->input->get('term', true);
        if (empty($term)) {
            $this->json_response(array('results' => array()));
            return;
        }

        $params = array(
            'search' => $term,
            'limit' => 15
        );
        $rows = $this->Umkm_model->get_all($params);

        $results = array();
        foreach ($rows as $r) {
            $results[] = array(
                'id' => $r['id'],
                'text' => "[{$r['id_umkm']}] {$r['nama_umkm']} - {$r['bidang_usaha']} ({$r['kecamatan']})",
                'umkm_data' => $r
            );
        }

        $this->json_response(array('results' => $results));
    }

    protected function validate_umkm_form($id = null)
    {
        $this->form_validation->set_rules('id_umkm', 'ID UMKM', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('nama_umkm', 'Nama UMKM/Pemilik', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('bidang_usaha', 'Bidang Usaha', 'trim|max_length[150]');
        $this->form_validation->set_rules('nilai_investasi', 'Nilai Investasi/Modal', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('lama_usaha', 'Lama Usaha', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('stabilitas_pendapatan', 'Stabilitas Pendapatan', 'required|in_list[Fluktuatif,Cukup stabil,Stabil]');
        $this->form_validation->set_rules('kecamatan', 'Kecamatan', 'trim|max_length[100]');
        $this->form_validation->set_rules('desa_kelurahan', 'Desa/Kelurahan', 'trim|max_length[100]');
        $this->form_validation->set_rules('nilai_omzet', 'Nilai Omzet', 'numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('nilai_laba', 'Nilai Laba', 'numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('biaya_operasional', 'Biaya Operasional', 'numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('arus_kas_bersih', 'Arus Kas Bersih', 'numeric');
        $this->form_validation->set_rules('status_usaha', 'Status Usaha Administratif', 'trim|max_length[50]');
    }
}
