<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Newsroom_Custom extends Model {
	
	protected static $__table = 'nr_newsroom_custom';
	protected static $__primary = 'company_id';

	use Raw_Data_Trait;

	public function content_type_labels($value = NR_DEFAULT)
	{
		if ($value === NR_DEFAULT)
		     return $this->raw_data(NR_DEFAULT, 'content_type_labels');
		else return $this->raw_data($value, 'content_type_labels');
	}

}

?>