<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Plan_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	const DEFAULT_PERIOD = 30;

	const EVENT_PLAN_ACTIVATE = 'item_activate_plan';
	const EVENT_PLAN_ORDER = 'item_order_plan';

	const EVENT_CREDIT_ACTIVATE = 'item_activate_credit';
	const EVENT_CREDIT_ORDER = 'item_order_credit';

	public $title = 'Custom Plans';

	public function index()
	{
		$this->redirect('admin/store/plan/active');
	}

	public function active($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/plan/active/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, 'is_disabled = 0');
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/plan/active';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function deleted($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/plan/deleted/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, 'is_disabled = 1');
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{		
			$url = 'admin/store/plan/deleted';
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
			$search_fields = array('p.name', 'i.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS p.*,
			i.id AS item__id, 
			i.name AS item__name, 
			i.price AS item__price, 
			i.raw_data AS item__raw_data, 
			i.is_disabled AS item__is_disabled,
			i.secret AS item__secret
			FROM co_plan p
			INNER JOIN co_item i
			ON p.connected_item_id = i.id
			WHERE {$filter}
			AND i.is_listed = 1
			AND p.is_protected = 0
			ORDER BY p.id DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Plan::from_db_all($query, array(
			'item' => 'Model_Item',
		));

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
		$this->load->view('admin/store/plan/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function restore($plan_id)
	{
		$plan = Model_Plan::find($plan_id);
		if (!$plan) show_404();
		
		if ($item = Model_Item::find($plan->connected_item_id))
		{
			$item->is_disabled = 0;
			$item->save();
		}

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Plan restored successfully.');
		$this->add_feedback($feedback);
		$this->redirect('admin/store/plan/deleted');
	}

	public function delete($plan_id)
	{
		$plan = Model_Plan::find($plan_id);
		if (!$plan || $plan->is_protected) show_404();

		if ($this->input->post('confirm'))
		{
			if ($item = Model_Item::find($plan->connected_item_id))
			{
				$item->is_disabled = 1;
				$item->save();
			}
						
			// load feedback message for the user
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Plan deleted successfully.');
			$this->add_feedback($feedback);
			$this->redirect('admin/store/plan');
		}
		else
		{
			// load confirmation feedback 
			$this->vd->plan_id = $plan_id;
			$feedback_view = 'admin/store/plan/partials/plan_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($plan_id);
		}
	}

	public function edit($plan_id = null)
	{
		$credit_types = Credit::list_types();
		$plan_credits = array();

		// need to set to null first
		// because of __get() overloading
		$this->vd->plan_credits = null;
		$this->vd->plan_credits = & $plan_credits;
		$this->vd->plan = $plan = Model_Plan::find($plan_id);		

		if ($plan)
		{
			if ($plan->is_protected) show_404();
			$connected_item = Model_Item::find($plan->connected_item_id);
			if (!$connected_item) show_404();

			$this->vd->connected_item = $connected_item;
			$connected_item->data = $connected_item->raw_data();
			$plan_credits = Model_Plan_Credit::find_all_plan($plan->id);
		}

		foreach ($credit_types as $credit_type)
		{
			if (!isset($plan_credits[$credit_type]))
			{
				$plan_credit = new Model_Plan_Credit();
				$plan_credit->type = $credit_type;
				$plan_credits[$credit_type] = $plan_credit;
			}
		}

		if ($plan)
		{
			foreach ($credit_types as $credit_type)
			{
				$plan_credit = $plan_credits[$credit_type];
				$ex_credit_id = array($plan->id, $credit_type);
				$ex_credit = Model_Plan_Extra_Credit::find_id($ex_credit_id);
				if ($ex_credit && !$ex_credit->item()->is_disabled)
					$plan_credit->extra_item = $ex_credit->item();
			}
		}

		$plan_credits = array_merge(
			array_flip($credit_types), 
			$plan_credits);

		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/plan/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit_save()
	{
		$plan_id = $this->input->post('plan_id');
		$plan = Model_Plan::find($plan_id);

		if (!$plan)
		{
			$plan = new Model_Plan();
			$plan->period = static::DEFAULT_PERIOD;
			$plan->is_protected = 0;
			$plan->is_legacy = 0;
			$plan->save();
		}

		$plan->name = $this->input->post('plan_name');
		$plan->package = $this->input->post('access_level');
		$connected_item = Model_Item::find($plan->connected_item_id);

		if (!$connected_item)
		{
			$connected_item = new Model_Item();
			$connected_item->tracking = 'custom_plan';
			$connected_item->type = Model_Item::TYPE_PLAN;
			$connected_item->secret = md5(microtime(true));
			$connected_item->is_listed = 1;
			$connected_item->is_custom = 1;
			$connected_item->activate_event = static::EVENT_PLAN_ACTIVATE;
			$connected_item->order_event = static::EVENT_PLAN_ORDER;
			$connected_item->save();
		}

		$connected_item->comment = $this->input->post('store_item_comment');
		$connected_item->name = $this->input->post('store_item_name');
		$connected_item->price = (float) $this->input->post('price');

		$ci_raw_data = $connected_item->raw_data();
		if (!$ci_raw_data) $ci_raw_data = new stdClass();
		$ci_raw_data->plan_id = $plan->id;
		$ci_raw_data->period_repeat_count = (int) $this->input->post('period_repeat_count');
		$ci_raw_data->is_auto_renew_enabled = 1;
		$ci_raw_data->is_renewable = 1;		
		$connected_item->raw_data($ci_raw_data);
		$connected_item->save();
		
		$plan->connected_item_id = $connected_item->id;
		$plan->save();

		$_credit_extra_price = $this->input->post('credit_extra_price');
		$_credit_period      = $this->input->post('credit_period');
		$_credit_quantity    = $this->input->post('credit_quantity');
		$_credit_rollover    = $this->input->post('credit_rollover');

		$credit_types = Credit::list_types();
		$plan_credits = Model_Plan_Credit::find_all_plan($plan->id);

		foreach ($credit_types as $credit_type)
		{
			if (isset($plan_credits[$credit_type]))
			     $plan_credit = $plan_credits[$credit_type];
			else $plan_credit = new Model_Plan_Credit();
			$plan_credit->plan_id = $plan->id;
			$plan_credit->type = $credit_type;
			$plan_credit->available = (int) $_credit_quantity[$credit_type];

			if (isset($_credit_period[$credit_type]))
				$plan_credit->period = value_or_null($_credit_period[$credit_type]);

			if (Credit::has_rollover_support($credit_type))
				$plan_credit->is_rollover_to_held_enabled = (int) 
					($_credit_rollover[$credit_type] && !$plan_credit->period);

			// only create new if there is > 0 quantity
			if ($plan_credit->id || $plan_credit->available)
				$plan_credit->save();

			$ex_credit_id = array($plan->id, $credit_type);
			$ex_credit = Model_Plan_Extra_Credit::find_id($ex_credit_id);
			if (!$ex_credit) $ex_credit = new Model_Plan_Extra_Credit();
			$ex_credit->plan_id = $plan->id;
			$ex_credit->type = $credit_type;

			if (!($ex_credit_item = Model_Item::find($ex_credit->item_id)))
			{
				$default_item = Credit::item($credit_type);
				$credit_title = Credit::full_name($credit_type);

				$ex_credit_item = new Model_Item();
				$ex_credit_item->name = $credit_title;
				$ex_credit_item->tracking = concat('custom_',
					Credit::tracking_name($credit_type));
				$ex_credit_item->comment = 'Custom Plan Credit';
				$ex_credit_item->type = Model_Item::TYPE_CREDIT;
				$ex_credit_item->secret = md5(microtime(true));
				$ex_credit_item->activate_event = $default_item 
					? $default_item->activate_event 
					: static::EVENT_CREDIT_ACTIVATE;
				$ex_credit_item->order_event = $default_item 
					? $default_item->order_event 
					: static::EVENT_CREDIT_ORDER;
				$ex_credit_item->is_listed = 0;
				$ex_credit_item->is_custom = 1;

				$eci_raw_data = $default_item
					? $default_item->raw_data_object()
					: new stdClass();
				$eci_raw_data->type = $credit_type;
				$eci_raw_data->is_quantity_unlocked = true;
				$ex_credit_item->raw_data($eci_raw_data);
			}

			$ex_credit_item->price = $_credit_extra_price[$credit_type];
			$ex_credit_item->is_disabled = !$ex_credit_item->price;
			
			// only create a new item when price is defined
			if ($ex_credit_item->id || $ex_credit_item->price)
			{
				$ex_credit_item->save();
				$ex_credit->item_id = $ex_credit_item->id;
				$ex_credit->save();
			}
		}

		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The custom plan has been saved successfully.');
		$this->add_feedback($feedback);
		$this->redirect('admin/store/plan');
	}	
}

?>