<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Rekap Statistik Kategori Kelayakan
     */
    public function get_category_recap($params = array())
    {
        $this->db->select("
            category,
            COUNT(*) as total_evaluasi,
            AVG(score) as avg_score,
            MIN(score) as min_score,
            MAX(score) as max_score
        ");
        $this->db->from('evaluations e');
        $this->apply_report_filters($params);
        $this->db->group_by('category');
        $rows = $this->db->get()->result_array();

        $total_all = 0;
        foreach ($rows as $r) {
            $total_all += (int)$r['total_evaluasi'];
        }

        $recap = array();
        foreach ($rows as $r) {
            $cnt = (int)$r['total_evaluasi'];
            $pct = $total_all > 0 ? round(($cnt / $total_all) * 100, 2) : 0;
            $recap[] = array(
                'category' => $r['category'],
                'total' => $cnt,
                'percentage' => $pct,
                'avg_score' => round((float)$r['avg_score'], 2),
                'min_score' => round((float)$r['min_score'], 2),
                'max_score' => round((float)$r['max_score'], 2)
            );
        }

        return array(
            'total_all' => $total_all,
            'items' => $recap
        );
    }

    /**
     * Laporan Rekap per Bidang Usaha
     */
    public function get_sector_report($params = array())
    {
        $this->db->select("
            u.bidang_usaha,
            COUNT(e.id) as total_evaluasi,
            SUM(CASE WHEN e.category = 'Layak' THEN 1 ELSE 0 END) as total_layak,
            SUM(CASE WHEN e.category = 'Cukup Layak' THEN 1 ELSE 0 END) as total_cukup_layak,
            SUM(CASE WHEN e.category = 'Tidak Layak' THEN 1 ELSE 0 END) as total_tidak_layak,
            AVG(e.score) as avg_score
        ");
        $this->db->from('evaluations e');
        $this->db->join('umkm u', 'u.id = e.umkm_id', 'inner');
        $this->apply_report_filters($params);
        $this->db->where("u.bidang_usaha IS NOT NULL", null, false);
        $this->db->where("TRIM(u.bidang_usaha) != ''", null, false);
        $this->db->group_by('u.bidang_usaha');
        $this->db->order_by('total_evaluasi', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Laporan Rekap per Kecamatan
     */
    public function get_district_report($params = array())
    {
        $this->db->select("
            u.kecamatan,
            COUNT(e.id) as total_evaluasi,
            SUM(CASE WHEN e.category = 'Layak' THEN 1 ELSE 0 END) as total_layak,
            SUM(CASE WHEN e.category = 'Cukup Layak' THEN 1 ELSE 0 END) as total_cukup_layak,
            SUM(CASE WHEN e.category = 'Tidak Layak' THEN 1 ELSE 0 END) as total_tidak_layak,
            AVG(e.score) as avg_score
        ");
        $this->db->from('evaluations e');
        $this->db->join('umkm u', 'u.id = e.umkm_id', 'inner');
        $this->apply_report_filters($params);
        $this->db->where("u.kecamatan IS NOT NULL", null, false);
        $this->db->where("TRIM(u.kecamatan) != ''", null, false);
        $this->db->group_by('u.kecamatan');
        $this->db->order_by('total_evaluasi', 'DESC');
        return $this->db->get()->result_array();
    }

    protected function apply_report_filters($params = array())
    {
        if (!empty($params['start_date'])) {
            $this->db->where('DATE(e.evaluated_at) >=', $params['start_date']);
        }
        if (!empty($params['end_date'])) {
            $this->db->where('DATE(e.evaluated_at) <=', $params['end_date']);
        }
        if (!empty($params['category'])) {
            $this->db->where('e.category', $params['category']);
        }
        if (!empty($params['fuzzy_model_id'])) {
            $this->db->where('e.fuzzy_model_id', (int)$params['fuzzy_model_id']);
        }
    }
}
