<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_PRN_Valid_Company extends Model {
	
	const SOURCE_PRWEB			= 'prweb';
	const SOURCE_NEWSWIRE_CA	= 'newswire_ca';
	const SOURCE_PRNEWSWIRE		= 'prnewswire';

	protected static $__table = 'ac_nr_prn_valid_company';
	protected static $__primary = array('source_company_id', 'source');
}

?>