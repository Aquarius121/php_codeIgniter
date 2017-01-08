<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Plan_Migration_Controller extends CLI_Base {
	
	protected $trace = false;
	
	public function index()
	{
		$this->ldb = LEGACY::database();
		
		while (true)
		{
			$sql = "SELECT upd.*, ic.item_id AS co_item_id 
				FROM user_package_deals upd INNER JOIN 
				users u ON u.id = upd.user_id INNER JOIN 
				{$this->db->database}.co_item_conversion ic
				ON upd.deal_id = ic.legacy_deal_id
				WHERE upd.is_migrated = 0 
				AND u.is_migrated = 1
				ORDER BY upd.id ASC LIMIT 1";
				
			if (!($deal = $this->ldb->query($sql)->row())) break;
			
			// test that the user is actually migrated
			if (!($user = Model_User::find($deal->user_id)))
				return $this->mark_as_migrated($deal);
			
			// check the existing user plan
			if ($ex_user_plan = $user->m_user_plan())
			{
				$ex_plan = Model_Plan::find($ex_user_plan->plan_id);
				// user already has a non-legacy plan so must have cancelled legacy
				if (!$ex_plan->is_legacy) return $this->mark_as_migrated($deal);
			}
			
			// terminate any existing subscription
			$terminator = new Subscription_Terminator();
			$terminator->cancel_all($user->id, false);
			
			$mcs = new Model_Component_Set();
			$mcs->update_event = 'cs_update_legacy';
			$mcs->user_id = $user->id;
			$mcs->is_legacy = 1;
			$mcs->save();
			
			$date_created = new DateTime($deal->start_date);
			$date_expires = new DateTime($deal->end_date);
			
			// grace period because UC is not exact
			$date_expires->modify('+1 day');
			
			// give full range over the day	
			$date_created->setTime(00, 00, 00);
			$date_expires->setTime(23, 59, 59);
			
			$mci = new Model_Component_Item();
			$mci->component_set_id = $mcs->id;
			$mci->item_id = $deal->co_item_id;
			$mci->date_created = $date_created->format(Date::FORMAT_MYSQL);
			$mci->date_expires = $date_expires->format(Date::FORMAT_MYSQL);
			$mci->date_termination = $date_expires->format(Date::FORMAT_MYSQL);
			$mci->quantity = 1;
			$mci->price = $deal->price;
			$mci->is_renewable = 1;
			// no order related to this set
			// so it will not do anything
			$mci->is_auto_renew_enabled = 1;
			$mci->save();
			
			if ($date_created <= Date::$now && 
			    $date_expires >= Date::$now)
				$events_return = $mci->trigger();
				
			// attempt to transfer credit usage to new system
			// * we are assuming here that it's the first event
			if (isset($events_return[0]->response->user_plan_id))
			{
				// fetch existing usage for press releases
				$user_plan_id = $events_return[0]->response->user_plan_id;
				$sql = "SELECT * FROM user_package_deal_details
					WHERE user_package_id = {$deal->id} AND used > 0
					AND type_id IN (1, 3)";
				$dbr = $this->ldb->query($sql);
					
				foreach ($dbr->result() as $details)
				{
					if ($details->type_id == 3)
					     $type = 'PREMIUM_PR';
					else $type = 'BASIC_PR';
					
					if (!($user_plan = Model_User_Plan::find($user_plan_id))) break;
					if (!($plan = Model_Plan::find($user_plan->plan_id))) break;		
								
					$criteria = array();
					$criteria[] = array('plan_id', $plan->id);
					$criteria[] = array('type', $type);
					if (!($plan_credit = Model_Plan_Credit::find($criteria))) break;
					$plan_credit_id = $plan_credit->id;
					
					$criteria = array();
					$criteria[] = array('user_plan_id', $user_plan_id);
					$criteria[] = array('plan_credit_id', $plan_credit_id);
					$user_plan_credit = Model_User_Plan_Credit::find($criteria);
					if (!$user_plan_credit) break;
					
					$user_plan_credit->used = $details->used;
					$user_plan_credit->save();
				}
			}
			
			$this->mark_as_migrated($deal);
			
			// output for initial migration
			if (!$this->trace) continue;
			$time = new DateTime();
			$time = $time->format('H:i:s');
			$this->console("[{$time}] migrated: #{$deal->id}");
			usleep(500000);
			
		}
	}
	
	public function trace()
	{
		$this->trace = true;
		$this->index();
	}
	
	protected function mark_as_migrated($deal)
	{
		// mark as migrated to new system
		$sql = "UPDATE user_package_deals 
			SET is_migrated = 1 WHERE id = {$deal->id}";
		$this->ldb->query($sql);
	}
	
}

?>