<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluation_model extends CI_Model {

    protected $table = 'evaluations';

    public function __construct()
    {
        parent::__construct();
    }

    public function save_evaluation($data)
    {
        if (empty($data['evaluated_at'])) {
            $data['evaluated_at'] = date('Y-m-d H:i:s');
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function save_batch($batch_data, $chunk_size = 15)
    {
        if (empty($batch_data)) {
            return 0;
        }
        $total = 0;
        $chunks = array_chunk($batch_data, $chunk_size);
        foreach ($chunks as $chunk) {
            $this->db->insert_batch($this->table, $chunk);
            $total += count($chunk);
        }
        return $total;
    }

    public function clear_all_evaluations()
    {
        return $this->db->empty_table($this->table);
    }

    public function get_evaluated_umkm_ids()
    {
        $this->db->select('DISTINCT(umkm_id) as umkm_id');
        $rows = $this->db->get($this->table)->result_array();
        return array_map('intval', array_column($rows, 'umkm_id'));
    }

    public function get_score_summary()
    {
        $this->db->select('MIN(score) as min_score, MAX(score) as max_score, AVG(score) as avg_score, COUNT(*) as total');
        return $this->db->get($this->table)->row_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('e.*, u.id_umkm, u.nama_umkm, u.bidang_usaha, u.kecamatan, u.desa_kelurahan, u.status_usaha, u.nilai_omzet, u.nilai_laba, u.biaya_operasional, u.arus_kas_bersih, fm.model_code, fm.model_name, fm.version as model_version, usr.name as evaluator_name');
        $this->db->from($this->table . ' e');
        $this->db->join('umkm u', 'u.id = e.umkm_id', 'left');
        $this->db->join('fuzzy_models fm', 'fm.id = e.fuzzy_model_id', 'left');
        $this->db->join('users usr', 'usr.id = e.evaluated_by', 'left');
        $this->db->where('e.id', (int)$id);
        return $this->db->get()->row_array();
    }

    public function get_all($params = array())
    {
        $this->apply_evaluation_filters($params);

        $order_by = !empty($params['order_by']) ? $params['order_by'] : 'e.evaluated_at';
        $order_dir = !empty($params['order_dir']) && strtolower($params['order_dir']) === 'asc' ? 'ASC' : 'DESC';
        $this->db->order_by($order_by, $order_dir);

        if (isset($params['limit']) && (int)$params['limit'] > 0) {
            $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
            $this->db->limit((int)$params['limit'], $offset);
        }

        return $this->db->get()->result_array();
    }

    public function count_all($params = array())
    {
        $this->apply_evaluation_filters($params);
        return $this->db->count_all_results();
    }

    protected function apply_evaluation_filters($params = array())
    {
        $this->db->select('e.*, u.id_umkm, u.nama_umkm, u.bidang_usaha, u.kecamatan, u.desa_kelurahan, fm.model_code, fm.version as model_version, usr.name as evaluator_name');
        $this->db->from($this->table . ' e');
        $this->db->join('umkm u', 'u.id = e.umkm_id', 'left');
        $this->db->join('fuzzy_models fm', 'fm.id = e.fuzzy_model_id', 'left');
        $this->db->join('users usr', 'usr.id = e.evaluated_by', 'left');

        if (!empty($params['search'])) {
            $s = trim($params['search']);
            $this->db->group_start();
            $this->db->like('u.id_umkm', $s);
            $this->db->or_like('u.nama_umkm', $s);
            $this->db->or_like('u.bidang_usaha', $s);
            $this->db->group_end();
        }

        if (!empty($params['category'])) {
            $this->db->where('e.category', $params['category']);
        }

        if (!empty($params['kecamatan'])) {
            $this->db->where('u.kecamatan', $params['kecamatan']);
        }

        if (!empty($params['bidang_usaha'])) {
            $this->db->where('u.bidang_usaha', $params['bidang_usaha']);
        }

        if (!empty($params['fuzzy_model_id'])) {
            $this->db->where('e.fuzzy_model_id', (int)$params['fuzzy_model_id']);
        }

        if (!empty($params['umkm_id'])) {
            $this->db->where('e.umkm_id', (int)$params['umkm_id']);
        }

        if (!empty($params['start_date'])) {
            $this->db->where('DATE(e.evaluated_at) >=', $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $this->db->where('DATE(e.evaluated_at) <=', $params['end_date']);
        }
    }

    public function get_history_by_umkm($umkm_id)
    {
        $this->db->select('e.*, fm.model_code, fm.version as model_version, usr.name as evaluator_name');
        $this->db->from($this->table . ' e');
        $this->db->join('fuzzy_models fm', 'fm.id = e.fuzzy_model_id', 'left');
        $this->db->join('users usr', 'usr.id = e.evaluated_by', 'left');
        $this->db->where('e.umkm_id', (int)$umkm_id);
        $this->db->order_by('e.evaluated_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_stats_counts()
    {
        $total_umkm = $this->db->count_all('umkm');
        $total_eval = $this->db->count_all($this->table);

        $this->db->select('category, COUNT(*) as count');
        $this->db->group_by('category');
        $cat_query = $this->db->get($this->table)->result_array();

        $cat_counts = array(
            'Layak' => 0,
            'Cukup Layak' => 0,
            'Tidak Layak' => 0
        );

        foreach ($cat_query as $row) {
            if (isset($cat_counts[$row['category']])) {
                $cat_counts[$row['category']] = (int)$row['count'];
            }
        }

        return array(
            'total_umkm' => $total_umkm,
            'total_evaluasi' => $total_eval,
            'layak' => $cat_counts['Layak'],
            'cukup_layak' => $cat_counts['Cukup Layak'],
            'tidak_layak' => $cat_counts['Tidak Layak']
        );
    }

    public function get_monthly_trend($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $this->db->select("MONTH(evaluated_at) as month, category, COUNT(*) as count");
        $this->db->from($this->table);
        $this->db->where("YEAR(evaluated_at)", (int)$year);
        $this->db->group_by("MONTH(evaluated_at), category");
        $rows = $this->db->get()->result_array();

        $months = array(
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        );

        $trend = array(
            'labels' => array_values($months),
            'layak' => array_fill(0, 12, 0),
            'cukup_layak' => array_fill(0, 12, 0),
            'tidak_layak' => array_fill(0, 12, 0),
            'total' => array_fill(0, 12, 0)
        );

        foreach ($rows as $r) {
            $m_idx = (int)$r['month'] - 1;
            $cnt = (int)$r['count'];
            if ($r['category'] === 'Layak') {
                $trend['layak'][$m_idx] += $cnt;
            } elseif ($r['category'] === 'Cukup Layak') {
                $trend['cukup_layak'][$m_idx] += $cnt;
            } elseif ($r['category'] === 'Tidak Layak') {
                $trend['tidak_layak'][$m_idx] += $cnt;
            }
            $trend['total'][$m_idx] += $cnt;
        }

        return $trend;
    }

    public function get_category_distribution()
    {
        $this->db->select("category, COUNT(*) as count");
        $this->db->from($this->table);
        $this->db->group_by("category");
        return $this->db->get()->result_array();
    }

    public function get_sector_distribution($limit = 10)
    {
        $this->db->select("u.bidang_usaha, COUNT(e.id) as count");
        $this->db->from($this->table . ' e');
        $this->db->join('umkm u', 'u.id = e.umkm_id', 'inner');
        $this->db->where("u.bidang_usaha IS NOT NULL", null, false);
        $this->db->where("TRIM(u.bidang_usaha) != ''", null, false);
        $this->db->group_by("u.bidang_usaha");
        $this->db->order_by("count", "DESC");
        $this->db->limit((int)$limit);
        return $this->db->get()->result_array();
    }

    public function get_stability_distribution()
    {
        $this->db->select("input_stabilitas, COUNT(*) as count");
        $this->db->from($this->table);
        $this->db->group_by("input_stabilitas");
        return $this->db->get()->result_array();
    }
}
