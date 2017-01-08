<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Subscription_Instant_Update_Sent extends Model {
	
	protected static $__table = 'nr_subscription_instant_update_sent';
	protected static $__primary = array('subscription_id', 'date_sent');
	
}

?>