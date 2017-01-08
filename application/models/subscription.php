<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Subscription extends Model {
	
	const NOTIFY_INSTANT  = 'instant';
	const NOTIFY_DAILY	 = 'daily';
	const NOTIFY_NEVER	 = 'never';

	protected static $__table = 'nr_subscription';
	protected static $__primary = 'id';
	
}
