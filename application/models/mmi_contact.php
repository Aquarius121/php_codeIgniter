<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_MMI_Contact extends Model {

	use Raw_Data_Trait;

	protected static $__table = 'nr_mmi_contact';
	protected static $__primary = 'remote_id';

	public static function find_contact_id($contact_id)
	{
		return static::find('contact_id', $contact_id);
	}
	
}