<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Whois_Check_Domainindex extends Model {
	
	const SOURCE_CRUNCHBASE		= 'crunchbase';
	const SOURCE_PRWEB			= 'prweb';
	const SOURCE_MARKETWIRED	= 'marketwired';
	const SOURCE_BUSINESSWIRE	= 'businesswire';
	const SOURCE_OWLER			= 'owler';
	const SOURCE_NEWSWIRE_CA	= 'newswire_ca';
	const SOURCE_MYNEWSDESK		= 'mynewsdesk';
	const SOURCE_PR_CO			= 'pr_co';
	const SOURCE_TOPSEOS		= 'topseos';

	protected static $__table = 'ac_nr_whois_check_domainindex';
	protected static $__primary = array('source_company_id', 'source');
}

?>