<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter 
{
	protected $or_builder = 0;
	protected $and_builder = 1;
	protected $db;
	
	public function __construct($db)
	{
		$this->db = $db;
	}
}

?>