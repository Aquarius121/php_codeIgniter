<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Contact_MDB_Approved extends Model {
	
	protected static $__table = 'nr_contact_mdb_approved';
	protected static $__primary = 'contact_id';

	public function __construct($id)
	{
		$this->contact_id = $id;
		$this->date_created = Date::utc();
	}
	
}
