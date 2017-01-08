<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scheduled_Iella_Event extends Iella_Event {
	
	public function schedule($name, $when = null)
	{
		if (!$name) return;
		if (!$when) $when = Date::$now;
		
		$sie = new Model_Scheduled_Iella_Event();
		$sie->name = $name;
		$sie->data = json_encode($this->data);
		$sie->date_execute = $when->format(Date::FORMAT_MYSQL);
		$sie->save();
	}
	
}

?>