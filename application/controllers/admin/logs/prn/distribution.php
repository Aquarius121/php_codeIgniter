<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Distribution_Controller extends Admin_Base {

	public function index()
	{
		$this->vd->title[] = 'PRN Distribution';

		$provider = Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE;
		$provider = escape_and_quote($provider);

		$sql = "SELECT c.*, 
			{{ crp.* AS releasePlus USING Model_Content_Release_Plus }},
			{{ pd.* AS prn USING Model_PRN_Distribution }}
			FROM nr_content c 
			INNER JOIN nr_content_release_plus crp
			  ON crp.content_id = c.id 
			  AND crp.provider = {$provider}
			  AND crp.is_confirmed = 1
			  AND c.is_approved = 1
			  AND c.is_draft = 0
			  AND c.date_publish < DATE_ADD(UTC_TIMESTAMP(), INTERVAL 24 HOUR)
			LEFT JOIN nr_prn_distribution pd 
			  ON pd.content_id = c.id
			ORDER BY c.date_publish DESC
			LIMIT 200";

		$mContentArr = Model_Content::from_sql_all($sql);
		$this->vd->mContentArr = $mContentArr;

		$this->load->view('admin/header');
		$this->load->view('admin/logs/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/logs/prn/distribution');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function reset()
	{
		$id = $this->input->post('id');
		$mDist = Model_PRN_Distribution::find($id);
		if (!$mDist) return;
		$mDist->delete();
		$this->json(true);
	}

}
