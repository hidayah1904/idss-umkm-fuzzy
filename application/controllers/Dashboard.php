<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->active_menu = 'dashboard';
        $this->page_title = 'Dashboard IDSS UMKM';
    }

    public function index()
    {
        $year = $this->input->get('year', true);
        if (!$year || !is_numeric($year)) {
            $year = date('Y');
        }

        // Statistik aktual dari database
        $stats = $this->Evaluation_model->get_stats_counts();
        $category_dist = $this->Evaluation_model->get_category_distribution();
        $sector_dist = $this->Evaluation_model->get_sector_distribution(8);
        $stability_dist = $this->Evaluation_model->get_stability_distribution();
        $monthly_trend = $this->Evaluation_model->get_monthly_trend($year);

        // Evaluasi terbaru
        $recent_evaluations = $this->Evaluation_model->get_all(array(
            'limit' => 6,
            'offset' => 0,
            'order_by' => 'e.evaluated_at',
            'order_dir' => 'DESC'
        ));

        // Aktivitas terbaru
        $recent_activities = $this->Activity_log_model->get_recent(6);

        $data = array(
            'page_title' => 'Dashboard — IDSS UMKM Fuzzy Mamdani',
            'active_menu' => 'dashboard',
            'breadcrumbs' => array(
                array('label' => 'Home', 'url' => base_url('dashboard')),
                array('label' => 'Dashboard', 'url' => '')
            ),
            'stats' => $stats,
            'category_dist' => $category_dist,
            'sector_dist' => $sector_dist,
            'stability_dist' => $stability_dist,
            'monthly_trend' => $monthly_trend,
            'recent_evaluations' => $recent_evaluations,
            'recent_activities' => $recent_activities,
            'selected_year' => $year
        );

        $this->render('dashboard/index', $data);
    }

    /**
     * Endpoint API untuk update grafik secara AJAX jika diperlukan
     */
    public function chart_data()
    {
        $year = $this->input->get('year', true);
        if (!$year || !is_numeric($year)) {
            $year = date('Y');
        }

        $monthly_trend = $this->Evaluation_model->get_monthly_trend($year);
        $category_dist = $this->Evaluation_model->get_category_distribution();
        $sector_dist = $this->Evaluation_model->get_sector_distribution(8);
        $stability_dist = $this->Evaluation_model->get_stability_distribution();

        $this->json_response(array(
            'status' => true,
            'year' => $year,
            'monthly_trend' => $monthly_trend,
            'category_dist' => $category_dist,
            'sector_dist' => $sector_dist,
            'stability_dist' => $stability_dist
        ));
    }
}
