<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Component_Item extends Model {
	
	protected static $__table = 'co_component_item';
	
	public static function create()
	{
		$instance = new static();
		$instance->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
	public function item()
	{
		return Model_Item::find($this->item_id);
	}
	
	public function trigger($track = null)
	{
		$item = Model_Item::find($this->item_id);
		if (!$item->activate_event) return;
		
		$iella_event = new Iella_Event();
		$iella_event->data->item = $item->values();
		$iella_event->data->component_item = $this->values();
		$iella_event->data->track = $track;
		return $iella_event->emit($item->activate_event);
	}
	
	public function cancel($immediate = false, $use_cancel_events = true)
	{
		if ($immediate) $this->is_renewable = 0;
		$this->is_auto_renew_enabled = 0;
		$this->save();
		
		$component_set = Model_Component_Set::find($this->component_set_id);
		$item = Model_Item::find($this->item_id);
		
		// handle any gateway specific updates
		if ($use_cancel_events && $component_set->update_event)
		{
			$iella_event = new Iella_Event();
			$iella_event->data->item = $item->values();
			$iella_event->data->component_item = $this->values();
			$iella_event->data->component_set = $component_set->values();
			$iella_event->data->update_action = Model_Component_Set::UPDATE_CANCEL;
			$iella_event->emit($component_set->update_event);
		}
		
		if ($use_cancel_events)
		{
			// schedule cancel event for next run
			$event = new Scheduled_Iella_Event();
			$event->data->component_set = $component_set->values();
			$event->data->component_item = $this->values();
			$event->schedule('user_renewal_cancel');
		}
	}

	public static function reset_on_hold($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		$user = (int) $user;

		$cs_table = Model_Component_Set::__table();
		$ci_table = static::__table();

		$sql = "SELECT ci.* FROM {$ci_table} ci INNER JOIN
			{$cs_table} cs ON ci.component_set_id = cs.id
			WHERE cs.user_id = {$user} 
			AND ci.is_auto_renew_enabled = 1
			AND ci.is_on_hold > 0";

		$dbr = static::__db()->query($sql);
		$results = static::from_db_all($dbr);

		foreach ($results as $result)
		{
			$result->is_on_hold = 0;
			$result->save();
		}
	}
	
}

?>