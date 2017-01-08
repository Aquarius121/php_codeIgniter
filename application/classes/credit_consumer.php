<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class Credit_Consumer {
	
	protected $held;
	protected $plan;
	
	public function set_held($held)
	{
		$this->held = $held;
	}
	
	public function set_plan($plan)
	{
		$this->plan = $plan;	
	}
	
	protected function sorted()
	{
		$sortable = array();
		
		if ($this->held && $this->held->available())
			foreach ($this->held->collection() as $held)
				if ($held->available()) $sortable[] = $held;
			
		if ($this->plan && $this->plan->available())
			$sortable[] = $this->plan;
		
		usort($sortable, function($a, $b) {
			if ($a->uses_calculated && 
			    $b->uses_calculated) return 0;
			if ($a->uses_calculated) return -1;
			if ($b->uses_calculated) return 1;
			$dt_a = Date::utc($a->date_expires);
			$dt_b = Date::utc($b->date_expires);
			if ($dt_a == $dt_b) return 0;
			return $dt_a > $dt_b ? 1 : -1;
		});
		
		return $sortable;
	}
	
}

?>