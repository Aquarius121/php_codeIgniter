<?php

if (!isset($_SERVER['HTTP_HOST']))
	$_SERVER['HTTP_HOST'] = 'CLI';

// is profiling enabled?
define('CIL_PROFILER_ENABLED', false);

// load ci request profiler
require_once 'application/util/profiler.php';

// repair all input data
require_once 'application/util/utf8_safe.php';

// load all auto-loading functionality
require_once 'application/util/autoload.php';

// composer auto-loading
require_once 'vendor/autoload.php';

// load environment specific config
require_once 'application/util/functions.php';

// load environment specific config
require_once 'application/config/environment.php';

// store which rewrites have applied
$uri = &$_SERVER['REQUEST_URI'];
$puri = preg_replace('#\?.*$#s', null, $uri);

// capture the external url 
// so that it is accessible after
// any rewrites have been applied
// * warning: this will updated
// for internal requests only after
// CIL_Controller has _remap();
// * does not include query string
$env['requested_uri'] = $puri;
$env['rewrites'] = array();

// rewrite the uri internally (if needed)
foreach ($env['rewrite'] as $rewrite)
{
	if (preg_match($rewrite->pattern, $puri))
	{
		if (!empty($rewrite->name))
			$env['rewrites'][$rewrite->name] = true;
		$uri = gstring(preg_replace($rewrite->pattern,
		 	$rewrite->replace, $puri));
	}
}

// apply rewrites to profiled uri
CIL_Profiler::instance()->set_uri($uri);

// has a url rewrite applied?
function has_url_rewrite($name)
{
	global $env;
	return isset($env['rewrites'][$name]);
}

// the user agent of the browser/bot
$env['user_agent'] = @$_SERVER['HTTP_USER_AGENT'];

// the remote address of the user (or null for unknown)
$env['remote_addr'] = @$_SERVER['REMOTE_ADDR'];

// access to cookies in CIL
$env['cookies'] = @$_COOKIE;

// current working dir
$env['cwd'] = getcwd();

// [auto] the callable that handles
// errors instead of codeigniter
$env['ci_error_handler'] = null;

// set the protocol based on header from nginx
if (!function_exists('apache_request_headers'))
	{ function apache_request_headers() { return array(); }}
$env['headers'] = $apache_request_headers = Raw_Data::from(apache_request_headers());
if (isset($apache_request_headers['X-SSL-Protocol']) && 
	$apache_request_headers['X-SSL-Protocol'])
{
	$env['protocol'] = 'https';
	$env['ssl_enabled'] = true;
}

// convert keys to lowercase for consistent access
foreach ($env['headers'] as $k => $header)
	$env['headers'][strtolower($k)] = $header;

// set the real hostname when tunnel is used
// * tunnel can be used for all newsrooms too
if ($env['website_tunnel_host'] === $_SERVER['HTTP_HOST'])
     $env['host'] = $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_ACTUAL_HOST'];
else $env['host'] = $_SERVER['HTTP_HOST'];

// no domain match? session is limited to hostname only
if (!str_ends_with($env['host'], $env['session_domain']))
	$env['session_domain'] = $env['host'];

// the base url of the current hosted newsroom/website
$env['base_url'] = sprintf('%s://%s/', $env['protocol'], $env['host']);

// create LT and ST data_caches
// LT = Long Term Storage (Sessions Etc)
// ST = Short Term Cache (Content Etc)
require_once 'application/util/data_cache.php';
require_once 'application/util/data_cache_lt.php';
require_once 'application/util/data_cache_st.php';
Data_Cache_LT::$data_cache = new Data_Cache($env['data_cache']['lt']);
Data_Cache_ST::$data_cache = new Data_Cache($env['data_cache']['st']);
require_once 'application/util/data_cache_session_handler.php';

// set the version information (git commit)
$env['version'] = @file_get_contents('version');

// set the server id information (generated once)
$env['server_id'] = @file_get_contents('server-id');

// get, post, put, delete, head, etc
if (isset($_SERVER['REQUEST_METHOD']))
	$env['request_method'] = strtolower($_SERVER['REQUEST_METHOD']);

// memcache will keep the session active for this
Data_Cache_LT_Session_Handler::$session_duration = 
	$env['session_duration'];

// set a high memory limit
set_memory_limit('512M');

// define the environment from config
define('ENVIRONMENT', $env['environment']);

// define the non-null default value
define('NR_DEFAULT', $env['nr_default']);

// carriage return, new line
define('CRLF', "\r\n");

// set the default timezone
date_default_timezone_set($env['timezone']);

// set the default user agent 
ini_set('user_agent', $env['user_agent']);

// buffer out
ob_start();
