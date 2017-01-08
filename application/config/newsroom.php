<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
global $env;

// the hostname pattern such that the newsroom 
// name can be extracted as the matched part
$config['host_pattern'] = '#^([a-z0-9\-]+)\.newswire\.com$#is';

// this is appended to newsroom name 
$config['host_suffix'] = '.newswire.com';

// the detached hostname pattern with a session id in the name
$config['detached_pattern'] = '#^detached-([a-f0-9]{16})\.newswire\.com$#is';

// this is appended to newsroom name 
$config['detached_suffix'] = '.newswire.com';

// this is prepended to newsroom name 
$config['detached_prefix'] = 'detached-';

// the url pattern to detect a newswire URL
$config['url_pattern'] = '#^https?://([a-z0-9\-]+)\.newswire\.com#is';

// base directory of newswire site
$config['compat_dir'] = '/home/www/newswire.com/v1';

// max filesize for audio uploads
$config['max_audio_size'] = 26214400; 

// max filesize for web file uploads
$config['max_web_file_size'] = 26214400; 

// max filesize for web file uploads
$config['max_image_file_size'] = 4194304; 

// prevent auth/register after this
// many attempts (reduces at 1 per minute)
$config['auth_limiter_count'] = 5; 

// common functions hostname name
$config['common_host_name'] = 'co';

// common functions hostname (must have host suffix)
$config['common_host'] = "{$config['common_host_name']}{$config['host_suffix']}";

// the detached hostname pattern such that the newsroom 
// name can be extracted as the matched part
$config['admo_pattern'] = '#^admo-([0-9]+)\.newswire\.com$#is';

// this is appended to newsroom name 
$config['admo_suffix'] = '.newswire.com';

// this is prepended to newsroom name 
$config['admo_prefix'] = 'admo-';

// the default content type and charset
$config['content_type'] = 'text/html; charset=utf-8';

// cookie used for tracking blocked user
$config['blocked_cookie'] = 'CICBv10';

// iPublish facebook application
$config['facebook_app'] = array();
$config['facebook_app']['api'] = array();
$config['facebook_app']['api']['appId'] = '586234651427775';
$config['facebook_app']['api']['secret'] = '3aad98ff71ed68db2fe731b54d9babfd';

// the base url for all facebook authorization logic
$config['facebook_app']['base_url'] = "http://{$config['common_host']}/common/facebook_auth_request";

// iPublish twitter application
$config['twitter_app'] = array();
$config['twitter_app']['api'] = array();
$config['twitter_app']['api']['key'] = 'PyzgPqcqpgQ4U1pE9TQQ';
$config['twitter_app']['api']['secret'] = 'Z4KOIkSpZXSvgdXEYJl7osfl3dg6fTI6qD5V35dw';
$config['twitter_app']['api']['oauth'] = array(
	'oauth_token' => '1604749861-GEKX8zGKqR387vO8ccDuOi0RBMjhmxZ5Fgr441K', 
	'oauth_token_secret' => 'z9m52zmhecW52Bp45l0eLT7hZq1xJQJubmcmy3f1g',
);

// linkedin app credentials 
$config['linkedin_app'] = array();
$config['linkedin_app']['clientId'] = '77frd270mpr3xn';
$config['linkedin_app']['secret'] = 'NylNQ79knQe3vDhP';
$config['linkedin_app']['base_url'] = "http://{$config['common_host']}/common/linkedin_auth_request";

// socialadr app credentials
$config['socialadr_app'] = array();
$config['socialadr_app']['clientId'] = '41';
$config['socialadr_app']['appId'] = '52';
$config['socialadr_app']['secret'] = '16fe61265499fab22e747818cf897cc3';
$config['socialadr_app']['base_url'] = "http://{$config['common_host']}/common/socialadr_auth_request";

// vimeo api access token
$config['vimeo_access_token'] = '525b2a8db28ca0f803de58422ac754e3';

// the base url for all twitter authorization logic
$config['twitter_app']['base_url'] = "http://{$config['common_host']}/common/twitter_auth_request";

