<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$context_uri = get_instance()->uri->uri_string;
$config['manage'] = array(
   
   '#^manage/contact/campaign#' => array(
		'label' => 'Media Outreach Campaigns', 
		'uri' => 'manage/contact/campaign/all',
	),
	
	'#^manage/contact/(list/edit|contact|import)#' => array(
		'label' => 'Imported Contacts', 
		'uri' => 'manage/contact/contact/search',
	),

	'#^manage/contact/list#' => array(
		'label' => 'Contact Lists', 
		'uri' => 'manage/contact/list/search',
	),
	
	'#^manage/analyze/email/view/#' => array(
		'label' => 'Analytics Email Stats', 
		'uri' => 'manage/analyze/email/view/search',
	),

	'#^manage/analyze/email#' => array(
		'label' => 'Analytics Email Stats', 
		'uri' => 'manage/analyze/email',
	),
	
	'#^manage/analyze#' => array(
		'label' => 'Analytics Content', 
		'uri' => 'manage/analyze/content/search',
	),
	
	'#^manage/newsroom#' => array(
		'label' => 'Company Newsroom Contacts', 
		'uri' => 'manage/newsroom/contact/search',
	),
	
	'#^manage/(dashboard|publish)#' => array(
		'label' => 'Distribution Content', 
		'uri' => 'manage/publish/search',
	),
	
	'#^manage/overview/(dashboard|publish)#' => array(
		'label' => 'Distribution Content', 
		'uri' => 'manage/overview/publish/search',
	),
	
	'#^manage/overview/contact#' => array(
		'label' => 'Media Outreach Campaigns', 
		'uri' => 'manage/overview/contact/search',
	),
	
	'#^manage/companies#' => array(
		'label' => 'Companies', 
		'uri' => 'manage/companies',
	),
	
	'#^manage/companies/archive#' => array(
		'label' => 'Companies', 
		'uri' => 'manage/companies/archive',
	),

);