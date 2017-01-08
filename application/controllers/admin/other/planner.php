<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Planner_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 50;

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/other/planner/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);

		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/other/planner';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	protected function fetch_results($chunkination)
	{
		$limit_str = $chunkination->limit_str();
		$user_prefixes = Model_User::__prefixes('u', null, 
			array('id', 'first_name', 'last_name', 'email'));
		$sql = "SELECT SQL_CALC_FOUND_ROWS sp.id, sp.email,
			sp.date_created, sp.claim_user_id, sp.is_finished,
			{$user_prefixes}
			FROM nr_sales_planner sp LEFT JOIN nr_user u
			ON u.id = sp.claim_user_id 
			WHERE sp.step_max > 1
			ORDER BY sp.date_created DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Sales_Planner::from_db_all($query);
		$chunkination->set_total($this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count);
					
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		$this->vd->title[] = 'Planner';

		$this->load->view('admin/header');
		$this->load->view('admin/other/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/other/planner/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function claim($id, $reclaim = false)
	{
		$this->set_redirect('admin/other/planner');
		$sp = Model_Sales_Planner::find($id);
		if (!$sp) return;

		if ($sp->claim_user_id && !$reclaim)
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Planner has been claimed already.');
			$this->add_feedback($feedback);
			return;
		}
		else
		{
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Planner is now claimed.');
			$this->add_feedback($feedback);
		}

		$sp->claim_user_id = Auth::user()->id;
		$sp->save();
	}

	public function review($id)
	{
		$planner = Model_Sales_Planner::find($id);
		if (!$planner) show_404();
		
		$rdata = $planner->raw_data_object();
		$this->vd->planner = $planner;
		$this->vd->rdata = $rdata;

		$this->load->view('admin/header');
		$this->load->view('admin/other/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/other/planner/review');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

}
