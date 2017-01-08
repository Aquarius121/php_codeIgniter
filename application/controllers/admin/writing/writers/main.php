<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Main_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 10;
	
	public $title = 'Writers';

	public function index($chunk = 1)
	{
		$filter = 1;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/writers/-chunk-');
		$chunkination->set_url_format($url_format);
		
		$response = Model_MOT_Writer::fetch($chunkination, $filter);
		
		if ($response->response == 'success!')
		{
			$results = $response->results;
			$total_results = $response->total_results;
		}
		
		$chunkination->set_total($total_results);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/writing/writers';
			$this->redirect(gstring($url));
		}
		
		$this->vd->status = "all";
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/writers/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
}

?>	