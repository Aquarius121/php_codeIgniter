<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_CB_Company_Data extends Model {

	protected static $__table = 'ac_nr_cb_company_data';
	protected static $__primary = 'company_id';

	const SOCIAL_NOT_CHECKED = 'not_checked';
	const SOCIAL_VALID = 'valid';
	const SOCIAL_INVALID = 'invalid';
	
}

?>