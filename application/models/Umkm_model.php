<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Umkm_model extends CI_Model {

    protected $table = 'umkm';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($params = array())
    {
        $this->apply_filters($params);

        $order_by = !empty($params['order_by']) ? $params['order_by'] : 'id';
        $order_dir = !empty($params['order_dir']) && strtolower($params['order_dir']) === 'desc' ? 'DESC' : 'ASC';
        $this->db->order_by($order_by, $order_dir);

        if (isset($params['limit']) && (int)$params['limit'] > 0) {
            $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
            $this->db->limit((int)$params['limit'], $offset);
        }

        return $this->db->get()->result_array();
    }

    public function count_all($params = array())
    {
        $this->apply_filters($params);
        return $this->db->count_all_results();
    }

    protected function apply_filters($params = array())
    {
        $this->db->from($this->table);

        if (!empty($params['search'])) {
            $s = trim($params['search']);
            $this->db->group_start();
            $this->db->like('id_umkm', $s);
            $this->db->or_like('nama_umkm', $s);
            $this->db->or_like('bidang_usaha', $s);
            $this->db->or_like('kecamatan', $s);
            $this->db->or_like('desa_kelurahan', $s);
            $this->db->group_end();
        }

        if (!empty($params['bidang_usaha'])) {
            $this->db->where('bidang_usaha', $params['bidang_usaha']);
        }

        if (!empty($params['kecamatan'])) {
            $this->db->where('kecamatan', $params['kecamatan']);
        }

        if (!empty($params['desa_kelurahan'])) {
            $this->db->where('desa_kelurahan', $params['desa_kelurahan']);
        }

        if (!empty($params['stabilitas_pendapatan'])) {
            $this->db->where('stabilitas_pendapatan', $params['stabilitas_pendapatan']);
        }

        if (!empty($params['status_usaha'])) {
            $this->db->where('status_usaha', $params['status_usaha']);
        }
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => (int)$id))->row_array();
    }

    public function get_by_id_umkm($id_umkm)
    {
        return $this->db->get_where($this->table, array('id_umkm' => trim($id_umkm)))->row_array();
    }

    public function check_duplicate_id($id_umkm, $exclude_id = null)
    {
        $this->db->from($this->table);
        $this->db->where('id_umkm', trim($id_umkm));
        if ($exclude_id !== null) {
            $this->db->where('id !=', (int)$exclude_id);
        }
        return ($this->db->count_all_results() > 0);
    }

    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int)$id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete($this->table);
    }

    public function get_distinct_kecamatan()
    {
        $this->db->distinct();
        $this->db->select('kecamatan');
        $this->db->where('kecamatan IS NOT NULL', null, false);
        $this->db->where("TRIM(kecamatan) != ''", null, false);
        $this->db->order_by('kecamatan', 'ASC');
        $rows = $this->db->get($this->table)->result_array();
        return array_column($rows, 'kecamatan');
    }

    public function get_distinct_bidang_usaha()
    {
        $this->db->distinct();
        $this->db->select('bidang_usaha');
        $this->db->where('bidang_usaha IS NOT NULL', null, false);
        $this->db->where("TRIM(bidang_usaha) != ''", null, false);
        $this->db->order_by('bidang_usaha', 'ASC');
        $rows = $this->db->get($this->table)->result_array();
        return array_column($rows, 'bidang_usaha');
    }

    public function get_distinct_desa($kecamatan = null)
    {
        $this->db->distinct();
        $this->db->select('desa_kelurahan');
        $this->db->where('desa_kelurahan IS NOT NULL', null, false);
        $this->db->where("TRIM(desa_kelurahan) != ''", null, false);
        if ($kecamatan) {
            $this->db->where('kecamatan', $kecamatan);
        }
        $this->db->order_by('desa_kelurahan', 'ASC');
        $rows = $this->db->get($this->table)->result_array();
        return array_column($rows, 'desa_kelurahan');
    }

    public function get_total_count()
    {
        return $this->db->count_all($this->table);
    }
}
