<?php

$env = array();
$ENV = & $env;

// this is appended to newsroom name
// * revert to automatic when domain doesn't end with this
$env['session_domain'] = '.newswire.com';

// the length of the session cookie
$env['session_duration'] = 86400;

// the path of the session cookie
$env['session_path'] = '/';

// the name of the session cookie
$env['session_cookie'] = 'inw_session';

// this should almost always be UTC
// * changing this will break things
// * must be set for piwik too
$env['timezone'] = 'UTC';

// default user agent for HTTP requests
$env['user_agent'] = 'Newswire';

// a primitive default value that is non-null that can be used to detect "default" values
$env['nr_default'] = '^"931-]<~4#}3[z_-H6/4)c(()>>Dm49?y]<566Dah9":|>>B7|2[oJ3#nlE &2v';

// the ENVIRONMENT constant value
$env['environment'] = 'production';

// the tunnel hostname of the new newswire website
$env['website_tunnel_host'] = 'version25.newswire.com';

// the actual hostname of the newswire website
$env['website_host'] = 'www.newswire.com';

// if ssl is supported or not
$env['ssl_support'] = true;

// [auto] the protocol in use 
$env['protocol'] = 'http';

// [auto] if ssl is enabled or not
$env['ssl_enabled'] = false;

// [auto] the current host
$env['host'] = null;

// [auto] the current dir
$env['cwd'] = null;

// [auto] the base url 
$env['base_url'] = null;

// placeholder for rewrites
// * see environment.rewrite.php
$env['rewrite'] = array();

// data_cache servers pool
$env['data_cache'] = array();
$env['data_cache']['lt'] = array();
$env['data_cache']['st'] = array();
$env['data_cache']['lt'][] = array('db1.xena-private.newswire.com', 11211, false);
// $env['data_cache']['lt'][] = array('db2.xena-private.newswire.com', 11211, false);
$env['data_cache']['st'][] = array('127.0.0.1', 11212, false);

// include url rewriting patterns
require 'environment.rewrite.php';

// load any local version of config
require 'environment.local.php';

