<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_PRN_Distribution_Asset extends Model {
	
	// uncomment to enable Raw_Data
	// use Raw_Data_Trait;
	
	// the table name in the database
	protected static $__table = 'nr_prn_distribution_asset';

	// @string    the primary key field
	// @string[]  the primary key fields
	protected static $__primary = 'hash';

	// should in-memory caching be enabled by default
	protected static $__cache_enabled = false;

	// how long data should be cached in-memory
	protected static $__cache_duration = 60;

	// the CI database class to use
	protected static $__db_class = 'default';

	// list of BLOB fields that use compression 
	protected static $__compressed = array();
	
}