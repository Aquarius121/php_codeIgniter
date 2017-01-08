<?php 

// the hostname pattern such that the newsroom 
// name can be extracted as the matched part
$config['host_pattern'] = '#^([a-z0-9\-]+)\.dev1\.zorka\.newswire\.com$#is';

// this is appended to newsroom name 
$config['host_suffix'] = '.dev1.zorka.newswire.com';

// the detached hostname pattern such that the newsroom 
// name can be extracted as the matched part
$config['detached_pattern'] = '#^detached-([a-f0-9]{16})\.dev1\.zorka\.newswire\.com$#is';

// this is appended to newsroom name 
$config['detached_suffix'] = '.dev1.zorka.newswire.com';

// this is prepended to newsroom name 
$config['detached_prefix'] = 'detached-';

// base directory of i-newswire site
$config['compat_dir'] = '/home/www/dev/inw/v1';

// the directory that accepts file uploads
$config['upload_dir'] = '/home/www/dev/inw/v2/files';

// common functions hostname name
$config['common_host_name'] = 'co';

// common functions hostname (must have host suffix)
$config['common_host'] = "{$config['common_host_name']}{$config['host_suffix']}";

// the detached hostname pattern such that the newsroom 
// name can be extracted as the matched part
$config['admo_pattern'] = '#^admo-([0-9]+)\.dev1\.zorka\.newswire\.com$#is';

// this is appended to newsroom name 
$config['admo_suffix'] = '.dev1.zorka.newswire.com';

// this is prepended to newsroom name 
$config['admo_prefix'] = 'admo-';

// base url for contact unsubscribe action
$config['unsubscribe_base_url'] = "http://{$config['common_host']}/common/contact_unsubscribe";

// the base url for the iella api (optional use)
$config['iella_base_url'] = "http://{$config['common_host']}/api/iella/";

// the host url for the iella api (optional use)
$config['iella_host_url'] = "http://{$config['common_host']}/";

// the base url for assets folder
$config['assets_base'] = "{$env['protocol']}://www{$config['host_suffix']}/assets/";

// admin user to send writing related emails to 
// (for admin_editor editing privilege reseller)
$config['writing_admin_user'] = 219764;

// the hostname of the MOT website (used also for MOT iella)
$config['mot_host'] = '';

// the url to the base of the MOT website
$config['mot_host_url'] = "http://{$config['mot_host']}/";

// the base url for all facebook authorization logic
$config['facebook_app']['base_url'] = "http://{$config['common_host']}/common/facebook_auth_request";

// the base url for all twitter authorization logic
$config['twitter_app']['base_url'] = "http://{$config['common_host']}/common/twitter_auth_request";

// the hostname of the stats engine
$config['stats_host'] = 'dev1.zorka.newswire.com:4321';

// by default stats is disabled, must be enabled locally
$config['stats_enabled'] = false;

// use the system sendmail() instead
$config['mailer_exec'] = 'sendmail -t -f %s';

// braintree payment gateway
$config['braintree'] = array();
$config['braintree']['environment'] = 'sandbox';
$config['braintree']['merchant_id'] = 'gm5tq54cmxhnwb73';
$config['braintree']['public_key'] = 'rn4g6dtqbd8mtn6v';
$config['braintree']['private_key'] = '49801023bf08ecea7844226344d5e3c0';
$config['braintree']['merchant_account_id'] = 'newswire';

// salesforce sales tracking
$config['salesforce']['password'] = null;
// // various hard coded identifiers for objects
$config['salesforce']['objects'] = array();
$config['salesforce']['objects']['owner'] = '005i0000001eXuW';
$config['salesforce']['objects']['pricebook'] = '01si0000001YFwC';

// kissmetrics tracking 
// $config['kissmetrics'] = array();
$config['kissmetrics']['api_key'] = '7ea4bcdad632262881e16a6e48fb6e7a2d615e2f';
$config['kissmetrics']['global_id'] = '5c6ac070-ee1e-0130-4f2c-1231381fa34d';

// prnewswire api access
$config['prnewswire'] = array();
$config['prnewswire']['api_base'] = 'https://orderapisandbox.prnewswire.com/OrderAPI.svc/XML/';
$config['prnewswire']['api_key'] = '6205bad2cbb3465da2dc814314d9feec';
$config['prnewswire']['user_first_name'] = 'Patrick';
$config['prnewswire']['user_last_name'] = 'Santiago';
$config['prnewswire']['user_email'] = 'dev-inewswire@staite.net';
$config['prnewswire']['user_phone'] = '800-713-7278';

// aweber email lists
$config['aweber']['consumer_key'] = null;
$config['aweber']['consumer_secret'] = null;
$config['aweber']['access_key'] = null;
$config['aweber']['access_secret'] = null;

// email address to use for all mails in dev enviroment
$config['dev_email'] = 'supertech930@gmail.com';

// email address to use for all critical errors
// * this goes to the dev email as well
$config['crit_email'] = 'supertech930@gmail.com';

// email address that a copy of all receipts is sent to
$config['order_email'] = 'supertech930@gmail.com';

// prevent auth/register after this
// many attempts (reduces at 1 per minute)
$config['auth_limiter_count'] = PHP_INT_MAX; 

$config['social_facebook_app']['base_url'] = "http://{$config['common_host']}/common/social_facebook";
$config['social_twitter_app']['base_url'] = "http://{$config['common_host']}/common/social_twitter";
$config['social_instagram_app']['base_url'] = "http://{$config['common_host']}/common/social_instagram";