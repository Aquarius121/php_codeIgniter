<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Blog_Feed_Import extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_blog_feed_import';
	protected static $__primary = 'hash';
	
}

?>