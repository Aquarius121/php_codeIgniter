<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Auto_Built_Verified_Export extends Model {
	
	const FILTER_EXPORT_SELECTED = 'selected'; 
	const FILTER_EXPORT_ALL_NOT_EXPORTED = 'all_not_exported';
	const FILTER_EXPORT_ALL_ALREADY_EXPORTED = 'all_already_exported';

	protected static $__table = 'ac_nr_auto_built_verified_export';
	protected static $__primary = 'id';
}

?>