<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Contact_List_Action_Detail extends Model {

	const DETAIL_MDB_FILTER_BEAT_ID = 'mdb_filter_beat_id';
	const DETAIL_MDB_FILTER_MEDIA_TYPE_ID = 'mdb_filter_media_type_id';
	const DETAIL_MDB_FILTER_ROLE_ID = 'mdb_filter_role_id';
	const DETAIL_MDB_FILTER_COVERAGE_ID = 'mdb_filter_coverage_id';
	const DETAIL_MDB_FILTER_COUNTRY_ID = 'mdb_filter_country_id';
	const DETAIL_MDB_FILTER_REGION_ID = 'mdb_filter_region_id';
	const DETAIL_MDB_FILTER_LOCALITY_ID = 'mdb_filter_locality_id';

	// references nr_contact_list_action_mdb_search_text.id
	const DETAIL_MDB_FILTER_SEARCH_TEXT_ID = 'mdb_filter_search_text_id'; 
	
	const DETAIL_MDB_OPTION_UNIQUE_EMAILS = 'mdb_option_unique_emails';
	const DETAIL_MDB_OPTION_CONTACT_WITH_PICS = 'mdb_option_contact_with_pics';
	
	const DETAIL_MDB_NUM_CONTACTS_ADDED = 'mdb_num_contacts_added';

	// references nr_contact_list_action_import_file.stored_file_id
	const DETAIL_CSV_STORED_FILE_ID = 'csv_stored_file_id';
	
	const DETAIL_CSV_NUM_CONTACTS_IMPORTED = 'csv_num_contacts_imported';

	protected static $__table = 'nr_contact_list_action_detail';

	public static function create_and_save($contact_list_action_id, $detail, $value)
	{
		$cl_action_detail = new static();
		$cl_action_detail->contact_list_action_id = $contact_list_action_id;
		$cl_action_detail->detail = $detail;
		$cl_action_detail->value = $value;
		$cl_action_detail->save();
		return $cl_action_detail;
	}

}