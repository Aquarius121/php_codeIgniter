<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Contact_Transfer extends Model {
	
	protected static $__table = 'nr_contact_transfer';
	protected static $__primary = null;

	public static function find_remote($id)
	{
		return static::find('contact_id_remote', $id);
	}
	
	public static function find_local($id)
	{
		return static::find('contact_id_local', $id);
	}
	
}

?>