<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Contact_Bounce extends Model {
	
	protected static $__table = 'nr_contact_bounce';
	protected static $__primary = array(
		'contact_id',
		'ts_bounced'
	);
	
	public static function create($contact, $ts)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;

		// the contact bounce has already been recorded
		if ($ins = static::find_id(array($contact, $ts)))
			return $ins;

		$ins = new static();
		$ins->contact_id = $contact;
		$ins->ts_bounced = (int) $ts;
		$ins->date_bounced = Date::ts($ts);
		return $ins;
	}
	
}

?>