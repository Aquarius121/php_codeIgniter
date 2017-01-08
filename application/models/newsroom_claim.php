<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Newsroom_Claim extends Model {
	
	protected static $__table = 'ac_nr_newsroom_claim';

	const STATUS_CLAIMED     = 'claimed';
	const STATUS_CONFIRMED   = 'confirmed';
	const STATUS_REJECTED    = 'rejected';
	const STATUS_IGNORED     = 'ignored';

}

?>