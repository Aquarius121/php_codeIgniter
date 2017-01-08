<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/base');

class VS_Item_Base extends VS_Base {

	const LISTING_CHUNK_SIZE = 20;

	public $title = 'Custom Items';

	public function index()
	{
		$this->redirect(sprintf('%s/item/active', $this->store_base));
	}

	public function active($chunk = 1)
	{
		$filter = new stdClass();
		$filter->is_disabled = 0;
		$filter->is_custom = 1;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/item/active/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);

		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = sprintf('%/item/active', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function deleted($chunk = 1)
	{
		$filter = new stdClass();
		$filter->is_disabled = 1;
		$filter->is_custom = 1;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/item/deleted/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{		
			$url = sprintf('%/item/deleted', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function system($chunk = 1)
	{
		$filter = new stdClass();
		$filter->is_custom = 0;
		$filter->is_disabled = 0;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring(sprintf('%s/item/system/-chunk-', $this->store_base));
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{		
			$url = sprintf('%/item/system', $this->store_base);
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
		
	protected function fetch_results($chunkination, $filter)
	{
		$request = $this->create_request();
		$request->data->chunk = new stdClass();
		$request->data->chunk->offset = $chunkination->offset();
		$request->data->chunk->size = $chunkination->chunk_size();
		$request->data->filter = $filter;
		if (!isset($request->data->filter->is_custom))
			$request->data->filter->is_custom = 1;
		if (!isset($request->data->filter->is_listed))
			$request->data->filter->is_listed = 1;

		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$request->data->filter->search = $filter_search;
		}

		$response = $request->send('admin/store/item');
		if (!$response) return array();

		$chunkination->set_total($response->total);
		if ($chunkination->is_out_of_bounds())
		 	return array();
		
		foreach ($response->results as $k => &$result)
			$result = Model_Item::from_object($result);
		$this->process_results($response->results);
		return $response->results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$user_filter = new stdClass();
		$user_filter->site = $this->m_virtual_source->id;
		$this->vd->transfer_to_user_filter = $user_filter;

		$create_order_modal = new Modal();
		$create_order_modal->set_title('Create Order');
		$modal_view = 'admin/partials/transfer_to_user_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$create_order_modal->set_content($modal_content);
		$modal_view = 'admin/partials/transfer_create_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$create_order_modal->set_footer($modal_content);
		$this->add_eob($create_order_modal->render(500, 300));
		$this->vd->create_order_modal_id = $create_order_modal->id;

		$this->load->view('admin/header');
		$this->load->view('admin/virtual/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/virtual/store/item/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit($item_id = null)
	{		
		$item = null;

		if ($item_id)
		{
			$request = $this->create_request();
			$request->data->id = (int) $item_id;
			$response = $request->send('admin/store/item/load');
			$item = Raw_Data::from_object($response->item);
			if ($item && $item->event && !$item->order_event)
				$item->order_event = $item->event;
		}

		if ($item && !$item->is_custom)
		{
			$feedback = new Feedback('error');
			$feedback->set_text('Editing a system item can cause issues. Be careful.');
			$this->use_feedback($feedback);
		}

		if (!$item)
		{
			$item = new Raw_Data();
			$item->secret = substr(md5(microtime(true)), 0, 16);
			$item->comment = 'Custom Product';
		}

		$this->vd->item = $item;
		$this->load->view('admin/header');
		$this->load->view('admin/virtual/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/virtual/store/item/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit_save()
	{
		if ($item_id = $this->input->post('item_id'))
		{
			$request = $this->create_request();
			$request->data->id = (int) $item_id;
			$response = $request->send('admin/store/item/load');
			$item = Raw_Data::from_object($response->item);
		}
		else
		{
			$item = new Raw_Data();
			$item->is_custom = 1;
			$item->is_listed = 1;
		}

		$item->name = $this->input->post('name');
		$item->comment = $this->input->post('comment');
		$item->tracking = $this->input->post('tracking');
		$item->price = (float) $this->input->post('price');
		$item->order_event = $this->input->post('order_event');
		$item->event = $this->input->post('order_event');
		$item->secret = $this->input->post('secret');
		$item->raw_data = $this->input->post('raw_data');		

		$request = $this->create_request();
		$request->data->item = $item;
		$request->send('admin/store/item/save');

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The item has been saved.');
		$this->add_feedback($feedback);

		$this->redirect(sprintf('%s/item/active', 
			$this->store_base));
	}
	
	public function restore($item_id)
	{
		$request = $this->create_request();
		$request->data->id = (int) $item_id;
		$request->send('admin/store/item/restore');

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Item restored.');
		$this->add_feedback($feedback);
		$this->redirect(sprintf('%s/item/active', 
			$this->store_base));
	}

	public function delete($item_id)
	{
		if ($this->input->post('confirm'))
		{
			$request = $this->create_request();
			$request->data->id = (int) $item_id;
			$request->send('admin/store/item/delete');
						
			// load feedback message for the user
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Item deleted.');
			$this->add_feedback($feedback);
			$this->redirect(sprintf('%s/item/deleted', 
				$this->store_base));
		}
		else
		{
			// load confirmation feedback 
			$this->vd->item_id = $item_id;
			$feedback_view = 'admin/virtual/store/item/partials/item_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($item_id);
		}
	}

	public function order($item_id)
	{
		$request = $this->create_request();
		$request->data->id = (int) $item_id;
		$response = $request->send('admin/store/item/load');
		$item = Model_Item::from_object($response->item);

		$order_url = $item->order_url('order/reset');
		$user = Model_User::find($this->input->get('user'));
		$virtual_user = $user->virtual_user();
		$vuras = Virtual_User_Remote_Admo_Session::create($virtual_user);
		$this->redirect($vuras->url($order_url), false);
	}

}