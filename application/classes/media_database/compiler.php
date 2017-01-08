<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Compiler {

	public static $editor_roles = array(1,5,7,25,29,31,77);
	public static $reporter_roles = array(2);
	public static $local_news_beats = array(40,277,279,280,282);

	protected $beats = array();
	protected $regions = array();
	protected $countries = array();
	protected $roles = array();
	protected $unique_emails_only = false;

	public function set_beats($beats)
	{
		$this->beats = $beats;
		foreach ($this->beats as $k => $v)
			if ($v instanceof Model_Beat)
				$v = $v->id;
	}

	public function set_roles($roles)
	{
		$this->roles = $roles;
		foreach ($this->roles as $k => $v)
			if ($v instanceof Model_Contact_Role)
				$v = $v->id;
	}

	public function set_regions($regions)
	{
		$this->regions = $regions;
		foreach ($this->regions as $k => $v)
			if ($v instanceof Model_Region)
				$v = $v->id;
	}

	public function enable_unique_emails_only()
	{
		$this->unique_emails_only = true;
	}

	public function disable_unique_emails_only()
	{
		$this->unique_emails_only = false;
	}

	public function set_countries($countries)
	{
		$this->countries = $countries;
		foreach ($this->countries as $k => $v)
			if ($v instanceof Model_Country)
				$v = $v->id;
	}

	public function compile($limit = 100)
	{
		$ci =& get_instance();
		$limit = (int) $limit;
		$rand = Model_Contact::rand();
		$filter = 1;

		if (count($this->beats))
		{
			$beats = sql_in_list($this->beats);
			$filter = "{$filter} AND (
				   c.beat_1_id in ({$beats})
				or c.beat_2_id in ({$beats})
				or c.beat_3_id in ({$beats}))";
		}

		if (count($this->regions))
		{
			$regions = sql_in_list($this->regions);
			$filter = "{$filter} AND 
				c.region_id in ({$regions})";
		}

		if (count($this->countries))
		{
			$countries = sql_in_list($this->countries);
			$filter = "{$filter} AND 
				c.country_id in ({$countries})";
		}

		if (count($this->roles))
		{
			$roles = sql_in_list($this->roles);
			$filter = "{$filter} AND 
				c.contact_role_id in ({$roles})";
		}

		if ($this->unique_emails_only)
		{
			// do not have an email appearing twice
			$unique_filter = "GROUP BY c.email";
		}

		$sql = "SELECT c.id
			FROM nr_contact c 
			WHERE {$filter}
			AND c.is_media_db_contact = 1
			AND c.is_unsubscribed = 0
			{$unique_filter}
			ORDER BY c.rand > {$rand} desc, 
				c.rand asc
			LIMIT {$limit}";

		$contact_ids = array();
		$dbr = $ci->db->query($sql);
		$contact_ids = Model_Base::values_from_db($dbr, 'id');

		$m_list = new Model_Contact_List();
		$m_list->date_created = Date::$now;
		$m_list->save();
		$m_list->add_all_contacts($contact_ids);
		
		return $m_list;
	}

}