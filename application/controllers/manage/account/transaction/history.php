<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class History_Controller extends Manage_Base {

	const LISTING_CHUNK_SIZE = 10;	
	public $title = 'Transactions';

	public function index($chunk = 1)
	{
		$user = Auth::user();
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('manage/account/transaction/history/-chunk-');
		$chunkination->set_url_format($url_format);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS t.* FROM co_transaction t
			LEFT JOIN co_order o ON t.order_id = o.id
			WHERE t.user_id = {$user->id}
			ORDER BY t.date_created DESC
			{$limit_str}";
			
		$db_result = $this->db->query($sql);
		$results = Model_Transaction::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$this->vd->show_account_menu = true;
		$this->vd->user_email = Auth::user()->email;
		$this->vd->transactions = $results;
		$this->vd->chunkination = $chunkination;
		$chunkination->set_total($total_results);
				
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'manage/account/transaction/history';
			$this->redirect(gstring($url));
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/transaction/history');
		$this->load->view('manage/footer');
	}
	
}

?>
