<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_News_Feed_Import extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_news_feed_import';
	protected static $__primary = 'hash';
	
}

?>