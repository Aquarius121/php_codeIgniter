<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Coupon_Controller extends Admin_Base {

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
		$url_format = gstring('admin/store/coupon/active/-chunk-');
		$chunkination->set_url_format($url_format);

		$filter = "date_expires > UTC_TIMESTAMP()";
		$filter = "{$filter} AND is_deleted = 0";
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/coupon/active';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function expired($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/coupon/expired/-chunk-');
		$chunkination->set_url_format($url_format);
		
		$filter = "date_expires < UTC_TIMESTAMP()";
		$filter = "{$filter} AND is_deleted = 0";
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/coupon/expired';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function deleted($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/coupon/deleted/-chunk-');
		$chunkination->set_url_format($url_format);

		$filter = "is_deleted = 1";
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/coupon/deleted';
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
			$search_fields = array('c.code');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*, cc.count
			FROM co_coupon c LEFT JOIN (
				SELECT coupon_id, COUNT(1) AS count 
				FROM co_component_set cs
				GROUP BY coupon_id
			) cc ON cc.coupon_id = c.id
			WHERE {$filter} 
			AND is_test = 0
			AND code IS NOT NULL
			ORDER BY c.id DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Coupon::from_db_all($query);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		return $results;
	}

	public function restore($id)
	{
		if (!$id) return;
		$coupon = Model_Coupon::find($id);
		$coupon->is_deleted = 0;
		$coupon->save();

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Coupon restored successfully.');		
		$this->add_feedback($feedback);
		$this->redirect('admin/store/coupon/deleted');
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/coupon/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit($id = null)
	{
		if ($this->input->post('save'))
			$this->save();

		if ($coupon = Model_Coupon::find($id))
		{
			$coupon->raw_data = $coupon->raw_data();
			$this->vd->coupon = $coupon;
		}

		$sql = "SELECT * FROM co_item 
			WHERE is_disabled = 0
			ORDER BY is_custom = 0 DESC, name ASC";
		$this->vd->items = Model_Item::from_sql_all($sql);
		
		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/coupon/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	protected function save()
	{
		$post = $this->input->post();

		if ($this->input->post('coupon_id') && $old_coupon = Model_Coupon::find($this->input->post('coupon_id')))
		{
			// save the old coupon code so we 
			// know what it was for receipts
			$old_coupon_rd = $old_coupon->raw_data();
			if (!$old_coupon_rd) $old_coupon_rd = new Raw_Data();
			$old_coupon_rd->code = $old_coupon->code;
			$old_coupon->raw_data($old_coupon_rd);

			// expire coupon
			$old_coupon->code = null;
			$old_coupon->date_expires = Date::$now->format(Date::FORMAT_MYSQL);
			$old_coupon->save();
		}

		// POST with existing coupon code
		if (Model_Coupon::find_code($post['code']))
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Coupon code exists.');
			$this->add_feedback($feedback);
			$this->redirect('admin/store/coupon');
		}
		
		$coupon = new Model_Coupon();		
		$coupon->code = strtoupper($post['code']);
		$coupon->date_expires = $post['date_expires'];
		$coupon->is_one_time = $this->input->post('is_one_time');
		$c_raw_data = new stdClass();
		
		if ($this->input->post('minimum_cost'))
			$c_raw_data->minimum_cost = (float) $this->input->post('minimum_cost');
		if ($this->input->post('percentage_discount'))
			$c_raw_data->percentage_discount = (float) $this->input->post('percentage_discount');
		if ($this->input->post('fixed_discount'))
			$c_raw_data->fixed_discount = (float) $this->input->post('fixed_discount');

		if ($this->input->post('item_restriction'))
		{
			foreach ($post['item_restriction'] as $c => $item_r) 
			{
				if (empty($item_r)) unset($post['item_restriction'][$c]);
				else $post['item_restriction'][$c] = (int) $post['item_restriction'][$c];
			}

			if (count($post['item_restriction']))
			{
				$c_raw_data->item_restriction = $post['item_restriction'];
			}
		}			

		if ($this->input->post('item_list') && $this->input->post('item_price'))
		{
			$items = array();
			foreach ($post['item_list'] as $c => $item) 
			{
				if (!empty($post['item_list'][$c]) && !empty($post['item_price'][$c]))
					$items[(int) $post['item_list'][$c]] = (float) $post['item_price'][$c];	
			}

			if (count($items))
			{
				$c_raw_data->item_static_cost = $items;
			}
		}
			
		$coupon->raw_data($c_raw_data);
		$coupon->save();

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Coupon saved successfully.');
		$this->add_feedback($feedback);
		$this->redirect('admin/store/coupon');
	}

	public function code_check()
	{
		$code = $this->input->post('code');
		$coupon = Model_Coupon::find_code($code);
		$this->json(array('available' => (!$coupon || 
			$coupon->id == $this->input->post('coupon_id'))));
	}

	public function delete($coupon_id)
	{
		if (!$coupon_id) return;
		$coupon = Model_Coupon::find($coupon_id);
		
		if ($this->input->post('confirm'))
		{
			$coupon->is_deleted = 1;
			$coupon->save();
						
			// load feedback message for the user
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Coupon deleted successfully.');
			$this->add_feedback($feedback);
			$this->redirect('admin/store/coupon');
		}
		else
		{
			// load confirmation feedback 
			$this->vd->coupon_id = $coupon_id;
			$feedback_view = 'admin/store/coupon/partials/coupon_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($coupon_id);
		}
	}
	
}

?>