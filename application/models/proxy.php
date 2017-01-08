<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Proxy extends Model {

	protected static $__table = 'ac_nr_proxy';

	const SOURCE_BUYPROXYLIST_COM = 'buyproxylist.com';
	const TYPE_HTTP = 'http';
	const TYPE_HTTPS = 'https';
	const TYPE_SOCKS4 = 'socks4';
	const TYPE_SOCKS5 = 'socks5';
}

?>