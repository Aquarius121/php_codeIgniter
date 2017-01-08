<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_WireUpdate_Subscriber extends Model {
	
	use Raw_Data_Trait;

	protected static $__table = 'nr_wireupdate_subscriber';

	public static function find_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		return static::find('contact_id', $contact);
	}
	
}

?>