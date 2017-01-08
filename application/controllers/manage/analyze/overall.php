<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('manage/analyze/stats_ui_base');

class Overall_Controller extends Manage_Base {

	protected $context;

	public function __construct()
	{
		parent::__construct();

		if (!$this->newsroom->is_active)
		{
			$this->session->set('upgrade-redirect', 
				$this->newsroom->url('manage/analyze/overall'));
			$this->session->set('upgrade-company',
				$this->newsroom->company_id);
			$this->redirect('manage/upgrade/newsroom');
		}

		$this->vd->title[] = 'Analytics';
		$this->vd->title[] = 'Newsroom Stats';
		$stats_hash = new Stats_Hash();
		$stats_hash->company = $this->newsroom->company_id;
		$this->context = $stats_hash->context();
	}
	
	public function report()
	{
		if (!$this->newsroom->is_active)
			$this->redirect('manage/upgrade/newsroom');
		
		$generate_url = 'manage/analyze/overall/report_generate';
		$generate_url = gstring($generate_url);
		$this->vd->generate_url = $generate_url;
		
		$return_url = 'manage/analyze/overall';
		$return_url = gstring($return_url);
		$this->vd->return_url = $return_url;
		
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/report-generate');
		$this->load->view('manage/footer');
	}
	
	public function report_generate()
	{
		$url = 'manage/analyze/overall/report_index';
		$url = $this->newsroom->url($url);
		$url = gstring($url);
		$report = new PDF_Generator($url);
		$report->generate();
		
		if ($this->input->post('indirect'))
			  $this->vd->download_url = $report->indirect();
		else $report->deliver();
		
		// indirect => load feedback (and download) message for the user
		$feedback_view = 'manage/partials/report-generated-feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
	}
	
	public function report_index()
	{
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->index('manage/analyze/report/overall');
	}
	
	public function report_geolocation() 
	{
		$view = 'manage/analyze/report/partials/geolocation';
		$_base = new Stats_UI_Base($this, $this->context);
		return $_base->geolocation($view, 12, 300, 12);
	}

	public function index()
	{
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->index('manage/analyze/overall');
	}
	
	public function geolocation() 
	{
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->geolocation(null, 5, 20);
	}

	public function report_world_map()
	{
		$this->vd->disable_zoom = true;
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->world_map();
	}

	public function report_us_states_map()
	{
		$this->vd->disable_zoom = true;
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->us_states_map();
	}

	public function world_map()
	{
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->world_map();
	}

	public function us_states_map()
	{
		$_base = new Stats_UI_Base($this, $this->context);
		$_base->us_states_map();
	}

}

?>