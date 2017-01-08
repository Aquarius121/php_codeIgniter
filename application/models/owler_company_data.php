<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Owler_Company_Data extends Model {

	protected static $__table = 'ac_nr_owler_company_data';
	protected static $__primary = 'owler_company_id';

	const SOCIAL_NOT_CHECKED = 'not_checked';
	const SOCIAL_VALID = 'valid';
	const SOCIAL_INVALID = 'invalid';
	
}

?>