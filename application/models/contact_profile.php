<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Contact_Profile extends Model {
	
	use Raw_Data_Trait;

	const REMOTE_TYPE_MMI = 'mmi';

	protected static $__table = 'nr_contact_profile';
	protected static $__primary = 'id';

	public static function find_remote($remote_type, $remote_id)
	{
		return static::find(array(
			array('remote_type', $remote_type),
			array('remote_id', $remote_id)
		));
	}

	public static function find_for_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		$sql = "SELECT cxcp.contact_profile_id FROM nr_contact_x_contact_profile 
			cxcp WHERE cxcp.contact_id = ? LIMIT 1";
		$dbrow = static::__db()->query($sql, array($contact))->row();
		
		if ($dbrow)
			  $profile = static::find($dbrow->contact_profile_id);
		else $profile = null;

		if (!$profile)
		{
			$profile = new static();
			$profile->remote_id = null;
			$profile->remote_type = null;
			$profile->raw_data(null);
			$profile->save();
		}

		return $profile;
	}
	
}