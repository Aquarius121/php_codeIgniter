<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Company extends Model {
	
	const SOURCE_NEWSWIRE		= 'newswire';
	const SOURCE_CRUNCHBASE		= 'crunchbase';
	const SOURCE_PRWEB			= 'prweb';
	const SOURCE_MARKETWIRED	= 'marketwired';
	const SOURCE_BUSINESSWIRE	= 'businesswire';
	const SOURCE_OWLER			= 'owler';
	const SOURCE_NEWSWIRE_CA	= 'newswire_ca';
	const SOURCE_MYNEWSDESK		= 'mynewsdesk';
	const SOURCE_PR_CO			= 'pr_co';
	const SOURCE_TOPSEOS		= 'topseos';
	const SOURCE_PRNEWSWIRE		= 'prnewswire';

	protected static $__table = 'nr_company';
	protected static $__primary = 'id';

	protected static $__allow_zero_id = true;
	
	public function newsroom()
	{
		return Model_Newsroom::from_company_model($this);
	}
	
	public function owner()
	{
		return Model_User::find($this->user_id);
	}

	public static function full_source($type)
	{
		$display = array(
			static::SOURCE_NEWSWIRE => 'Newswire',
			static::SOURCE_CRUNCHBASE => 'Crunchbase',
			static::SOURCE_PRWEB => 'PRWeb', 
			static::SOURCE_MARKETWIRED => 'MarketWired',
			static::SOURCE_BUSINESSWIRE => 'BusinessWire', 
			static::SOURCE_OWLER => 'Owler',
			static::SOURCE_NEWSWIRE_CA => 'Newswire.ca',
			static::SOURCE_MYNEWSDESK => 'MyNewsDesk',
			static::SOURCE_PR_CO => 'PR.Co',
			static::SOURCE_TOPSEOS => 'TopSEOs'
		);
		
		return @$display[$type];
	}

	public static function scraping_sources()
	{
		$sources = array(
			static::SOURCE_CRUNCHBASE,
			static::SOURCE_PRWEB,
			static::SOURCE_MARKETWIRED,
			static::SOURCE_BUSINESSWIRE, 
			static::SOURCE_OWLER,
			static::SOURCE_NEWSWIRE_CA,
			static::SOURCE_MYNEWSDESK,
			static::SOURCE_PR_CO,
			static::SOURCE_TOPSEOS
		);
		
		return $sources;
	}

	public static function scraping_source_tbl_prefix($type)
	{
		$tbl_prefix = array(
			static::SOURCE_CRUNCHBASE => 'ac_nr_cb_',
			static::SOURCE_PRWEB => 'ac_nr_prweb_', 
			static::SOURCE_MARKETWIRED => 'ac_nr_marketwired_',
			static::SOURCE_BUSINESSWIRE => 'ac_nr_businesswire_', 
			static::SOURCE_OWLER => 'ac_nr_owler_',
			static::SOURCE_NEWSWIRE_CA => 'ac_nr_newswire_ca_',
			static::SOURCE_MYNEWSDESK => 'ac_nr_mynewsdesk_',
			static::SOURCE_PR_CO => 'ac_nr_pr_co_',
			static::SOURCE_TOPSEOS => 'ac_nr_topseos_'
		);
		
		return @$tbl_prefix[$type];
	}

	public static function scraping_sources_inactive_by_default()
	{
		return array(static::SOURCE_PRWEB, static::SOURCE_NEWSWIRE_CA);
	}
	
}

?>