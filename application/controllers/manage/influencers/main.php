<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Main_Controller extends Manage_Base {
	
	public function index()
	{
		$this->title = 'Insights (Beta)';
		$this->vd->beats = Model_Beat::list_all_beats_by_group();

		// $modal = new Modal();
		// $modal->set_id('alert-modal');
		// $modal->set_title('Create Alert');
		// $modal->set_content_view('manage/insights/alert-modal');
		// $modal->set_footer_view('manage/insights/alert-modal-footer');	
		// $this->add_eob($modal->render(400, 400));

		// $modal = new Modal();
		// $modal->set_id('insights-beta-modal');
		// $modal->set_header_view('manage/insights/beta-modal-header');
		// $modal->set_content_view('manage/insights/beta-modal');
		// $modal->set_footer_view('manage/insights/beta-modal-footer');
		// $modal->auto_show(true);
		// $this->add_eob($modal->render(400, 400));
		
		// $this->load->view('manage/header');
		// $this->load->view('manage/insights/header');
		// $this->load->view('manage/insights/index');
		// $this->load->view('manage/insights/footer');
		// $this->load->view('manage/footer');

		$common = $this->conf('website_url');
		$params = array('newsroom' => $this->newsroom->name);
		$params = http_build_query($params);
		$prefix = "/manage/influencers/";
		$url = "{$prefix}social_facebook?{$params}";
		$this->redirect($url, false);
	}

}