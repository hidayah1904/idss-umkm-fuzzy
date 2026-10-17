<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->require_admin();
        $this->active_menu = 'pengguna';
        $this->page_title = 'Manajemen Pengguna';
    }

    public function index()
    {
        $search = $this->input->get('search', true);
        $users = $this->User_model->get_all(50, 0, $search);

        $data = array(
            'page_title' => 'Manajemen Pengguna Sistem',
            'active_menu' => 'pengguna',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Pengguna', 'url' => '')
            ),
            'users' => $users,
            'search' => $search
        );

        $this->render('pengguna/index', $data);
    }

    public function tambah()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|max_length[50]|is_unique[users.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator]');

            if ($this->form_validation->run() === TRUE) {
                $user_data = array(
                    'name' => trim($this->input->post('name', true)),
                    'username' => trim($this->input->post('username', true)),
                    'password' => $this->input->post('password'),
                    'role' => $this->input->post('role', true),
                    'is_active' => $this->input->post('is_active') ? 1 : 0
                );

                $insert_id = $this->User_model->create($user_data);
                $this->Activity_log_model->log('CREATE_USER', "Menambahkan pengguna baru: {$user_data['username']} ({$user_data['role']})", 'users', $insert_id);

                $this->session->set_flashdata('success', "Pengguna '{$user_data['username']}' berhasil ditambahkan.");
                redirect('pengguna');
            }
        }

        $data = array(
            'page_title' => 'Tambah Pengguna Baru',
            'active_menu' => 'pengguna',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Pengguna', 'url' => base_url('pengguna')),
                array('label' => 'Tambah Baru', 'url' => '')
            )
        );

        $this->render('pengguna/form', $data);
    }

    public function ubah($id)
    {
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('pengguna');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator]');

            $new_username = trim($this->input->post('username', true));
            if ($new_username !== $user['username']) {
                $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|max_length[50]|is_unique[users.username]');
            }

            if (!empty($this->input->post('password'))) {
                $this->form_validation->set_rules('password', 'Password Baru', 'min_length[6]');
            }

            if ($this->form_validation->run() === TRUE) {
                // Keamanan: Cek jika menurunkan role admin terakhir yang aktif
                if ($user['role'] === 'admin' && $this->input->post('role') !== 'admin') {
                    $active_admins = $this->User_model->count_active_admins();
                    if ($active_admins <= 1) {
                        $this->session->set_flashdata('error', 'Tidak dapat mengubah role administrator terakhir yang aktif.');
                        redirect('pengguna/ubah/' . $id);
                        return;
                    }
                }

                $update_data = array(
                    'name' => trim($this->input->post('name', true)),
                    'username' => $new_username,
                    'role' => $this->input->post('role', true),
                    'is_active' => $this->input->post('is_active') ? 1 : 0
                );

                if (!empty($this->input->post('password'))) {
                    $update_data['password'] = $this->input->post('password');
                }

                $this->User_model->update($id, $update_data);
                $this->Activity_log_model->log('UPDATE_USER', "Memperbarui pengguna: {$update_data['username']}", 'users', $id);

                $this->session->set_flashdata('success', "Data pengguna '{$update_data['username']}' berhasil diperbarui.");
                redirect('pengguna');
            }
        }

        $data = array(
            'page_title' => 'Ubah Data Pengguna — ' . $user['username'],
            'active_menu' => 'pengguna',
            'breadcrumbs' => array(
                array('label' => 'Dashboard', 'url' => base_url('dashboard')),
                array('label' => 'Pengguna', 'url' => base_url('pengguna')),
                array('label' => 'Ubah ' . $user['username'], 'url' => '')
            ),
            'edit_user' => $user
        );

        $this->render('pengguna/form', $data);
    }

    public function hapus($id)
    {
        $del_res = $this->User_model->delete($id);
        if ($del_res['status']) {
            $this->Activity_log_model->log('DELETE_USER', "Menghapus akun pengguna ID #{$id}", 'users', $id);
            $this->session->set_flashdata('success', $del_res['message']);
        } else {
            $this->session->set_flashdata('error', $del_res['message']);
        }

        redirect('pengguna');
    }
}
