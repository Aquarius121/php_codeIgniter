<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Report_Controller extends Admin_Base {

	public function index($content_id)
	{
		$generate_url = "admin/writing/orders/report/generate/{$content_id}";
		$this->vd->generate_url = $generate_url;
		$this->vd->return_url = null;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('manage/analyze/report-generate');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function generate($content_id)
	{
		$m_content = Model_Content::find($content_id);
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
		
		$response = new stdClass();
		$url = "manage/analyze/content/dist_index/{$content_id}";
		$url = $m_newsroom->url($url);
		$report = new PDF_Generator($url);
		$report->generate();
		$response->download_url = $report->indirect();
		$this->json($response);
	}
	
}

?>