// Social wire Youtube application
$config['youtube_api_key'] = 'AIzaSyAu-wjZPHMZQSZXyhw-V-AlSN_VnXJnhG4'; 		// production?
// $config['youtube_api_key'] = 'AIzaSyBpu8hgnXbkqFVWrAvwRUEz7T13ii3I7WM'; 	// dev?

// the hostname of the stats engine
$config['stats_host'] = 'stats.newswire.com';

// by default stats is disabled, must be enabled locally
$config['stats_enabled'] = false;

// address to send emails from
$config['email_address'] = 'notification@newswire.com';

// name to send emails from
$config['email_name'] = 'Newswire';

// address to send emails from
$config['journalists_email_address'] = 'journalists@newswire.com';

// name to send emails from
$config['journalists_email_name'] = 'Newswire';

// secret used to generate unsubscribe hashes
$config['unsubscribe_secret'] = 'a281e825d55b7ca9081be43134975261';

// base url for contact unsubscribe action
$config['unsubscribe_base_url'] = "http://{$config['common_host']}/common/contact_unsubscribe";

// file that contains authentication secret
$config['auth_secret_file'] = 'application/config/auth_secret.php';

// file that contains api secret
$config['iella_secret_file'] = 'application/config/iella_secret.php';

// file that contains api secret
$config['iella_virtuals_callback_secret_file'] = 'application/config/iella/virtuals_callback.php';

// the base url for the iella api (optional use)
$config['iella_base_url'] = "https://{$config['common_host']}/api/iella/";

// the host url for the iella api (optional use)
$config['iella_host_url'] = "https://{$config['common_host']}/";

// the hostname of the main newswire website
$config['website_host'] = $env['website_host'];

// the url of the main newswire website (end in slash)
$config['website_url'] = "{$env['protocol']}://{$env['website_host']}/";

$config['social_facebook_app'] = array();
$config['social_facebook_app']['api'] = array();
$config['social_facebook_app']['api']['appId'] = '1129306723832877';
$config['social_facebook_app']['api']['secret'] = '4a61d2934c386ed9add358bcaa12f0d5';

// the base url for all facebook authorization logic
$config['social_facebook_app']['base_url'] = "http://{$config['common_host']}/common/social_facebook";

// iPublish twitter application
$config['social_twitter_app'] = array();
$config['social_twitter_app']['api'] = array();
$config['social_twitter_app']['api']['key'] = 'YWxAP84zGiItAJLMDyPRmHSV0';
$config['social_twitter_app']['api']['secret'] = 'IQrBxpGkqAS3irfz751akvmTQF4XAfRpdpPzJg4v2xW1CE1vBL';
$config['social_twitter_app']['api']['oauth'] = array(
	'oauth_token' => '794250798712684544-EiMINN8KIGPtCHReizIfrSuamNV53Ot', 
	'oauth_token_secret' => 'NOq1yJnEAYHH3S3k2YBHacXxbPSooaAY2l9j4bPoD76rU',
);

$config['social_twitter_app']['base_url'] = "http://{$config['common_host']}/common/social_twitter";

$config['social_instagram_app'] = array();
$config['social_instagram_app']['api'] = array();
$config['social_instagram_app']['api']['apiKey'] = '3d4d33b88dcb46f6a4603206d1ded76a';
$config['social_instagram_app']['api']['apiSecret'] = '30f38ec4571d48c0927c3cdcab408e85';;

$config['social_instagram_app']['base_url'] = "http://{$config['common_host']}/common/social_instagram";

// this is the new website hostname
$config['website_tunnel_host'] = $env['website_tunnel_host'];

// the directory that accepts file uploads
$config['upload_dir'] = '/home/www/newswire.com/shared/files';

// the url prefix for file uploads
$config['upload_url'] = 'files';

// the hostname for file uploads 
$config['upload_host'] = $config['website_host'];

// the url of the main newswire panel (account)
$config['non_migrated_url'] = "{$config['website_url']}MyAccount";

