<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Iella_Event extends Iella_Request {
	
	public function emit($name)
	{
		if (!$name) return;
		$ci =& get_instance();
		$this->base = $ci->conf('iella_host_url');
		$events = Model_Iella_Event::find($name);
		
		foreach ($events as $event)
		{
			if (is_object($this->data))
			{
				$this->data->event = new stdClass();
				$this->data->event->name = $event->name;
				$this->data->event->method = $event->method;
			}
			
			parent::send($event->method);
			$event->data = $this->data;
			$event->raw_response = $this->raw_response;
			$event->response = $this->response;
		}
		
		return $events;
	}
	
}

?>