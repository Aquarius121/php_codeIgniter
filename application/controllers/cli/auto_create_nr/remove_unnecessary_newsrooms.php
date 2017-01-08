<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// The script below removes all the unnecessary
// newsrooms that are duplicate and are unusable. 
// e.g. Newsrooms which have source = 'mynewsdesk' 
// but the company_id does not exist in 
// ac_nr_mynewsdesk_company

load_controller('cli/base');

class Remove_Unnecessary_Newsrooms_Controller extends CLI_Base {

	public function index()
	{
		// $this->remove_mynewsdesk_nrs();
		// $this->remove_newswire_ca_nrs();
		// $this->remove_prweb_nrs();
		// $this->remove_marketwired_nrs();
		// $his->remove_businesswire_nrs();
		 $this->remove_owler_nrs();
		// $this->remove_cb_nrs();

	}

	protected function remove_mynewsdesk_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_mynewsdesk_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MYNEWSDESK);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_mynewsdesk_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MYNEWSDESK);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_mynewsdesk_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MYNEWSDESK);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_mynewsdesk_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_MYNEWSDESK));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
	}


	protected function remove_newswire_ca_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_newswire_ca_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_NEWSWIRE_CA);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_newswire_ca_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_NEWSWIRE_CA);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_newswire_ca_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_NEWSWIRE_CA);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_newswire_ca_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_NEWSWIRE_CA));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}




	protected function remove_prweb_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_prweb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_PRWEB);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_prweb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_PRWEB);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_prweb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_PRWEB);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_prweb_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_PRWEB));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}


	protected function remove_marketwired_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_marketwired_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MARKETWIRED);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_marketwired_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MARKETWIRED);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_marketwired_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_MARKETWIRED);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_marketwired_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_MARKETWIRED));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}



	protected function remove_businesswire_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_businesswire_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_BUSINESSWIRE);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_businesswire_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_BUSINESSWIRE);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_businesswire_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_BUSINESSWIRE);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_businesswire_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_BUSINESSWIRE));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}


	protected function remove_owler_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_owler_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_OWLER);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_owler_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_OWLER);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_owler_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_OWLER);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_owler_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_OWLER));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}

	protected function remove_cb_nrs()
	{
		$sql = "UPDATE nr_content 
				SET company_id = 0
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_cb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_CRUNCHBASE);

		$sql = "DELETE  
				FROM nr_company_profile
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_cb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_CRUNCHBASE);


		$sql = "DELETE 
				FROM nr_newsroom_custom
				WHERE company_id IN
				(
					SELECT c.id FROM nr_company c
					LEFT JOIN ac_nr_cb_company mndc
					ON mndc.company_id = c.id
					WHERE source = ? 
					AND mndc.id IS NULL
					AND c.user_id = 1
				)";

		$this->db->query($sql, Model_Company::SOURCE_CRUNCHBASE);

		$sql = "SELECT c.id FROM nr_company c
				LEFT JOIN ac_nr_cb_company mndc
				ON mndc.company_id = c.id
				WHERE source = ?
				AND mndc.id IS NULL
				AND c.user_id = 1";

		$query = $this->db->query($sql, array(Model_Company::SOURCE_CRUNCHBASE));
		$results = Model_Company::from_db_all($query);
		$ids = array();
		foreach ($results as $result)
			$ids[] = $result->id;

		if (!count($ids))
			return;

		$ids_list = sql_in_list($ids);

		$sql = "DELETE FROM nr_company
				WHERE id IN ({$ids_list})";

		$this->db->query($sql);
		
	}





}

?>
