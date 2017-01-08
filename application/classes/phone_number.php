<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Phone_Number {
	
	protected $number;

	public function __construct($number)
	{
		$this->number = $number;
		$this->number = $this->raw();
	}

	public function raw()
	{
		return preg_replace('#[^0-9]#', null, $this->number);
	}

	public function formatted()
	{
		return preg_replace('#(\d+)(\d{3})(\d{4})$#', '$1-$2-$3', $this->number);
	}
	
}

?>