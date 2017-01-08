<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Renewal {

	// default period to use for
	// no item or no period
	const DEFAULT_PERIOD = 30;
	
	// number of hours before due
	// that we attempt transaction
	const AUTO_RENEW_PRE_HOURS = 24;
	
	// number of attempts to make transaction
	const AUTO_RENEW_ATTEMPTS = 3;

	// max time a renewal can be suspended
	// for in number of days
	const MAX_SUSPENSION_PERIOD = 60;

}