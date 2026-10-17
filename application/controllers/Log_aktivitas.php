<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_aktivitas extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->require_admin();
        $this->active_menu = 'log_aktivitas';
        $this->page_title = 'Log Aktivitas Sistem';
    }

    public function index()
    {
        $limit = 30;
        $page = $this->input->get('page', true);
        $offset = ($page && is_numeric($page) && $page > 1) ? ($page - 1) * $limit : 0;

        $total_rows = $this->Activity_log_model->count_all();
        $logs = $this->Activity_log_model->get_all($limit, $offset);

        $data = array(
            'page_title' => 'Log Aktivitas & Audit Trail',
            'active_menu' => 'log_aktivitas',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Log Aktivitas', 'url' => '')
            ),
            'logs' => $logs,
            'total_rows' => $total_rows,
            'limit' => $limit,
            'offset' => $offset,
            'current_page' => ($offset / $limit) + 1,
            'total_pages' => ceil($total_rows / $limit)
        );

        $this->render('log_aktivitas/index', $data);
    }
}
