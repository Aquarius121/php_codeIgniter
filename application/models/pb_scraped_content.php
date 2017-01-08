<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_PB_Scraped_Content extends Model {
	
	protected static $__table = 'nr_pb_scraped_content';
	protected static $__primary = 'content_id';

	const SOURCE_PRWEB			= 'prweb';
	const SOURCE_MARKETWIRED	= 'marketwired';
	const SOURCE_BUSINESSWIRE	= 'businesswire';
	const SOURCE_OWLER			= 'owler';
	const SOURCE_NEWSWIRE_CA	= 'newswire_ca';
	const SOURCE_MYNEWSDESK		= 'mynewsdesk';
	const SOURCE_PR_CO			= 'pr_co';
	const SOURCE_PRNEWSWIRE		= 'prnewswire';
	
}

?>