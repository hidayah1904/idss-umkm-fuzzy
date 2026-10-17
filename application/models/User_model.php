<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => (int)$id))->row_array();
    }

    public function get_by_username($username)
    {
        return $this->db->get_where($this->table, array('username' => trim($username)))->row_array();
    }

    public function authenticate($username, $password)
    {
        $user = $this->get_by_username($username);
        if (!$user) {
            return false;
        }

        if ((int)$user['is_active'] !== 1) {
            return 'inactive';
        }

        if (password_verify($password, $user['password'])) {
            $this->update_last_login($user['id']);
            return $user;
        }

        return false;
    }

    public function update_last_login($id)
    {
        $this->db->where('id', (int)$id);
        $this->db->update($this->table, array('last_login' => date('Y-m-d H:i:s')));
    }

    public function get_all($limit = 50, $offset = 0, $search = '')
    {
        $this->db->from($this->table);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('username', $search);
            $this->db->or_like('role', $search);
            $this->db->group_end();
        }
        $this->db->order_by('id', 'ASC');
        $this->db->limit((int)$limit, (int)$offset);
        return $this->db->get()->result_array();
    }

    public function count_all($search = '')
    {
        $this->db->from($this->table);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('username', $search);
            $this->db->or_like('role', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function create($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int)$id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $id = (int)$id;
        $user = $this->get_by_id($id);
        if (!$user) {
            return array('status' => false, 'message' => 'Pengguna tidak ditemukan.');
        }

        // Keamanan: Larangan menghapus akun administrator terakhir yang aktif
        if ($user['role'] === 'admin') {
            $active_admins = $this->count_active_admins();
            if ($active_admins <= 1) {
                return array('status' => false, 'message' => 'Tidak dapat menghapus administrator terakhir yang aktif.');
            }
        }

        $this->db->where('id', $id);
        $res = $this->db->delete($this->table);
        return array('status' => $res, 'message' => $res ? 'Pengguna berhasil dihapus.' : 'Gagal menghapus pengguna.');
    }

    public function count_active_admins()
    {
        $this->db->where('role', 'admin');
        $this->db->where('is_active', 1);
        return $this->db->count_all_results($this->table);
    }
}
