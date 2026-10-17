<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {

    protected $table = 'activity_logs';

    public function __construct()
    {
        parent::__construct();
    }

    public function log($action, $description, $entity_type = null, $entity_id = null)
    {
        $user_id = $this->session->userdata('user_id');

        $data = array(
            'user_id' => !empty($user_id) ? (int)$user_id : null,
            'action' => substr($action, 0, 100),
            'entity_type' => !empty($entity_type) ? substr($entity_type, 0, 50) : null,
            'entity_id' => !empty($entity_id) ? (int)$entity_id : null,
            'description' => $description,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr((string)$this->input->user_agent(), 0, 255),
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert($this->table, $data);
    }

    public function get_recent($limit = 10)
    {
        $this->db->select('l.*, u.name as user_name, u.username');
        $this->db->from($this->table . ' l');
        $this->db->join('users u', 'u.id = l.user_id', 'left');
        $this->db->order_by('l.id', 'DESC');
        $this->db->limit((int)$limit);
        return $this->db->get()->result_array();
    }

    public function get_all($limit = 50, $offset = 0)
    {
        $this->db->select('l.*, u.name as user_name, u.username');
        $this->db->from($this->table . ' l');
        $this->db->join('users u', 'u.id = l.user_id', 'left');
        $this->db->order_by('l.id', 'DESC');
        $this->db->limit((int)$limit, (int)$offset);
        return $this->db->get()->result_array();
    }

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }
}
