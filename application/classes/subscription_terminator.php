<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Subscription_Terminator {
	
	// cancel all plan subscriptions
	public function cancel_all($user, $use_cancel_event = true)
	{
		$ci =& get_instance();
		if (!($user instanceof Model_User))
			$user = Model_User::find($user);
		if (!$user) throw new Exception();
		
		// update all existing plan components for this 
		// user and set them to not auto renew
		$sql = "SELECT ci.* FROM co_component_item ci 
			INNER JOIN co_component_set cs 
			ON ci.component_set_id = cs.id 
			AND (ci.is_renewable = 1 OR ci.is_auto_renew_enabled = 1)
			AND cs.user_id = ? 
			INNER JOIN co_item i 
			ON ci.item_id = i.id AND i.type = ?";
			
		$db_result = $ci->db->query($sql, 
			array($user->id, Model_Item::TYPE_PLAN));
		$ex_component_items = Model_Component_Item::from_db_all($db_result);
		foreach ($ex_component_items as $ex_component_item) 
			$ex_component_item->cancel(true, $use_cancel_event);
	}
	
}

?>