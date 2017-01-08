<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class Media_Database_Contact_Access {
	
	public static function has_plus_access($user = null)
	{
		if ($user === null) 
		{
			if (Auth::is_admin_online()) return true;
			$user = Auth::user();
		}

		if ($user->has_platinum_access()) return true;
		if ($user->is_admin) return true;

		$raw_data = $user->raw_data();
		if (isset($raw_data->has_media_database_plus) &&
			$raw_data->has_media_database_plus)
			return true;

		if (isset($raw_data->has_media_database_plus_until))
			if (Date::utc($raw_data->has_media_database_plus_until) > Date::$now)
				return true;

		return false;
	}

	public static function email_obfuscator($user = null)
	{
		return Media_Database_Contact_Access::has_plus_access($user)
			? new Passthru_Email_Obfuscator()
			: new Email_Obfuscator();
	}

	public static function phone_obfuscator($user = null)
	{
		return Media_Database_Contact_Access::has_plus_access($user)
			? new Passthru_Phone_Obfuscator()
			: new Phone_Obfuscator();
	}
	
}

?>