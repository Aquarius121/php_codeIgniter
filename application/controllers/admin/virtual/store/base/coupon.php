<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/base');

class VS_Coupon_Base extends VS_Base {

	const LISTING_CHUNK_SIZE = 20;
	public $title = 'Coupons';

	public function index()
	{
		$this->redirect('admin/store/coupon/active');
	}

	public function active($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/coupon/active/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);

		$filter = new stdClass();
		$filter->has_expired = 0;
		$filter->is_deleted = 0;
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = sprintf('%s/coupon/active', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function expired($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/coupon/expired/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);
		
		$filter = new stdClass();
		$filter->has_expired = 1;
		$filter->is_deleted = 0;
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = sprintf('%s/coupon/expired', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function deleted($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/coupon/deleted/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);

		$filter = new stdClass();
		$filter->is_deleted = 1;
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = sprintf('%s/coupon/deleted', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	protected function load_items()
	{
		$request = $this->create_request();
		$request->data->chunk = new stdClass();
		$request->data->chunk->offset = 0;
		$request->data->chunk->size = 1000;
		$request->data->filter = new stdClass();
		$request->data->filter->is_listed = 1;
		$request->data->filter->is_disabled = 0;
		$response = $request->send('admin/store/item');
		$this->vd->items = $response->results;
		return $response->results;
	}
	
	protected function fetch_results($chunkination, $filter = 1)
	{
		$request = $this->create_request();
		$request->data->chunk = new stdClass();
		$request->data->chunk->offset = $chunkination->offset();
		$request->data->chunk->size = $chunkination->chunk_size();
		$request->data->filter = $filter;
		$request->data->filter->is_test = 0;

		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$request->data->filter->search = $filter_search;
		}

		$response = $request->send('admin/store/coupon');
		if (!$response) return array();

		$chunkination->set_total($response->total);
		if ($chunkination->is_out_of_bounds())
		 	return array();
		
		foreach ($response->results as $k => &$result)
			$result = Model_Coupon::from_object($result);
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
		$this->load->view('admin/virtual/store/coupon/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit($coupon_id = null)
	{
		$coupon = null;

		if ($coupon_id)
		{
			$request = $this->create_request();
			$request->data->id = (int) $coupon_id;
			$response = $request->send('admin/store/coupon/load');
			$coupon = isset($response->coupon) ? $response->coupon : null;
			
			if ($coupon) 
			{
				$coupon = Model_With_Raw_Data::from_object($coupon);
				$coupon->raw_data = $coupon->raw_data();
			}
		}

		if (!$coupon)
		{
			$coupon = new Model_With_Raw_Data();
		}

		$this->vd->coupon = $coupon;
		$this->load_items();
		
		$this->load->view('admin/header');
		$this->load->view('admin/virtual/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/virtual/store/coupon/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit_save()
	{
		$post = $this->input->post();

		if ($coupon_id = $this->input->post('coupon_id'))
		{
			$request = $this->create_request();
			$request->data->id = (int) $coupon_id;
			$response = $request->send('admin/store/coupon/load');
			$old_coupon = isset($response->coupon) ? $response->coupon : null;

			if ($old_coupon)
			{
				$old_coupon = Model_With_Raw_Data::from_db_object($old_coupon);

				// save the old coupon code so we 
				// know what it was for receipts
				$old_coupon_rd = $old_coupon->raw_data();
				if (!$old_coupon_rd) $old_coupon_rd = new Raw_Data();
				$old_coupon_rd->code = $old_coupon->code;
				$old_coupon->raw_data($old_coupon_rd);

				// expire coupon
				$old_coupon->code = null;
				$old_coupon->date_expires = Date::$now->format(Date::FORMAT_MYSQL);
				
				// save the changes to old coupon
				$request = $this->create_request();
				$request->data->coupon = $old_coupon;
				$request->send('admin/store/coupon/save');
			}
		}

		$request = $this->create_request();
		$request->data->code = $post['code'];
		$response = $request->send('admin/store/coupon/load');
		$code_exists = isset($response->coupon) ? (bool) $response->coupon : false;

		// POST with existing 
		// coupon code
		if ($code_exists)
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Coupon code exists.');
			$this->add_feedback($feedback);
			$this->redirect(sprintf('%s/coupon/active', 
				$this->store_base));
		}
		
		$coupon = new Model_With_Raw_Data();
		$coupon->code = strtoupper($post['code']);
		$coupon->date_expires = $post['date_expires'];
		$coupon->is_one_time = $this->input->post('is_one_time');
		$c_raw_data = new stdClass();
		
		if ($this->input->post('minimum_cost'))
		{
			$c_raw_data->minimum_cost = (float) 
				$this->input->post('minimum_cost');
		}

		if ($this->input->post('percentage_discount'))
		{
			$c_raw_data->percentage_discount = (float) 
				$this->input->post('percentage_discount');
		}

		if ($this->input->post('fixed_discount'))
		{
			$c_raw_data->fixed_discount = (float) 
				$this->input->post('fixed_discount');
		}

		if ($this->input->post('item_restriction'))
		{
			foreach ($post['item_restriction'] as $c => $item_r) 
				if (empty($item_r)) unset($post['item_restriction'][$c]);
				else $post['item_restriction'][$c] = (int) $post['item_restriction'][$c];
			if (count($post['item_restriction']))
				$c_raw_data->item_restriction = $post['item_restriction'];
		}			

		if ($this->input->post('item_list') && $this->input->post('item_price'))
		{
			$items = array();
			foreach ($post['item_list'] as $c => $item) 
				if (!empty($post['item_list'][$c]) && !empty($post['item_price'][$c]))
					$items[(int) $post['item_list'][$c]] = (float) $post['item_price'][$c];	
			if (count($items))
				$c_raw_data->item_static_cost = $items;
		}
			
		$coupon->raw_data($c_raw_data);
		// save the changes to new coupon
		$request = $this->create_request();
		$request->data->coupon = $coupon;
		$request->send('admin/store/coupon/save');

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Coupon saved.');
		$this->add_feedback($feedback);
		$this->redirect(sprintf('%s/coupon/active', 
			$this->store_base));
	}

	public function code_check()
	{
		$code = $this->input->post('code');
		$request = $this->create_request();
		$request->data->code = $code;
		$response = $request->send('admin/store/coupon/load');
		$coupon = isset($response->coupon) ? $response->coupon : null;
		$this->json(array('available' => (!$coupon || 
			$coupon->id == $this->input->post('coupon_id'))));
	}

	public function restore($coupon_id)
	{
		$request = $this->create_request();
		$request->data->id = (int) $coupon_id;
		$request->send('admin/store/coupon/restore');

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Coupon restored.');
		$this->add_feedback($feedback);
		$this->redirect(sprintf('%s/coupon/active', 
			$this->store_base));
	}

	public function delete($coupon_id)
	{
		if ($this->input->post('confirm'))
		{
			$request = $this->create_request();
			$request->data->id = (int) $coupon_id;
			$request->send('admin/store/coupon/delete');
						
			// load feedback message for the user
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Coupon deleted.');
			$this->add_feedback($feedback);
			$this->redirect(sprintf('%s/coupon/deleted', 
				$this->store_base));
		}
		else
		{
			// load confirmation feedback 
			$this->vd->coupon_id = $coupon_id;
			$feedback_view = 'admin/virtual/store/coupon/partials/coupon_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($coupon_id);
		}
	}
	
}

?>