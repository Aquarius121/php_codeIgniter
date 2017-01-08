<?php

load_controller('cli/base');

class Builder_Controller extends CLI_Base {
	
	protected $trace_enabled = false;
	protected $trace_time = true;

	// TODO 
	// change trim_index_content to be:
	// 1. create new table
	// 2. copy correct data
	// 3. rename
	// ------------------------------------------------------
	// ------------------------------------------------------
	// ------------------------------------------------------
	// ------------------------------------------------------

	public function index()
	{
		$this->error('usage: ... search builder <build|trim>');
	}
	
	public function build()
	{
		// allow at most 1 process
		if ($this->process_count() > 1)
			return;
		
		$this->build_index_content();
		$this->build_index_companies();
	}

	public function trim()
	{
		// allow at most 1 process
		while ($this->process_count() > 1)
			sleep(5);

		$this->trim_index_content();
		$this->trim_index_companies();
	}

	public function trim_index_content()
	{
		set_time_limit(0);
		$builder = new Search_Builder_With_Content($this->db);
		$counter = 0;

		while (true) 
		{
			set_time_limit(10000);

			$sql = "SELECT sb.content_id AS id
				FROM nr_search_builder_content sb
				LEFT JOIN nr_content c ON sb.content_id = c.id
				WHERE c.id IS NULL ORDER BY sb.content_id ASC
				LIMIT 1000";

			$dbr = $this->db->query($sql);
			$ids = Model::values_from_db($dbr, 'id');
			if (!count($ids)) break;

			foreach ($ids as $id)
			{
				$builder->remove($id);
				$this->trace(++$counter, $id);

				// remove meta record from the builder table
				$sql = "DELETE FROM nr_search_builder_content WHERE content_id = ?";
				$this->db->query($sql, array($id));
			}
		}
	}

	public function trim_index_companies()
	{
		set_time_limit(0);
		$builder = new Search_Builder_With_Company($this->db);
		$counter = 0;

		while (true) 
		{
			set_time_limit(10000);

			$sql = "SELECT sb.company_id AS id
				FROM nr_search_builder_company sb
				LEFT JOIN nr_company cm ON sb.company_id = cm.id
				WHERE cm.id IS NULL ORDER BY sb.company_id ASC
				LIMIT 1000";

			$dbr = $this->db->query($sql);
			$ids = Model::values_from_db($dbr, 'id');
			if (!count($ids)) break;

			foreach ($ids as $id)
			{
				$builder->remove($id);
				$this->trace(++$counter, $id);

				// remove meta record from the builder table
				$sql = "DELETE FROM nr_search_builder_company WHERE company_id = ?";
				$this->db->query($sql, array($id));
			}
		}
	}

	public function build_index_content()
	{
		$builder = new Search_Builder_With_Content($this->db);
		$counter = 0;

		while (true)
		{			
			set_time_limit(10000);

			$sql = "SELECT c.* FROM nr_content c 
				LEFT JOIN nr_search_builder_content sb
				ON c.id = sb.content_id
				WHERE c.is_published = 1
				AND sb.content_id IS NULL
				ORDER BY c.id DESC
				LIMIT 1000";

			$m_content_a = Model_Content::from_sql_all($sql);
			if (!count($m_content_a)) return;

			foreach ($m_content_a as $m_content)
			{				
				$builder->build($m_content);
				$this->trace(++$counter, $m_content->id);

				// record date this content was indexed
				$date = escape_and_quote(Date::utc());
				$sql = "INSERT IGNORE INTO nr_search_builder_content
					VALUES ({$m_content->id}, {$date})
					ON DUPLICATE KEY UPDATE date_indexed = {$date}";
				$this->db->query($sql);
			}
		}
	}

	public function build_index_companies()
	{
		$builder = new Search_Builder_With_Company($this->db);
		$counter = 0;

		while (true)
		{
			set_time_limit(10000);
			
			$sql = "SELECT cm.* FROM nr_company cm 
				LEFT JOIN nr_search_builder_company sb ON cm.id = sb.company_id
				-- Give the user 24 hours to make sure the company name is correct
				WHERE cm.date_created < DATE_SUB(UTC_TIMESTAMP, INTERVAL 24 HOUR) 
				AND cm.name NOT LIKE 'COMPANY %'
				AND sb.company_id IS NULL
				ORDER BY cm.id DESC
				LIMIT 1000";

			$m_company_a = Model_Company::from_sql_all($sql);
			if (!count($m_company_a)) return;

			foreach ($m_company_a as $m_company)
			{				
				$builder->build($m_company);
				$this->trace(++$counter, $m_company->id);

				// record date this content was indexed
				$date = escape_and_quote(Date::utc());
				$sql = "INSERT IGNORE INTO nr_search_builder_company
					VALUES ({$m_company->id}, {$date})
					ON DUPLICATE KEY UPDATE date_indexed = {$date}";
				$this->db->query($sql);
			}
		}
	}

}