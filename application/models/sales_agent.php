<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Sales_Agent extends Model {

	protected static $__table = 'nr_sales_agent';
	
	public function name()
	{
		return trim(sprintf('%s %s', 
			$this->first_name, 
			$this->last_name));
	}
}

?>