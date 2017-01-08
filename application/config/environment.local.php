<?php

// this is appended to newsroom name
// * revert to automatic when domain doesn't end with this
$env['session_domain'] = '.dev1.zorka.newswire.com';

// the ENVIRONMENT constant value
$env['environment'] = 'development';

// the tunnel hostname of the new i-newswire website
$env['website_tunnel_host'] = 'v25.dev.zorka.newswire.com';

// // the actual hostname of the i-newswire website
$env['website_host'] = 'www.dev1.zorka.newswire.com';

// if ssl is supported or not
$env['ssl_support'] = true;

// data_cache servers pool
$env['data_cache'] = array();
$env['data_cache']['lt'] = array();
$env['data_cache']['st'] = array();
$env['data_cache']['lt'][] = array('127.0.0.1', 11211, false);
$env['data_cache']['st'][] = array('127.0.0.1', 11212, false);