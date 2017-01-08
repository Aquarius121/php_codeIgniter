<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Activate_MDB_Plus_Controller extends Iella_Base {

	public function index()
	{
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$item = Model_Item::from_object($this->iella_in->item);
		$item_data = $item->raw_data();

		// find component set (for user id)
		$set_id = $component_item->component_set_id;
		$component_set = Model_Component_Set::find($set_id);
		if (!$component_set) return;

		$user = Model_User::find($component_set->user_id);
		$user_data = $user->raw_data();
		if (!$user_data) $user_data = new stdClass();

		if (isset($item_data->lifetime) && $item_data->lifetime)
		{
			$user_data->has_media_database_plus = true;
			if (isset($user_data->has_media_database_plus_until))
				unset($user_data->has_media_database_plus_until);
		}
		else
		{
			$date_until = Date::days($component_item->period);
			if (isset($user_data->has_media_database_plus_until))
				if (Date::utc($user_data->has_media_database_plus_until) > $date_until)
					$date_until = Date::days($component_item->period, $user_data->has_media_database_plus_until);
			$user_data->has_media_database_plus_until = $date_until->__toString();
		}
		
		$user->raw_data($user_data);
		$user->save();
	}
	
}

?>