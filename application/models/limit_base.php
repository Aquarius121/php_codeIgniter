<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

abstract class Model_Limit_Base extends Model {
	
	public $uses_calculated = false;
	
	public abstract function consume($context);
	public abstract function used();
	public abstract function available();
	public abstract function total();
	
}

?>