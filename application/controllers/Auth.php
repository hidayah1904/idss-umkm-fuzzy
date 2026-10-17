<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Activity_log_model');
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        } else {
            redirect('login');
        }
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() === TRUE) {
                $username = $this->input->post('username', true);
                $password = $this->input->post('password');

                $user = $this->User_model->authenticate($username, $password);

                if ($user === 'inactive') {
                    $this->session->set_flashdata('error', 'Akun Anda sedang dinonaktifkan. Hubungi Administrator.');
                } elseif ($user) {
                    $session_data = array(
                        'logged_in' => true,
                        'user_id' => $user['id'],
                        'user_session' => array(
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'username' => $user['username'],
                            'role' => $user['role']
                        )
                    );
                    $this->session->set_userdata($session_data);

                    $this->Activity_log_model->log('LOGIN', "Pengguna {$user['username']} ({$user['name']}) berhasil masuk ke sistem.", 'users', $user['id']);

                    $this->session->set_flashdata('success', "Selamat datang, {$user['name']}! Anda berhasil masuk sebagai " . ucfirst($user['role']) . '.');
                    redirect('dashboard');
                } else {
                    $this->Activity_log_model->log('LOGIN_FAILED', "Percobaan login gagal untuk username: {$username}", 'users', null);
                    $this->session->set_flashdata('error', 'Username atau password yang Anda masukkan salah.');
                }
            }
        }

        $data['page_title'] = 'Login — IDSS UMKM Fuzzy Mamdani';
        $this->load->view('auth/login', $data);
    }

    public function logout()
    {
        $user = $this->session->userdata('user_session');
        if ($user) {
            $this->Activity_log_model->log('LOGOUT', "Pengguna {$user['username']} keluar dari sistem.", 'users', $user['id']);
        }

        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_session');
        $this->session->sess_destroy();

        redirect('login');
    }

    public function profile()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($user_id);

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim|max_length[100]');
            if (!empty($this->input->post('new_password'))) {
                $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required');
                $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password Baru', 'required|matches[new_password]');
            }

            if ($this->form_validation->run() === TRUE) {
                $update_data = array(
                    'name' => $this->input->post('name', true)
                );

                if (!empty($this->input->post('new_password'))) {
                    // Verifikasi password saat ini
                    if (!password_verify($this->input->post('current_password'), $user['password'])) {
                        $this->session->set_flashdata('error', 'Password saat ini yang Anda masukkan tidak sesuai.');
                        redirect('profile');
                        return;
                    }
                    $update_data['password'] = $this->input->post('new_password');
                }

                $this->User_model->update($user_id, $update_data);

                // Perbarui data sesi
                $sess = $this->session->userdata('user_session');
                $sess['name'] = $update_data['name'];
                $this->session->set_userdata('user_session', $sess);

                $this->Activity_log_model->log('UPDATE_PROFILE', "Pengguna memperbarui profil dan/atau password.", 'users', $user_id);
                $this->session->set_flashdata('success', 'Profil dan pengaturan akun berhasil diperbarui.');
                redirect('profile');
            }
        }

        $data['user'] = $user;
        $data['page_title'] = 'Profil & Pengaturan Akun';
        $data['active_menu'] = 'profile';
        $data['breadcrumbs'] = array(
            array('label' => 'Dashboard', 'url' => base_url('dashboard')),
            array('label' => 'Profil Pengguna', 'url' => '')
        );

        $data['logged_user'] = $this->session->userdata('user_session');
        $data['active_fuzzy_model'] = $this->Fuzzy_model->get_active_model();
        $data['content_view'] = 'auth/profile';

        $this->load->view('layouts/main', $data);
    }
}
