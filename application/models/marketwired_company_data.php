<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_MarketWired_Company_Data extends Model {

	protected static $__table = 'ac_nr_marketwired_company_data';
	protected static $__primary = 'marketwired_company_id';

	const SOCIAL_NOT_CHECKED = 'not_checked';
	const SOCIAL_VALID = 'valid';
	const SOCIAL_INVALID = 'invalid';
	
}

?>