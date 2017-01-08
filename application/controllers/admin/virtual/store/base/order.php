<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/base');

class VS_Order_Base extends VS_Base {

	const LISTING_CHUNK_SIZE = 20;
	public $title = 'Orders';

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/order/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = sprintf('%s/order', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
	
	protected function fetch_results($chunkination, $filter = null)
	{
		$request = $this->create_request();
		$request->data->chunk = new stdClass();
		$request->data->chunk->offset = $chunkination->offset();
		$request->data->chunk->size = $chunkination->chunk_size();
		$request->data->filter = new stdClass();

		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$request->data->filter->search = $filter_search;
		}
		
		if ($filter_user = (int) $this->input->get('filter_user'))
		{
			$this->create_filter_user($filter_user);
			$m_user = Model_User::find($filter_user);
			if (($vu = $m_user->virtual_user()) && 
				$m_user->virtual_source_id == Model_Virtual_Source::ID_PRESSRELEASECOM)
				$request->data->filter->user = $vu;
		}

		$response = $request->send('admin/store/order');
		if (!$response) return array();

		$chunkination->set_total($response->total);
		if ($chunkination->is_out_of_bounds())
		 	return array();
		
		foreach ($response->results as $k => &$result)
			$result = Model_Base::from_object($result);
		$this->process_results($response->results);
		return $response->results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/virtual/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/virtual/store/order/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>