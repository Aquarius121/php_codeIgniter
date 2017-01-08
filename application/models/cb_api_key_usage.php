<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_CB_API_Key_Usage extends Model {

	protected static $__table = 'ac_nr_cb_api_key_usage';
	protected static $__primary = array('cb_api_key_id', 'date_used');

}

?>