// the codeigniter "index" file 
// with a unique name to recognise process
$config['cli_php_file'] = 'newswire.php';

// the path to the msmtp configuration file
$config['mailer_conf'] = 'application/binaries/msmtp/conf.php';

// the path to the mailer queue and buffer
$config['mailer_queue_dir'] = 'application/data/mailer/queue';
$config['mailer_buffer_dir'] = 'application/data/mailer/buffer';

// the path and arguments to msmtp
$config['mailer_exec'] = 'application/binaries/msmtp/mailer -t '
	. '--file=application/binaries/msmtp/conf.php '
	. '--logfile=application/binaries/msmtp/mail.log '
	. '-f %s';
	
// the fin distribution check url prefix
$config['fin_distribution_url'] = 'http://tracking.prconnect.com/inewswire?Module=clipping-top100&SourceID=%s';

// the base url for assets folder
$config['assets_base'] = "{$env['protocol']}://as25{$config['host_suffix']}/assets/";

// the assets folder
$config['assets_base_dir'] = 'assets';

// admin user to send writing related emails to 
// (for admin_editor editing privilege reseller)
$config['writing_admin_user'] = 1073747166;

$config['scraped_nr_default_user'] = 1;

// the hostname of the MOT website (used also for MOT iella)
$config['mot_host'] = 'myoutsourceteam.com';

// the url to the base of the MOT website
$config['mot_host_url'] = "http://{$config['mot_host']}/";

// the ip address used for the main website
// newsroom domains must use this
$config['ip_address'] = trim(file_get_contents('/etc/ip'));

// the default timezone to use when there is 
// no user timezone specified
// DO NOT CHANGE THIS
$config['timezone'] = 'UTC';

// the api access details for scribed
$config['docsite_scribd'] = array();
$config['docsite_scribd']['url'] = 'http://api.scribd.com/api';
$config['docsite_scribd']['api_key'] = '1otk2p5d67v6moc0fffum';
$config['docsite_scribd']['url_doc'] = 'http://www.scribd.com/doc/%d';

// the api access details for issuu
$config['docsite_issuu'] = array();
$config['docsite_issuu']['url_upload'] = 'http://upload.issuu.com/1_0';
$config['docsite_issuu']['url_api'] = 'http://api.issuu.com/1_0';
$config['docsite_issuu']['api_key'] = 'xchfsd40futii22s63gj8tno1uw6amp2';
$config['docsite_issuu']['secret'] = 'ssrtx9v8p2insohyogj27aw98e7apecx';
$config['docsite_issuu']['url_doc'] = 'http://issuu.com/inewswire/docs/%s';

// braintree payment gateway
$config['braintree'] = array();
$config['braintree']['environment'] = 'production';
$config['braintree']['merchant_id'] = '4spw7wbpnnt96x59';
$config['braintree']['public_key'] = 'czhkq64g6yrcnx8p';
$config['braintree']['private_key'] = 'b3e01797e36786e39f9a60a6d419ed10';
$config['braintree']['merchant_account_id'] = 'INEWSWIRE_instant';

// salesforce sales tracking
$config['salesforce'] = array();
$config['salesforce']['wsdl'] = 'application/config/salesforce.wsdl';
$config['salesforce']['username'] = 'anthony@newswire.com';
// the password and the security token concatenated
$config['salesforce']['password'] = 'Newswire2016l7NTXuYPJjwyh8Qx0e2lEDw5i';
// various hard coded identifiers for objects
$config['salesforce']['objects'] = array();
$config['salesforce']['objects']['owner'] = '005i0000001eXuW';
$config['salesforce']['objects']['pricebook'] = '01si0000001YFwC';

// kissmetrics tracking 
$config['kissmetrics'] = array();
$config['kissmetrics']['api_key'] = 'ac52503e08361e6c3f23f51109d67fc98821caa1';
$config['kissmetrics']['global_id'] = '715c1950-e87f-0130-d0ac-12313d23508a';

