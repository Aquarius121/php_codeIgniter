<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class View_Controller extends Admin_Base {

	public function index($user_id = null)
	{
		if ($this->input->post('save'))
		{
			if (!($user = Model_User::find($user_id)))
			{
				$user = Model_User::create();
				$pass = Model_User::generate_password();
				$user->set_password($pass);
			}
			
			$email = strtolower($this->input->post('email'));
			$is_enabled = $this->input->post('is_enabled');
			$is_admin = $this->input->post('is_admin');
			$notes = value_or_null($this->input->post('notes'));
			$first_name = value_or_null($this->input->post('first_name'));
			$last_name = value_or_null($this->input->post('last_name'));
			$is_reseller = $this->input->post('is_reseller');
			$reseller_priv = $this->input->post('reseller_priv');
			$raw_data = $this->input->post('raw_data');
			
			// if we have duplicate email address then set null
			// and let admin know that the user must be updated
			if (($other_user = Model_User::find_email($email)) && 
			    $other_user->id != $user->id)
			{
				$email = null;
				// load feedback message for the user
				$feedback_view = 'admin/users/partials/duplicate_email_feedback';
				$feedback = $this->load->view($feedback_view, null, true);
				$this->add_feedback($feedback);
			}
			
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->email = $email;
			$user->is_admin = $is_admin;
			$user->is_reseller = $is_reseller;

			if ($user->is_virtual())
			{
				if (($vu = $user->virtual_user()))
				{
					$vs = $vu->virtual_source();
					$iella = Virtuals_Callback_Iella_Request::create($vs);
					$iella->data->virtual_user = $vu;
					$iella->data->is_enabled = $is_enabled;
					$iella->data->is_verified = 1;
					$iella->send('user_event/status');
				}
			}
			else
			{
				$user->is_enabled = $is_enabled;
				$user->is_verified = 1;
			}
			
			$user->notes = $notes;
			$user->raw_data = $raw_data;
			$user->save();
			
			if ($is_reseller)
			{
				if (!$reseller_priv) $reseller_priv = Model_Reseller_Details::PRIV_ADMIN_EDITOR;
				if (!($r_details = Model_Reseller_Details::find($user->id)))
					$r_details = new Model_Reseller_Details();
				$r_details->user_id = $user->id;
				$r_details->editing_privilege = $reseller_priv;
				$r_details->save();
			}
			
			$ac_class = (array) $this->input->post('ac_class');
			$ac_amount = (array) $this->input->post('ac_amount');
			$ac_expires = (array) $this->input->post('ac_expires');
			
			foreach ($ac_class as $k => $class)
			{
				if ($ac_amount[$k] <= 0) continue;
				$held = Credit::construct_held($class);
				$held->user_id = $user->id;
				$held->amount_total = $ac_amount[$k];
				$held->date_expires = $ac_expires[$k];
				$held->save();
			}

			if ($this->input->post('deactivate_plan'))
			{
				// terminate any existing subscription
				$terminator = new Subscription_Terminator();
				$terminator->cancel_all($user->id, false);

				// deactivate the current plan
				$m_user_plan = $user->m_user_plan();
				if ($m_user_plan) $m_user_plan->deactivate(true);
			}

			if ($this->input->post('unblock_billing_change'))
			{
				// remove billing update, permit new update
				if ($ulbu = Model_User_Last_Billing_Update::find($user->id))
					$ulbu->delete();

				// mark any billing failures as safe
				Model_Bill_Failure::mark_safe_user($user);
				Model_Bill_Failure::mark_safe_addr($user->remote_addr);
			}
			
			$give_plan_period_repeat_count = $this->input->post('give_plan_period_repeat_count');
			$give_plan_period = $this->input->post('give_plan_period');
			$give_plan_item_id = $this->input->post('give_plan_item_id');
			
			if ($plan_item = Model_Item::find($give_plan_item_id))
			{
				if ($plan_item->type != Model_Item::TYPE_PLAN) throw new Exception();
				if ($plan_item->is_disabled) throw new Exception();
				$item_data = $plan_item->raw_data();
				$plan = Model_Plan::find($item_data->plan_id);
				
				if ($give_plan_period)
				     $period = $give_plan_period;
				else $period = $plan->period;
					
				if ($give_plan_period_repeat_count)
				     $period_repeat_count = $give_plan_period_repeat_count;
				else if (isset($item_data->period_repeat_count))
				     $period_repeat_count = $item_data->period_repeat_count;
				else $period_repeat_count = 1;
				
				// terminate any existing subscription
				$terminator = new Subscription_Terminator();
				$terminator->cancel_all($user->id, false);
				
				$component_set = new Model_Component_set();
				$component_set->user_id = $user->id;
				$component_set->is_legacy = 0;
				$component_set->save();
				
				$component_item = Model_Component_Item::create();
				$component_item->component_set_id = $component_set->id;
				$component_item->item_id = $plan_item->id;
				$component_item->date_expires = Date::days($period)->format(Date::FORMAT_MYSQL);
				$component_item->date_termination = Date::days($period_repeat_count * $period)->format(Date::FORMAT_MYSQL);
				$component_item->period_repeat_count = $period_repeat_count;
				$component_item->period = $period;
				$component_item->price = 0;
				$component_item->is_auto_renew_enabled = 0;
				if (@$item_data->is_renewable)
					$component_item->is_renewable = 1;
				$component_item->quantity = 1;
				$component_item->save();
				
				// activate plan immediately
				$component_item->trigger();
			}
			
			// load feedback message for the user
			$feedback_view = 'admin/users/partials/save_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			
			// redirect back to the user page 
			$this->redirect("admin/users/view/{$user->id}");
		}
		
		if (!($user = Model_User::find($user_id)))
			$user = new Model_User();
		$this->vd->user = $user;

		if ($user->id) 
			Admo::save_recent_user($user->id);
		
		if ($user->id)
		     $this->title = $user->name();
		else $this->title = 'New User';
		
		if ($user->id)
		{
			$this->vd->credit_data = new stdClass();
			$this->vd->credit_data->pr_premium = $user->pr_credits_premium_stat();
			$this->vd->credit_data->pr_basic = $user->pr_credits_basic_stat();
			$this->vd->credit_data->email = $user->email_credits_stat();
			$this->vd->credit_data->newsroom = $user->newsroom_credits_stat();
			$this->vd->credit_data->writing = $user->writing_credits_stat();

			$pr_premium_held = Model_Limit_PR_Held::find_collection($user, Model_Content::PREMIUM);
			$pr_premium_held = $pr_premium_held->collection();
			$this->vd->credit_data->pr_premium->held = $pr_premium_held;
			
			$pr_basic_held = Model_Limit_PR_Held::find_collection($user, Model_Content::BASIC);
			$pr_basic_held = $pr_basic_held->collection();
			$this->vd->credit_data->pr_basic->held = $pr_basic_held;
			
			$email_held = Model_Limit_Email_Held::find_collection($user);
			$email_held = $email_held->collection();
			$this->vd->credit_data->email->held = $email_held;
			
			$newsroom_held = Model_Limit_Newsroom_Held::find_collection($user);
			$newsroom_held = $newsroom_held->collection();
			$this->vd->credit_data->newsroom->held = $newsroom_held;
			
			$writing_held = Model_Limit_Writing_Held::find_collection($user);
			$writing_held = $writing_held->collection();
			$this->vd->credit_data->writing->held = $writing_held;

			$this->vd->credit_data->common = new stdClass();
			foreach (Credit::list_common_types() as $type)
				$this->vd->credit_data->common->{$type} = 
					Model_Limit_Common_Held::find_collection($user, $type)
						->collection();
			
			$this->vd->reseller_details = Model_Reseller_Details::find($user->id);
			$content_types = sql_in_list(Model_Content::internal_types());
			$sql = "SELECT COUNT(1) AS count FROM nr_content c 
				INNER JOIN nr_company cm ON cm.user_id = ?
				AND c.company_id = cm.id 
				AND c.is_published = 1
				WHERE c.type IN ({$content_types})";
			$dbr = $this->db->query($sql, array($user->id));
			$this->vd->published_count = $dbr->row()->count;
			
			$sql = "SELECT COUNT(1) AS count FROM nr_campaign c 
				INNER JOIN nr_company cm ON cm.user_id = ?
				AND c.company_id = cm.id";
			$dbr = $this->db->query($sql, array($user->id));
			$this->vd->campaign_count = $dbr->row()->count;
			
			$sql = "SELECT COUNT(1) AS count 
				FROM nr_company cm WHERE cm.user_id = ?";
			$dbr = $this->db->query($sql, array($user->id));
			$this->vd->companies_count = $dbr->row()->count;

			// active plan details
			$this->vd->active_user_plan = $user->m_user_plan();
			$this->vd->active_plan = $user->m_plan();
			$this->vd->active_plan_ci = $user->plan_renewal_ci();

			// user last billing update?
			$this->vd->ulbu = Model_User_Last_Billing_Update::find($user->id);

			// has the user been blocked from billing?
			$this->vd->has_bill_block = Model_Bill_Failure::has_bill_block_user($user)
				|| Model_Bill_Failure::has_bill_block_addr($user->remote_addr);
		}
		
		$plans = Model_Plan::find_all();
		$this->vd->plans = array();
		foreach ($plans as $plan)
			$this->vd->plans[$plan->id] = $plan;
		
		$criteria = array();
		$criteria[] = array('type', Model_Item::TYPE_PLAN);
		$criteria[] = array('is_disabled', 0);
		$criteria[] = array('is_listed', 1);
		$plan_items = Model_Item::find_all($criteria);
		$this->vd->plan_items = array();
		
		foreach ($plan_items as $item)
		{
			$raw_data = $item->raw_data();
			$item->plan_id = $raw_data->plan_id;
			
			// check for the existence of a plan for this item
			if (!isset($this->vd->plans[$item->plan_id])) continue;
			
			$this->vd->plan_items[$item->id] = $item;
			if (isset($raw_data->period_repeat_count))
			     $item->period_repeat_count = $raw_data->period_repeat_count;
			else $item->period_repeat_count = null;
			if (isset($raw_data->period))
			     $item->period = $raw_data->period;
			else $item->period = $this->vd->plans[$item->plan_id]->period;	
			$item->is_renewable = (bool) @$raw_data->is_renewable;
		}
		
		$this->load->view('admin/header');
		$this->load->view('admin/users/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/users/view');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function reset($user_id = null)
	{
		if (!$this->input->post('confirm')) return;
		if (!($user = Model_User::find($user_id)))
			$this->json(null);
		$password = Model_User::generate_password();
		$user->set_password($password);
		$user->save();

		if ($user->is_virtual() && ($vu = $user->virtual_user()))
		{
			$vs = $vu->virtual_source();
			$iella = Virtuals_Callback_Iella_Request::create($vs);
			$iella->data->virtual_user = $vu;
			$iella->data->password = $password;
			$iella->send('user_event/password');
		}
		
		$response = new stdClass();
		$response->password = $password;
		$this->json($response);
	}

	public function remove_held_credits()
	{
		$held_class = $this->input->post('held_class');
		$held_data = json_decode($this->input->post('held_data'));

		// attempt to find the model 
		// dynamically based on class
		$fn_find_id = "{$held_class}::find_id";
		$model = call_user_func($fn_find_id, $held_data->id);
		if (!$model) return;

		// delete the credit
		$model->delete();

		$res = array('success' => true);
		$this->json($res);
	}
	
}