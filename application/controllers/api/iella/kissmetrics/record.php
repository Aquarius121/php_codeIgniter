<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Record_Controller extends Iella_Base {
	
	public function index()
	{
		if (empty($this->iella_in->identity)) return;
		$event_identity = $this->iella_in->identity;
		$event_name = $this->iella_in->name;
		$event_data = $this->iella_in->data;
		$kissmetrics = new KissMetrics_Process();
		$kissmetrics->identify($event_identity);
		$kissmetrics->record($event_name, $event_data);
	}
	
}

?>