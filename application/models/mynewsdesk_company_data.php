<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_MyNewsDesk_Company_Data extends Model {

	protected static $__table = 'ac_nr_mynewsdesk_company_data';
	protected static $__primary = 'mynewsdesk_company_id';

	const SOCIAL_NOT_CHECKED = 'not_checked';
	const SOCIAL_VALID = 'valid';
	const SOCIAL_INVALID = 'invalid';

	const WEBSITE_SOURCE_NONE						= 'none';
	const WEBSITE_SOURCE_NEWSROOM	 				= 'newsroom'; // from newsroom of company
	const WEBSITE_SOURCE_WORD_MATCHING 				= 'word_matching'; 
	const WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING = 'email_domain_word_matching'; 
	const WEBSITE_SOURCE_ABBREVIATION 				= 'abbreviation';
	const WEBSITE_SOURCE_DOMAIN_MATCHING 			= 'domain_matching';
	
}

?>