<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Bundled_Campaign extends Model {
	
	use Raw_Data_Trait;

	protected static $__table = 'nr_content_bundled_campaign';
	protected static $__primary = 'content_id';
	
}

?>