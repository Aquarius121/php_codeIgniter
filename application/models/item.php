<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Item extends Model {
	
	use Raw_Data_Trait;
	
	const TYPE_PLAN    = 'PLAN';
	const TYPE_CREDIT  = 'CREDIT';
	
	protected static $__table = 'co_item';
	
	public static function find_slug($slug)
	{
		return static::find('slug', $slug);
	}
	
	public function is_valid_secret($secret)
	{
		if (!$this->secret) return true;
		if ($this->secret === $secret) return true;
		
		$unb64 = base64_decode($secret);
		$data = json_decode($unb64);
			
		if (!isset($data->d)) return false;
		if (!isset($data->h)) return false;
		
		$date = new DateTime($data->d);
		$hash = $data->h;
		
		if ($date < Date::$now) return false;
		if ($hash != $this->secret_hash($data->d)) return false;
		return true;
	}
		
	protected function secret_hash($date)
	{
		$text = sprintf('%s-%s', $date, $this->secret);
		$hash = substr(md5($text), 0, 16);
		return $hash;
	}
	
	public function generate_secret($days)
	{
		$data = new stdClass();
		$data->d = Date::days($days)->format('Y-m-d');
		$data->h = $this->secret_hash($data->d);
		return base64_encode(json_encode($data));
	}
	
	// this functionality should not
	// be defined here as we could add
	// more items later on that are also
	// exclusive - do not use this function
	public function is_exclusive()
	{
		if ($this->type === static::TYPE_PLAN)
			return true;
		return false;
	}
	
	public static function generate_slug($name)
	{
		return Slugger::create($name, 128);
	}

	// generate an order link (with optional secret)
	// * uses the perma-secret if none provided
	public function order_url($base_url = null, $secret = null, $quantity = 1)
	{
		if ($base_url === null) $base_url = 'order';
		if ($secret === null) $secret = $this->secret;
		if (!strlen($secret)) $secret = 's';
		
		return "{$base_url}/item/{$this->id}/{$secret}/{$quantity}";
	}

	// calculate distance between 2 renewals
	// using plan/credit defaults if not set
	public function renewal_distance()
	{
		$data = $this->raw_data();
		if (!isset($data->is_auto_renew_enabled)) return 0;
		if (!$data->is_auto_renew_enabled) return 0;

		if ($this->type === Model_Item::TYPE_PLAN && isset($data->plan_id))
		{
			// default to the period from the plan
			$plan = Model_Plan::find($data->plan_id);
			$period = $plan->period;
		}
		else
		{
			// assume that its a credit or uses the same period
			$period = Model_Setting::value('held_credit_period');
		}

		if (isset($data->period))
			$period = $data->period;
		if (isset($data->period_repeat_count))
			  $distance = $period * $data->period_repeat_count;
		else $distance = $period * 1;
		return $distance;
	}
	
}

?>