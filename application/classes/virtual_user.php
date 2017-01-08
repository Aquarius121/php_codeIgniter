<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Virtual_User extends Model_Base {

	public static function create_virtual_email()
	{
		return sprintf('%s@virtual', UUID::create());
	}

	public static function is_virtual_email($email)
	{
		return str_ends_with($email, '@virtual');
	}

	public function virtual_source()
	{
		return Model_Virtual_Source::find($this->virtual_source_id);
	}
	
	public function name()
	{
		return trim(sprintf('%s %s', 
			$this->first_name, 
			$this->last_name));
	}
	
}