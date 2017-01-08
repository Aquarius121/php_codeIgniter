<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'default';
$active_record = TRUE;
$db = array();

$db['default'] = array();
$db['default']['hostname'] = 'dbmaster.xena-private.newswire.com';
$db['default']['username'] = 'v2';
$db['default']['password'] = 'LY3xCb3yYwz3PWUh';
$db['default']['database'] = 'v2_main';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$db['stat'] = array();
$db['stat']['hostname'] = 'dbmaster.xena-private.newswire.com';
$db['stat']['username'] = 'v2';
$db['stat']['password'] = 'LY3xCb3yYwz3PWUh';
$db['stat']['database'] = 'v2_stat';
$db['stat']['dbdriver'] = 'mysqli';
$db['stat']['dbprefix'] = '';
$db['stat']['pconnect'] = FALSE;
$db['stat']['db_debug'] = FALSE;
$db['stat']['cache_on'] = FALSE;
$db['stat']['cachedir'] = '';
$db['stat']['char_set'] = 'utf8';
$db['stat']['dbcollat'] = 'utf8_general_ci';
$db['stat']['swap_pre'] = '';
$db['stat']['autoinit'] = TRUE;
$db['stat']['stricton'] = FALSE;

$db['scratch'] = array();
$db['scratch']['hostname'] = 'dbmaster.xena-private.newswire.com';
$db['scratch']['username'] = 'v2';
$db['scratch']['password'] = 'LY3xCb3yYwz3PWUh';
$db['scratch']['database'] = 'v2_scratch';
$db['scratch']['dbdriver'] = 'mysqli';
$db['scratch']['dbprefix'] = '';
$db['scratch']['pconnect'] = FALSE;
$db['scratch']['db_debug'] = FALSE;
$db['scratch']['cache_on'] = FALSE;
$db['scratch']['cachedir'] = '';
$db['scratch']['char_set'] = 'utf8';
$db['scratch']['dbcollat'] = 'utf8_general_ci';
$db['scratch']['swap_pre'] = '';
$db['scratch']['autoinit'] = TRUE;
$db['scratch']['stricton'] = FALSE;

$db['legacy'] = array();
$db['legacy']['hostname'] = 'dbmaster.xena-private.newswire.com';
$db['legacy']['username'] = 'v2';
$db['legacy']['password'] = 'LY3xCb3yYwz3PWUh';
$db['legacy']['database'] = 'freepr_inews';
$db['legacy']['dbdriver'] = 'mysqli';
$db['legacy']['dbprefix'] = '';
$db['legacy']['pconnect'] = FALSE;
$db['legacy']['db_debug'] = FALSE;
$db['legacy']['cache_on'] = FALSE;
$db['legacy']['cachedir'] = '';
$db['legacy']['char_set'] = 'utf8';
$db['legacy']['dbcollat'] = 'utf8_general_ci';
$db['legacy']['swap_pre'] = '';
$db['legacy']['autoinit'] = TRUE;
$db['legacy']['stricton'] = FALSE;

// load any local version of config
require 'database.local.php';