// aweber email lists
// * developer email: dev-inewswire@staite.net
// * developer password: JE5kA153cm9rX6G
$config['aweber'] = array();
$config['aweber']['consumer_key'] = 'AkTTHMplG6he2WY0dr0HQxNA';
$config['aweber']['consumer_secret'] = '5w8FWrgVQdi6sFfuYr0COoz2hn9o5RL0MNahhu7g';
$config['aweber']['access_key'] = 'Ag60Uh20XjZVwiBrpO49bJnI';
$config['aweber']['access_secret'] = 'ZcV5qEqcAczf0MuHItLlwUD9CBn1DxGjNmZoYKXf';
$config['aweber']['lists'] = array();
$config['aweber']['lists'][0] = 970952; // free package
$config['aweber']['lists'][1] = 1048140; // silver package
$config['aweber']['lists'][2] = 1048767; // gold package
$config['aweber']['lists'][3] = 1048772; // platinum package

// sendgrid api 
$config['sendgrid'] = array();
$config['sendgrid']['username'] = 'inewswire';
$config['sendgrid']['password'] = 'icontacts2013777';
$config['sendgrid']['api_key'] = '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';

// Accesswire
$config['accesswire'] = array();
$config['accesswire']['id'] = 1181;
$config['accesswire']['email'] = 'amsinternet@gmail.com';
$config['accesswire']['password'] = 'PRESSrelease2016zz';
$config['accesswire']['name'] = 'Patrick';

// copyscape api data
$config['copyscape'] = array();
$config['copyscape']['username'] = 'newswirecom';
$config['copyscape']['password'] = 'mikesmikes999';
$config['copyscape']['key'] = 'aqnvhkxbsb57x8ah';

// configuration for recording events
// to the idevaffiliate php script
$config['idevaffiliate'] = array();
$config['idevaffiliate']['profile'] = 72198;
$config['idevaffiliate']['base_url'] = "http://{$config['website_host']}/affiliate-program";

// delete user related session data
// on logout (or login of another user)
$config['auth_reset'] = array();
$config['auth_reset']['session'] = array();
$config['auth_reset']['session'][] = 'nr_feedback';
$config['auth_reset']['cookie'] = array();
$config['auth_reset']['cookie'][] = 'kiss_anon_id';

// a list of names that are not allowed
$config['reserved_names'] = array();
$config['reserved_names'][] = '^detached-';
$config['reserved_names'][] = '^admo-';
$config['reserved_names'][] = '^version\d+$';
$config['reserved_names'][] = '^(v1|v2)$';

// email address to use for all mails in dev enviroment
// * the critical errors will also be CC'd here
$config['dev_email'] = 'dev-inewswire-direct@staite.net';

// email address: critical errors (SEND ONLY)
$config['crit_email'] = 'critical@newswire.com';

// email address for sending outreach campaign (virtual users)
$config['outreach_email'] = 'outreach@newswire.com';

// email address for things that don't expect reply
$config['no_reply_email'] = 'no-reply@newswire.com';

// email address for unsubscribe requests
$config['list_unsubscribe_email'] = 'support@newswire.com';

// email details for sending outreach campaign
$config['media_outreach_email'] = array();
$config['media_outreach_email']['mailer'] = 'Newswire';
$config['media_outreach_email']['sender_email'] = 'outreach@newswire.com';
$config['media_outreach_email']['sender_name'] = 'Newswire Media Outreach';

// url patterns that require user online
// (and that do not implement own auth)
$config['requires_user'] = array();
$config['requires_user'][] = '#^admin(/|$)#';
$config['requires_user'][] = '#^manage(/|$)#';
$config['requires_user'][] = '#^reseller(?!/api)(/|$)#';

// dynamic transaction descriptors 
$config['transaction_descriptor'] = array();
$config['transaction_descriptor']['name'] = 'NewswireLLC *%s';
$config['transaction_descriptor']['phone'] = '800-713-7278';
$config['transaction_descriptor']['url'] = 'newswire.com';

