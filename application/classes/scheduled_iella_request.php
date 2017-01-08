<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scheduled_Iella_Request extends Iella_Request {
	
	public function schedule($method, $when = null)
	{
		if (!$method) return;
		if (!$when) $when = Date::$now;
		
		$m_sir = new Model_Scheduled_Iella_Request();
		$m_sir->url = "{$this->base}{$method}";
		$m_sir->data = json_encode($this->data);
		$m_sir->date_execute = $when->format(Date::FORMAT_MYSQL);
		$m_sir->save();

		return $m_sir;
	}
	
}

?>