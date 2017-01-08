<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class LEGACY_User extends Model_Base {
	
	public function package_name()
	{
		if ($this->deal_id == 1)
		     return 'Silver';
		else if ($this->deal_id == 2)
		     return 'Gold';
		else if ($this->deal_id == 3)
		     return 'Platinum';
		else if ($this->deal_id == 0)
		     return 'Basic';
		else return null;
	}
	
}

?>