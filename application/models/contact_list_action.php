<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Contact_List_Action extends Model {

	const TYPE_IMPORT_FROM_CSV = 'import_from_csv';
	const TYPE_ADD_CONTACT_FROM_MDB = 'add_contacts_from_mdb';
	const TYPE_CREATE_LIST_FROM_MDB = 'create_list_from_mdb';

	protected static $__table = 'nr_contact_list_action';

	public static function log_create_list_from_mdb($contact_list_id)
	{
		$cl_action = new static();
		$cl_action->contact_list_id = $contact_list_id;
		$cl_action->type = static::TYPE_CREATE_LIST_FROM_MDB;
		$cl_action->date_action_taken = Date::$now->format(Date::FORMAT_MYSQL);
		$cl_action->save();
		return $cl_action;
	}

	public static function log_add_contacts_from_mdb($contact_list_id)
	{
		$cl_action = new static();
		$cl_action->contact_list_id = $contact_list_id;
		$cl_action->type = static::TYPE_ADD_CONTACT_FROM_MDB;
		$cl_action->date_action_taken = Date::$now->format(Date::FORMAT_MYSQL);
		$cl_action->save();
		return $cl_action;
	}

	public static function log_import_from_csv($contact_list_id)
	{
		$cl_action = new static();
		$cl_action->contact_list_id = $contact_list_id;
		$cl_action->type = static::TYPE_IMPORT_FROM_CSV;
		$cl_action->date_action_taken = Date::$now->format(Date::FORMAT_MYSQL);
		$cl_action->save();
		return $cl_action;
	}

}