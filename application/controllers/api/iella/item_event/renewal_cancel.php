<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Renewal_Cancel_Controller extends Iella_Base {
	
	public function index()
	{
		$component_set = Model_Component_Set::from_object($this->iella_in->component_set);
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$item = Model_Item::find($component_item->item_id);
		$user = Model_User::find($component_set->user_id);

		if (!$user->is_mail_blocked(Model_User_Mail_Blocks::PREF_ORDER))
		{				
			$en = new Email_Notification();
			$en->set_content_view('renewal_cancel');
			$en->set_data('dt_created', Date::utc($component_item->date_created));
			$en->set_data('item', $item);		
			$en->send($user);
		}
	}
	
}

?>