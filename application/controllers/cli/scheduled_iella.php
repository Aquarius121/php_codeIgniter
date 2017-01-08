<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Scheduled_Iella_Controller extends CLI_Base {
	
	public function __construct()
	{
		parent::__construct();		
		$date = Date::utc()->format('Y_m_d');
		$format = 'scheduled_iella_%s';
		$this->log_file = sprintf($format, $date);
	}
	
	public function index()
	{
		while ($si_request = Model_Scheduled_Iella_Request::find_due())
		{
			$si_request->is_active = 1;
			$si_request->save();

			set_time_limit(300);
			$iella_request = new Iella_Request();
			$iella_request->data = json_decode($si_request->data);
			$iella_request->base = $si_request->url;
			$iella_request->send(null);
			$si_request->delete();
			$this->log($si_request);
		}
		
		while ($si_event = Model_Scheduled_Iella_Event::find_due())
		{
			$si_event->is_active = 1;
			$si_event->save();

			set_time_limit(300);
			$iella_event = new Iella_Event();
			$iella_event->data = json_decode($si_event->data);
			$iella_event->emit($si_event->name);
			$si_event->delete();
			$this->log($si_event);
		}
	}

	public function test()
	{
		while ($si_request = Model_Scheduled_Iella_Request::find_due())
		{
			$si_request->is_active = 1;
			$si_request->save();

			set_time_limit(300);
			$iella_request = new Iella_Request();
			$iella_request->data = json_decode($si_request->data);
			$iella_request->base = $si_request->url;
			$iella_request->send(null);
		}

		while ($si = Model_Scheduled_Iella_Request::find_active())
		{
			$si->is_active = 0;
			$si->save();
		}
		
		while ($si_event = Model_Scheduled_Iella_Event::find_due())
		{
			$si_event->is_active = 1;
			$si_event->save();

			set_time_limit(300);
			$iella_event = new Iella_Event();
			$iella_event->data = json_decode($si_event->data);
			$iella_event->emit($si_event->name);
		}

		while ($si = Model_Scheduled_Iella_Event::find_active())
		{
			$si->is_active = 0;
			$si->save();
		}
	}
	
}