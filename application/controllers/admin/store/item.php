<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Item_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	const EVENT_ACTIVATE = 'item_activate_custom';
	const EVENT_ORDER = 'item_order_custom';

	public $title = 'Store Items';

	public function index()
	{
		$this->redirect('admin/store/item/active');
	}

	public function active($chunk = 1)
	{
		$filter = 'is_disabled = 0';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/item/active/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/item/active';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function deleted($chunk = 1)
	{
		$filter = 'is_disabled = 1';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/item/deleted/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{		
			$url = 'admin/store/item/deleted';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function system($chunk = 1)
	{
		$filter = 1;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/item/system/-chunk-');
		$chunkination->set_url_format($url_format);
		
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
					
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('i.name', 'i.comment');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS i.*
			FROM co_item i
			WHERE {$filter}
			AND i.is_listed = 1
			AND i.is_custom = 0
			AND i.is_disabled = 0
			ORDER BY i.id ASC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Item::from_db_all($query);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{		
			$url = 'admin/store/item/system';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
		
	protected function fetch_results($chunkination, $filter = 1)
	{
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
					
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('i.name', 'i.comment');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS i.*
			FROM co_item i
			WHERE {$filter}
			AND i.is_listed = 1
			AND i.is_custom = 1
			ORDER BY i.id DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Item::from_db_all($query);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

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
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/item/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function restore($item_id)
	{
		$item = Model_Item::find($item_id);
		if (!$item || !$item->is_custom) show_404();
		$item->is_disabled = 0;
		$item->save();

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Item restored successfully.');
		$this->add_feedback($feedback);
		$this->redirect('admin/store/item/deleted');
	}

	public function delete($item_id)
	{
		$item = Model_Item::find($item_id);
		if (!$item || !$item->is_custom) show_404();

		if ($this->input->post('confirm'))
		{
			$item->is_disabled = 1;
			$item->save();
						
			// load feedback message for the user
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Item deleted successfully.');
			$this->add_feedback($feedback);
			$this->redirect('admin/store/item');
		}
		else
		{
			// load confirmation feedback 
			$this->vd->item_id = $item->id;
			$feedback_view = 'admin/store/item/partials/item_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($item_id);
		}
	}

	public function edit($item_id = null)
	{		
		$item = Model_Item::find($item_id);
		
		if ($item && !$item->is_custom)
		{
			$feedback = new Feedback('error');
			$feedback->set_text('Editing a system item can cause issues. Be careful.');
			$this->use_feedback($feedback);
		}

		if (!$item)
		{
			$item = new Model_Item();
			$item->secret = substr(md5(microtime(true)), 0, 16);
			$item->activate_event = static::EVENT_ACTIVATE;
			$item->order_event = static::EVENT_ORDER;
			$item->comment = 'Custom Product';
		}

		$this->vd->item = $item;
		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/item/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit_save()
	{
		$item_id = $this->input->post('item_id');
		$item = Model_Item::find($item_id);
		
		if (!$item)
		{
			$item = new Model_Item();
			$item->is_custom = 1;
			$item->is_listed = 1;
		}

		$item->name = $this->input->post('name');
		$item->comment = $this->input->post('comment');
		$item->tracking = $this->input->post('tracking');
		$item->price = (float) $this->input->post('price');
		$item->order_event = $this->input->post('order_event');
		$item->activate_event = $this->input->post('activate_event');
		$item->secret = $this->input->post('secret');
		$item->raw_data = $this->input->post('raw_data');
		$item->raw_data($item->raw_data());
		$item->save();

		if (!$item->tracking) $item->tracking = $item->id;
		if (!str_starts_with($item->tracking, 'custom_'))
			$item->tracking = concat('custom_', $item->tracking);
		$item->save();

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The custom item has been saved successfully.');
		$this->add_feedback($feedback);
		$this->redirect('admin/store/item');
	}	
}

?>