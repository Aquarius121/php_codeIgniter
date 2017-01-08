<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Scrapped_Data_Migration extends Model {

	protected static $__table = 'ac_nr_scrapped_data_migration';

	const SOURCE_PRWEB			= 'prweb';
	const SOURCE_MARKETWIRED	= 'marketwired';
	const SOURCE_BUSINESSWIRE	= 'businesswire';
	const SOURCE_OWLER			= 'owler';
	const SOURCE_NEWSWIRE_CA	= 'newswire_ca';
	const SOURCE_MYNEWSDESK		= 'mynewsdesk';
	const SOURCE_PR_CO			= 'pr_co';
	const SOURCE_TOPSEOS		= 'topseos';
	const SOURCE_PRNEWSWIRE		= 'prnewswire';
}

?>