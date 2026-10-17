<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Application Controller
 * Handles authentication checks, role permissions, activity logging, and layout rendering
 */
class MY_Controller extends CI_Controller {

    protected $user = null;
    protected $page_title = 'IDSS UMKM';
    protected $active_menu = 'dashboard';
    protected $breadcrumbs = array();

    public function __construct()
    {
        parent::__construct();

        // Cek autentikasi
        if (!$this->is_logged_in()) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => false, 'message' => 'Sesi telah berakhir. Silakan login kembali.')))
                    ->_display();
                exit;
            }

            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
            redirect('login');
        }

        $this->user = $this->session->userdata('user_session');

        // Pastikan model fuzzy aktif selalu tersedia untuk layout footer
        $this->load->vars(array(
            'logged_user' => $this->user,
            'active_fuzzy_model' => $this->Fuzzy_model->get_active_model()
        ));
    }

    protected function is_logged_in()
    {
        return ($this->session->userdata('logged_in') === true && $this->session->userdata('user_session') !== null);
    }

    protected function require_admin()
    {
        if (!$this->is_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin Administrator.')))
                    ->_display();
                exit;
            }

            $this->session->set_flashdata('error', 'Akses ditolak. Fitur ini hanya dapat diakses oleh Administrator.');
            redirect('dashboard');
        }
    }

    protected function is_admin()
    {
        return ($this->user && isset($this->user['role']) && $this->user['role'] === 'admin');
    }

    protected function render($view_file, $data = array())
    {
        $data['page_title'] = isset($data['page_title']) ? $data['page_title'] : $this->page_title;
        $data['active_menu'] = isset($data['active_menu']) ? $data['active_menu'] : $this->active_menu;
        $data['breadcrumbs'] = isset($data['breadcrumbs']) ? $data['breadcrumbs'] : $this->breadcrumbs;
        $data['content_view'] = $view_file;

        $this->load->view('layouts/main', $data);
    }

    protected function json_response($data, $status_code = 200)
    {
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
