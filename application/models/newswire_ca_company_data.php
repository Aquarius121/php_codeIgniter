<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Newswire_CA_Company_Data extends Model {

	protected static $__table = 'ac_nr_newswire_ca_company_data';
	protected static $__primary = 'newswire_ca_company_id';

	const SOCIAL_NOT_CHECKED = 'not_checked';
	const SOCIAL_VALID = 'valid';
	const SOCIAL_INVALID = 'invalid';

	const WEBSITE_SOURCE_NONE						= 'none';
	const WEBSITE_SOURCE_ORG_PROFILE 				= 'org_profile'; // pr page, right side area
	const WEBSITE_SOURCE_ORG_PROFILE_PAGE			= 'org_profile_page'; 
	const WEBSITE_SOURCE_WORD_MATCHING 				= 'word_matching'; 
	const WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING = 'email_domain_word_matching'; 
	const WEBSITE_SOURCE_ABBREVIATION 				= 'abbreviation';
	const WEBSITE_SOURCE_DOMAIN_MATCHING 			= 'domain_matching';
	
}

?>