// spamassassin server
$config['spamassassin'] = array();
$config['spamassassin']['hostname'] = '127.0.0.1';
$config['spamassassin']['port'] = 783;
$config['spamassassin']['user'] = 'spamd';
$config['spamassassin']['enabled'] = true;

// score at which campaigns are marked as spam
$config['spam_score_threshold'] = 2.5;

// prnewswire api access
$config['prnewswire'] = array();
$config['prnewswire']['api_base'] = 'https://orderapi.prnewswire.com/OrderAPI.svc/XML/';
$config['prnewswire']['api_key'] = 'efe0debda67e473195344470af93b3f8';
$config['prnewswire']['api_label'] = 'Production';
$config['prnewswire']['user_first_name'] = 'Patrick';
$config['prnewswire']['user_last_name'] = 'Santiago';
$config['prnewswire']['user_email'] = 'patrick@newswire.com';
$config['prnewswire']['user_phone'] = '800-713-7278';

// the path to the maxmind city database 
$config['maxmind_db_file'] = 'application/data/maxmind_db/db.mmdb';

// which version to request
// for internal requests such 
// as iella and pdf generator
$config['request_version'] = 25;

// the min size of the image uploaded on web images
$config['min-image-size'] = new stdClass();
$config['min-image-size']->width = 250;
$config['min-image-size']->height = 250;

// the sizes of the default images
$v_sizes = array();

// the sizes of the default thumb images
$v_sizes['thumb'] = new stdClass();
$v_sizes['thumb']->width = 184;
$v_sizes['thumb']->height = 106;
$v_sizes['thumb']->cropped = true;

// the sizes of the default header logo images
$v_sizes['header'] = new stdClass(); 
$v_sizes['header']->format = Image::FORMAT_PNG;
$v_sizes['header']->width = 200;
$v_sizes['header']->height = 60;
$v_sizes['header']->cropped = false;

// the sizes of the header thumb images
$v_sizes['header-thumb'] = new stdClass();
$v_sizes['header-thumb']->width = 184;
$v_sizes['header-thumb']->height = 106;
$v_sizes['header-thumb']->cropped = true;
$v_sizes['header-thumb']->max_ratio_diff = 0.25;
$v_sizes['header-thumb']->max_ratio_diff_margin = 5;

// the sizes of the header finger images
$v_sizes['header-finger'] = new stdClass();
$v_sizes['header-finger']->width = 76;
$v_sizes['header-finger']->height = 76;
$v_sizes['header-finger']->cropped = true;

// the sizes of the default header-sidebar logo images
$v_sizes['header-sidebar'] = new stdClass(); 
$v_sizes['header-sidebar']->format = Image::FORMAT_PNG;
$v_sizes['header-sidebar']->width = 200;
$v_sizes['header-sidebar']->height = 80;
$v_sizes['header-sidebar']->cropped = false;

// the sizes of the default header-sidebar logo images (2x)
$v_sizes['header-sidebar-2x'] = new stdClass(); 
$v_sizes['header-sidebar-2x']->format = Image::FORMAT_PNG;
$v_sizes['header-sidebar-2x']->width = 400;
$v_sizes['header-sidebar-2x']->height = 160;
$v_sizes['header-sidebar-2x']->cropped = false;

// the sizes of the default finger images
$v_sizes['finger'] = new stdClass(); 
$v_sizes['finger']->width = 80;
$v_sizes['finger']->height = 80;
$v_sizes['finger']->cropped = true;

// the sizes of the default web images
$v_sizes['web'] = new stdClass(); 
$v_sizes['web']->width = 140;
$v_sizes['web']->height = 140;
$v_sizes['web']->cropped = true;
$v_sizes['web']->max_ratio_diff = 1;
$v_sizes['web']->max_ratio_diff_margin = 5;

// the sizes of the default web images (view page)
$v_sizes['view-web'] = new stdClass(); 
$v_sizes['view-web']->width = 160;
$v_sizes['view-web']->height = 110;
$v_sizes['view-web']->cropped = true;
$v_sizes['view-web']->max_ratio_diff = 0;
$v_sizes['view-web']->max_ratio_diff_margin = 5;

