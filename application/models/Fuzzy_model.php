<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fuzzy_model extends CI_Model {

    protected $table = 'fuzzy_models';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_active_model()
    {
        return $this->db->get_where($this->table, array('is_active' => 1))->row_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => (int)$id))->row_array();
    }

    public function get_all()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->table)->result_array();
    }

    public function create_version($data)
    {
        $this->db->trans_start();

        // Jika versi baru ditandai aktif, nonaktifkan versi sebelumnya
        if (!empty($data['is_active']) && $data['is_active'] == 1) {
            $this->db->update($this->table, array('is_active' => 0));
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();
        return $this->db->trans_status() ? $insert_id : false;
    }

    public function set_active($id)
    {
        $this->db->trans_start();
        $this->db->update($this->table, array('is_active' => 0));
        $this->db->where('id', (int)$id);
        $this->db->update($this->table, array('is_active' => 1));
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
