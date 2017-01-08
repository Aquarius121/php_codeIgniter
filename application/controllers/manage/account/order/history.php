<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class History_Controller extends Manage_Base {

	const LISTING_CHUNK_SIZE = 10;
	public $title = 'Orders';

	public function index($chunk = 1)
	{
		$user = Auth::user();
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('manage/account/order/history/-chunk-');
		$chunkination->set_url_format($url_format);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM co_order 
			WHERE user_id = {$user->id} 
			ORDER BY date_created DESC
			{$limit_str}";
			
		$db_result = $this->db->query($sql);
		$results = Model_Order::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$this->vd->results = $results;
		$this->vd->chunkination = $chunkination;
		$chunkination->set_total($total_results);
				
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'manage/account/order/history';
			$this->redirect(gstring($url));
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/order/history');
		$this->load->view('manage/footer');
	}
	
}

?>
