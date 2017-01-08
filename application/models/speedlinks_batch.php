<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_SpeedLinks_Batch extends Model {
	
	const SUBMISSION_TYPE_FC_LINK 			= 'fc_link';
	const SUBMISSION_TYPE_WORLD_NOW_LINK 	= 'world_now_link';
	const SUBMISSION_TYPE_NR 				= 'newsroom';

	protected static $__table   = 'sl_speedlinks_batch';
	protected static $__primary = 'id';
	
}

?>