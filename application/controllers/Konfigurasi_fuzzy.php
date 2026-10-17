<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfigurasi_fuzzy extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'konfigurasi_fuzzy';
        $this->page_title = 'Konfigurasi Model Fuzzy Mamdani';
    }

    public function index()
    {
        $model_id = $this->input->get('model_id', true);
        if ($model_id && is_numeric($model_id)) {
            $active_model = $this->Fuzzy_model->get_by_id($model_id);
        } else {
            $active_model = $this->Fuzzy_model->get_active_model();
        }

        if (!$active_model) {
            $this->session->set_flashdata('error', 'Konfigurasi model tidak ditemukan.');
            redirect('dashboard');
        }

        $config_data = json_decode($active_model['configuration_json'], true);
        $rule_base = json_decode($active_model['rule_base_json'], true);
        $all_models = $this->Fuzzy_model->get_all();

        $data = array(
            'page_title' => 'Konfigurasi Model Fuzzy Mamdani',
            'active_menu' => 'konfigurasi_fuzzy',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Konfigurasi Fuzzy', 'url' => '')
            ),
            'model' => $active_model,
            'config_data' => $config_data,
            'rule_base' => $rule_base,
            'all_models' => $all_models,
            'is_admin' => $this->is_admin()
        );

        $this->render('konfigurasi_fuzzy/index', $data);
    }

    public function activate($id)
    {
        $this->require_admin();

        $model = $this->Fuzzy_model->get_by_id($id);
        if (!$model) {
            $this->session->set_flashdata('error', 'Model tidak ditemukan.');
            redirect('konfigurasi_fuzzy');
        }

        $this->Fuzzy_model->set_active($id);
        $this->Activity_log_model->log('ACTIVATE_MODEL', "Mengaktifkan model fuzzy: {$model['model_code']} ({$model['version']})", 'fuzzy_models', $id);

        $this->session->set_flashdata('success', "Model '{$model['model_name']}' ({$model['version']}) berhasil diaktifkan sebagai model default.");
        redirect('konfigurasi_fuzzy?model_id=' . $id);
    }
}
