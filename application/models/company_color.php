<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Company_Color extends Model {
	
	protected static $__table = 'nr_company_color';
	protected static $__primary = 'company_id';
	
	public static function find_or_create($company_id)
	{
		$instance = static::find($company_id);
		
		if (!$instance)
		{
			$instance = new static();
			$instance->company_id = $company_id;
		}

		return $instance;
	}

}

?>