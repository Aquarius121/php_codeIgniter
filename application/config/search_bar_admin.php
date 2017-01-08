<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$context_uri = get_instance()->uri->uri_string;
$config['admin'] = array(

	'#^admin/publish/pr#' => array(
		'label' => 'Press Releases', 
		'uri' => 'admin/publish/pr/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/publish/news#' => array(
		'label' => 'News', 
		'uri' => 'admin/publish/news/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/publish/event#' => array(
		'label' => 'Events', 
		'uri' => 'admin/publish/event/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/publish/image#' => array(
		'label' => 'Images', 
		'uri' => 'admin/publish/image/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/publish/audio#' => array(
		'label' => 'Audio', 
		'uri' => 'admin/publish/audio/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/publish/video#' => array(
		'label' => 'Videos', 
		'uri' => 'admin/publish/video/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/contact/campaign#' => array(
		'label' => 'Campaigns', 
		'uri' => 'admin/contact/campaign/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/contact/list/customer#' => array(
		'label' => 'Lists', 
		'uri' => 'admin/contact/list/customer',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/contact/list/builder#' => array(
		'label' => 'Lists', 
		'uri' => 'admin/contact/list/builder',
	),

	'#^admin/contact/contact#' => array(
		'label' => 'Contacts', 
		'uri' => 'admin/contact/contact/all',
		'filters' => array(
			'company', 
			'site',
			'user', 
		),
	),

	'#^admin/companies#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/companies',
		'filters' => array(
			'site',
			'user', 
		),
	),

	'#^admin/nr_builder/crunch_base/auto_built_newsrooms_error_log#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/crunch_base/auto_built_newsrooms_error_log'
	),

	'#^admin/nr_builder/crunch_base/auto_built_newsrooms#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/crunch_base/auto_built_newsrooms'
	),
	
	'#^admin/nr_builder/crunch_base/all#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/crunch_base/all'
	),

	'#^admin/nr_builder/mynewsdesk/auto_built_newsrooms#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/mynewsdesk/auto_built_newsrooms'
	),

	'#^admin/nr_builder/mynewsdesk/auto_built_nrs_not_exported#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/mynewsdesk/auto_built_newsrooms'
	),
	
	'#^admin/nr_builder/mynewsdesk/all#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/mynewsdesk/all'
	),

	'#^admin/nr_builder/pr_co/auto_built_newsrooms#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/pr_co/auto_built_newsrooms'
	),

	'#^admin/nr_builder/pr_co/auto_built_nrs_not_exported#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/pr_co/auto_built_newsrooms'
	),
	
	'#^admin/nr_builder/pr_co/all#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/pr_co/all'
	),

	'#^admin/nr_builder/owler/auto_built_newsrooms#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/owler/auto_built_newsrooms'
	),

	'#^admin/nr_builder/owler/auto_built_nrs_not_exported#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/owler/auto_built_newsrooms'
	),
	
	'#^admin/nr_builder/owler/all#' => array(
		'label' => 'Companies', 
		'uri' => 'admin/nr_builder/owler/all'
	),

	'#^admin/users#' => array(
		'label' => 'Users', 
		'uri' => 'admin/users',
		'filters' => array(
			'site',
		),
	),

	'#^admin/settings/ip_block#' => array(
		'label' => 'IP Addresses', 
		'uri' => 'admin/settings/ip_block'
	),

	'#^admin/settings/fc_sites#' => array(
		'label' => 'FC Sites', 
		'uri' => 'admin/settings/fc_sites'
	),

	'#^admin/(virtual/)?store/(\d+/)?order#' => array(
		'label' => 'Orders', 
		'uri' => $context_uri,
		'filters' => array(
			'user', 
		),
	),

	'#^admin/(virtual/)?store/(\d+/)?coupon/active#' => array(
		'label' => 'Coupon', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?coupon/expired#' => array(
		'label' => 'Coupon', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?coupon/deleted#' => array(
		'label' => 'Coupon', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?plan/active#' => array(
		'label' => 'Plan', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?plan/deleted#' => array(
		'label' => 'Plan', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?item/active#' => array(
		'label' => 'Item', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?item/system#' => array(
		'label' => 'Item', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?item/deleted#' => array(
		'label' => 'Item', 
		'uri' => $context_uri,
	),

	'#^admin/(virtual/)?store/(\d+/)?transaction#' => array(
		'label' => 'Transactions', 
		'uri' => $context_uri,
		'filters' => array(
			'user', 
		),
	),

	'#^admin/(virtual/)?store/(\d+/)?renewals#' => array(
		'label' => 'Renewals', 
		'uri' => $context_uri,
		'filters' => array(
			'user', 
		),
	),

	'#^admin/writing/orders#' => array(
		'label' => 'Writing Orders', 
		'uri' => $context_uri,
		'filters' => array(
			'company', 
			'user', 
		),
	),

	'#^admin/contact/pitch_wizard_order/order#' => array(
		'label' => 'Pitch Order', 
		'uri' => 'admin/contact/pitch_wizard_order/order/all',
	),

	'#^admin/contact/pitch_wizard_order/all_list#' => array(
		'label' => 'Pitch List', 
		'uri' => 'admin/contact/pitch_wizard_order/all_list',
	),

);