<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Insights_Search_Company {

	use Search_Util_Trait;

	protected $params = array();

	public function __construct($params, $cache_ttl = 60)
	{
		$this->params = Raw_Data::from_object($params);
		$this->query_cache_ttl = $cache_ttl;
		$this->db = get_instance()->db;
	}

	public function fetch($offset = 0, $limit = 50)
	{
		$params = $this->params;
		$offset = (int) $offset;
		$limit = (int) $limit;
		$terms_filter = null;
		$response = new Raw_Data();

		if ($params->terms)
		{
			$terms = $this->extract_terms($params->terms);
			$response->terms = $terms;
			$si_table = $this->search_index_create($terms);
			$terms_filter = "INNER JOIN {$si_table} sct 
				ON sct.company_id = cm.id";
		}
		else
		{
			$response->terms = array();
		}

		$response->unfiltered = true
			&& count($response->terms) === 0;

		$sql = "SELECT cm.*
			FROM nr_company cm
			{$terms_filter}
			ORDER BY cm.id DESC
			LIMIT {$offset}, {$limit}";

		$results = $this->fetch_results($sql);
		if ($params->terms)
			$this->search_index_destroy($si_table);

		$response->results = $results;
		return $response;
	}

	protected function search_index_create($terms)
	{
		usort($terms, function($b, $a) {
			if (strlen($a) > strlen($b)) return +1;
			if (strlen($a) < strlen($b)) return -1;
			return 0;
		});

		$token = md5(microtime(true));
		$table = sprintf('nr_search_ct_%s', $token);

		// create a temporary table for matches
		$sql = "CREATE TEMPORARY TABLE 
			IF NOT EXISTS {$table} (
				company_id INT(11),
				PRIMARY KEY (company_id)
			) ENGINE=MEMORY";
		$this->db->query($sql);
		
		foreach ($terms as $index => $term)
		{
			if ($index === 0)
			{
				// insert company that has this term
				$sql = "INSERT IGNORE INTO {$table}
					SELECT si.company_id FROM nr_search_index_company si
					INNER JOIN nr_search_term st 
					ON st.id = si.search_term_id
					WHERE term = ?";

				$this->db->query($sql, array($term));
			}
			else
			{
				// remove all company without this term
				$sql = "DELETE sct FROM {$table} sct
					LEFT JOIN (
						SELECT si.company_id FROM nr_search_index_company si
						INNER JOIN nr_search_term st 
						ON st.id = si.search_term_id
						WHERE term = ?
					) si2 ON si2.company_id = sct.company_id
					WHERE si2.company_id IS NULL";

				$this->db->query($sql, array($term));
			}
		}

		return $table;
	}

	protected function search_index_destroy($table)
	{
		// clean up temporary table
		$sql = "DROP TABLE IF EXISTS {$table}";
		$this->db->query($sql);
	}

	protected function fetch_results($sql)
	{	
		$query = $this->db->cached($this->query_cache_ttl)->query($sql);
		$results = Model_Company::from_db_all($query);
		return $results;
	}

}