// the sizes of the default web images (view page) (2x)
$v_sizes['view-web-2x'] = new stdClass(); 
$v_sizes['view-web-2x']->width = 320;
$v_sizes['view-web-2x']->height = 220;
$v_sizes['view-web-2x']->cropped = true;
$v_sizes['view-web-2x']->max_ratio_diff = 0;
$v_sizes['view-web-2x']->max_ratio_diff_margin = 5;

// the sizes of the default web images (view page cover)
$v_sizes['view-cover'] = new stdClass(); 
$v_sizes['view-cover']->width = 225;
$v_sizes['view-cover']->min_height = 45;  // 5:1
$v_sizes['view-cover']->max_height = 675; // 1:3

// the sizes of the default web images (view page cover) (2x)
$v_sizes['view-cover-2x'] = new stdClass(); 
$v_sizes['view-cover-2x']->width = 450;
$v_sizes['view-cover-2x']->min_height = 90;
$v_sizes['view-cover-2x']->max_height = 1350;

// the sizes of the default cover images
$v_sizes['cover'] = new stdClass(); 
$v_sizes['cover']->width = 256;
$v_sizes['cover']->max_height = 256;
$v_sizes['cover']->smart_white = true;
$v_sizes['cover']->smart_white_size = 10;

// the sizes of the default cover images (website)
$v_sizes['cover-website'] = new stdClass(); 
$v_sizes['cover-website']->width = 512;
$v_sizes['cover-website']->max_height = 512;
$v_sizes['cover-website']->smart_white = true;
$v_sizes['cover-website']->smart_white_size = 20;

// the sizes of the default cover images (fc feed)
$v_sizes['cover-feed'] = new stdClass(); 
$v_sizes['cover-feed']->width = 512;
$v_sizes['cover-feed']->allow_zoom = false;

// the sizes of the default contact images
$v_sizes['contact'] = new stdClass(); 
$v_sizes['contact']->width = 92;
$v_sizes['contact']->height = 92;
$v_sizes['contact']->cropped = true;

// the sizes of the default contact images (2x)
$v_sizes['contact-2x'] = new stdClass(); 
$v_sizes['contact-2x']->width = 184;
$v_sizes['contact-2x']->height = 184;
$v_sizes['contact-2x']->cropped = true;

// the sizes of the contact cover images
$v_sizes['contact-cover'] = new stdClass(); 
$v_sizes['contact-cover']->width = 162;
$v_sizes['contact-cover']->height = 162;
$v_sizes['contact-cover']->cropped = true;

// the sizes of the full width image
$v_sizes['view-full'] = new stdClass(); 
$v_sizes['view-full']->width = 697;
$v_sizes['view-full']->min_width = 697;

// the sizes of the distribution logo thumbnails
$v_sizes['dist-thumb'] = new stdClass(); 
$v_sizes['dist-thumb']->width = 200;
$v_sizes['dist-thumb']->height = 100;
$v_sizes['dist-thumb']->cropped = false;

// the sizes of the distribution logo fingers
$v_sizes['dist-finger'] = new stdClass(); 
$v_sizes['dist-finger']->width = 100;
$v_sizes['dist-finger']->height = 50;
$v_sizes['dist-finger']->cropped = false;

// the sizes of the video previews 
// that will be used when iframe cannot
// be used or would not be suitable
$v_sizes['web-video-preview'] = new stdClass(); 
$v_sizes['web-video-preview']->width = 608;
$v_sizes['web-video-preview']->height = 342;
$v_sizes['web-video-preview']->cropped = true;

// reference the array
$config['v_sizes'] =& $v_sizes;

// bad bots detection 
$config['bad_bots_directory'] = 'application/data/bad_bots';
$config['bad_bots_map_file'] = '/home/www/conf/rate-limit.map';
$config['bad_bots_max_age'] = 3600;

// load any local version of config
require 'newsroom.local.php';
