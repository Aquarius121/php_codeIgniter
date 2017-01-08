<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Media_Database_Filters_Trait {
	
	public function list_beats()
	{
		$beats = Model_Beat::list_all_beats_by_group();
		$response = new stdClass();
		$response->results = $beats;
		$this->json($response);
	}
	
	public function list_roles()
	{
		$criteria = array();
		$sort_order = array('role', 'asc');
		$roles = Model_Contact_Role::find_all($criteria, $sort_order);
		$response = new stdClass();
		$response->results = $roles;
		$this->json($response);
	}
	
	public function list_media_types()
	{
		$criteria = array();
		$sort_order = array('media_type', 'asc');
		$media_types = Model_Contact_Media_Type::find_all($criteria, $sort_order);
		$response = new stdClass();
		$response->results = $media_types;
		$this->json($response);
	}
	
	public function list_regions()
	{
		$filter = 1;
		$dependencies = $this->input->post('dependencies');
		$search = trim($this->input->post('search'));
		
		if (isset($dependencies['countries']) && count($dependencies['countries']))
		{
			$countries = sql_in_list($dependencies['countries']);
			$filter = "{$filter} AND re.country_id IN ({$countries})";
		}
		
		if ($search && $search_filter = sql_search_terms(array('re.name'), $search))
		{
			$sql = "SELECT re.*,
				cn.id AS country__id,
				cn.name AS country__name
				FROM nr_region re
				INNER JOIN nr_contact c 
				ON c.region_id = re.id
				LEFT JOIN nr_country cn
				ON re.country_id = cn.id
				WHERE {$filter} AND
				{$search_filter}
				GROUP BY re.id 
				ORDER BY re.name ASC
				LIMIT 100";
		}
		else
		{
			$sql = "SELECT reo.*,
				cn.id AS country__id,
				cn.name AS country__name
				FROM (
					SELECT re.* FROM nr_region re
					INNER JOIN (
						SELECT c.region_id, COUNT(c.region_id) AS count 
						FROM nr_contact c 
						GROUP BY c.region_id
					) c
					ON c.region_id = re.id
					WHERE {$filter}
					GROUP BY re.id 
					ORDER BY c.count DESC,
						c.region_id DESC
					LIMIT 100
				) reo
				LEFT JOIN nr_country cn
				ON reo.country_id = cn.id
				ORDER BY reo.name ASC";
		}
		
		$dbr = $this->db->query($sql);
		$regions = Model_Region::from_db_all($dbr, array(
			'country' => 'Model_Country',
		));
			
		$response = new stdClass();
		$response->results = $regions;
		$response->search = $search;
		$this->json($response);
	}
	
	public function list_countries()
	{
		$sql = "SELECT cn.* FROM nr_country cn
			INNER JOIN nr_contact c
			ON c.country_id = cn.id
			GROUP BY cn.id 
			ORDER BY cn.is_common DESC, 
				cn.name ASC";
				
		$dbr = $this->db->query($sql);
		$countries = Model_Country::from_db_all($dbr);
		$response = new stdClass();
		$response->results = $countries;
		$this->json($response);
	}
	
	public function list_coverages()
	{
		$criteria = array();
		$sort_order = array('coverage', 'asc');
		$coverages = Model_Contact_Coverage::find_all($criteria, $sort_order);
		$response = new stdClass();
		$response->results = $coverages;
		$this->json($response);
	}
	
	public function list_localities()
	{
		$filter = 1;
		$search = trim($this->input->post('search'));
		$dependencies = $this->input->post('dependencies');
		
		if (isset($dependencies['countries']) && count($dependencies['countries']))
		{
			$countries = sql_in_list($dependencies['countries']);
			$filter = "{$filter} AND lo.country_id IN ({$countries})";
		}
		
		if (isset($dependencies['regions']) && count($dependencies['regions']))
		{
			$regions = sql_in_list($dependencies['regions']);
			$filter = "{$filter} AND lo.region_id IN ({$regions})";
		}
		
		if ($search && $search_filter = sql_search_terms(array('lo.name'), $search))
		{
			$sql = "SELECT lo.*,
				re.id AS region__id,
				re.name AS region__name,
				cn.id AS country__id,
				cn.name AS country__name
				FROM nr_locality lo
				INNER JOIN nr_contact c 
				ON c.locality_id = lo.id
				LEFT JOIN nr_region re
				ON lo.region_id = re.id
				LEFT JOIN nr_country cn
				ON lo.country_id = cn.id
				WHERE {$filter} AND 
				{$search_filter}
				GROUP BY lo.id 
				ORDER BY lo.name ASC
				LIMIT 100";
		}
		else
		{
			$sql = "SELECT loo.*,
				re.id AS region__id,
				re.name AS region__name,
				cn.id AS country__id,
				cn.name AS country__name
				FROM (
					SELECT lo.* FROM nr_locality lo
					INNER JOIN (
						SELECT c.locality_id, COUNT(c.locality_id) AS count 
						FROM nr_contact c 
						GROUP BY c.locality_id
					) c
					ON c.locality_id = lo.id
					WHERE {$filter}
					GROUP BY lo.id 
					ORDER BY c.count DESC,
						c.locality_id DESC
					LIMIT 100
				) loo
				LEFT JOIN nr_region re
				ON loo.region_id = re.id
				LEFT JOIN nr_country cn
				ON loo.country_id = cn.id
				ORDER BY loo.name ASC";
		}
		
		$dbr = $this->db->query($sql);
		$localities = Model_Locality::from_db_all($dbr, array(
			'country' => 'Model_Country',
			'region' => 'Model_Region',
		));
		
		$response = new stdClass();
		$response->results = $localities;
		$response->search = $search;
		$this->json($response);
	}

	public function list_matching_filters()
	{
		$hash = md5(microtime(true));
		$search = $this->input->post('search');
		if (empty($search))
			return $this->json(new stdClass());

		// create a temporary table for tag matches
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS
			mdb_clt_{$hash} (
				clt_hash CHAR(16),
				PRIMARY KEY (clt_hash)
			) ENGINE=MEMORY";
		$this->db->query($sql);
		
		// split the search into terms
		$terms = preg_split('#\s+#', $search);
		$excluded_terms = array();
		
		foreach ($terms as $index => $term)
		{
			$term = trim($term);
			if (!$term) continue;
			
			// check for + or - at the start
			if (preg_match('#^[\+\-]#', $term))
			{
				$included = $term[0] === '+';
				$term = substr($term, 1);
				
				if (!$included)
				{
					$excluded_terms[] = $term;
					continue;
				}
			}

			// minimum length of term
			if (strlen($term) < 3) continue;
			
			if ($index === 0)
			{
				// insert matches that have this term
				$term = escape_and_quote(Tag::uniform($term));		
				$sql = "INSERT IGNORE INTO mdb_clt_{$hash}
					SELECT hash FROM nr_contact_linked_tags WHERE
					value = {$term}";
				$this->db->query($sql);
			}
			else
			{
				// remove all matches without this term
				$term = escape_and_quote(Tag::uniform($term));		
				$sql = "DELETE clth FROM mdb_clt_{$hash} clth
					LEFT JOIN nr_contact_linked_tags clt 
					ON clth.clt_hash = clt.hash
					AND clt.value = {$term}
					WHERE clt.hash IS NULL";
				$this->db->query($sql);
			}
		}
		
		foreach ($excluded_terms as $term)
		{
			// remove all matches with this term
			$term = escape_and_quote(Tag::uniform($term));		
			$sql = "DELETE clth FROM mdb_clt_{$hash} clth
				LEFT JOIN nr_contact_linked_tags clt 
				ON clth.clt_hash = clt.hash
				AND clt.value = {$term}
				WHERE clt.hash IS NOT NULL";
			$this->db->query($sql);	
		}

		// select the results that matched
		$sql = "SELECT clt.class, clt.linked 
			FROM mdb_clt_{$hash} clth 
			INNER JOIN nr_contact_linked_tags clt
			ON clt.hash = clth.clt_hash 
			ORDER BY relevance 
			DESC LIMIT 1000";
		$dbr = $this->db->query($sql);
		$results = Model_Base::from_db_all($dbr);

		// remove the temporary table when we are done
		$sql = "DROP TABLE IF EXISTS mdb_clt_{$hash}";
		$this->db->query($sql);

		foreach ($results as $result)
		{
			$class = $result->class;
			if (!isset($classes[$class]))
				$classes[$class] = array();
			$classes[$class][] = $result->linked;
		}

		$response = new stdClass();
		$response->results = new stdClass();

		if (isset($classes['beats']))
		{
			$beats = $classes['beats'];
			$matching = $this->_list_matching_filters_beats($beats);
			$response->results->beats = $matching;
		}

		if (isset($classes['countries']))
		{
			$countries = $classes['countries'];
			$matching = $this->_list_matching_filters_countries($countries);
			$response->results->countries = $matching;
		}

		if (isset($classes['regions']))
		{
			$regions = $classes['regions'];
			$matching = $this->_list_matching_filters_regions($regions);
			$response->results->regions = $matching;
		}

		if (isset($classes['localities']))
		{
			$localities = $classes['localities'];
			$matching = $this->_list_matching_filters_localities($localities);
			$response->results->localities = $matching;
		}

		if (isset($classes['roles']))
		{
			$roles = $classes['roles'];
			$matching = $this->_list_matching_filters_roles($roles);
			$response->results->roles = $matching;
		}

		if (isset($classes['media_types']))
		{
			$media_types = $classes['media_types'];
			$matching = $this->_list_matching_filters_media_types($media_types);
			$response->results->media_types = $matching;
		}
		
		$this->json($response);
	}

	protected function _list_matching_filters_beats($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT * FROM nr_beat WHERE id IN ({$filter})
			AND id != beat_group_id 
			GROUP BY id
			ORDER BY FIELD (id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Beat::from_db_all($dbr);
	}

	protected function _list_matching_filters_countries($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT * FROM nr_country
			WHERE id IN ({$filter}) 
			GROUP BY id
			ORDER BY FIELD (id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Country::from_db_all($dbr);
	}

	protected function _list_matching_filters_regions($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT re.*,
			cn.id AS country__id,
			cn.name AS country__name
			FROM nr_region re
			LEFT JOIN nr_country cn
			ON re.country_id = cn.id
			WHERE re.id IN ({$filter})
			GROUP BY re.id
			ORDER BY FIELD (re.id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Region::from_db_all($dbr, array(
			'country' => 'Model_Country',
		));
	}

	protected function _list_matching_filters_localities($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT lo.*,
			re.id AS region__id,
			re.name AS region__name,
			cn.id AS country__id,
			cn.name AS country__name
			FROM nr_locality lo
			LEFT JOIN nr_region re
			ON lo.region_id = re.id
			LEFT JOIN nr_country cn
			ON lo.country_id = cn.id
			WHERE lo.id IN ({$filter})
			GROUP BY lo.id
			ORDER BY FIELD (lo.id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Locality::from_db_all($dbr, array(
			'country' => 'Model_Country',
			'region' => 'Model_Region',
		));
	}

	protected function _list_matching_filters_roles($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT * FROM nr_contact_role
			WHERE id IN ({$filter}) 
			GROUP BY id
			ORDER BY FIELD (id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Contact_Role::from_db_all($dbr);
	}

	protected function _list_matching_filters_media_types($filter_list)
	{
		$filter = sql_in_list($filter_list);
		$sql = "SELECT * FROM nr_contact_media_type 
			WHERE id IN ({$filter}) 
			GROUP BY id
			ORDER BY FIELD (id, {$filter})
			LIMIT 10";
		$dbr = $this->db->query($sql);
		return Model_Contact_Media_Type::from_db_all($dbr);
	}

	public function match_filter_increase_relevance()
	{
		// split the search into terms
		$class = $this->input->post('class');
		$linked = $this->input->post('linked');
		$search = $this->input->post('search');
		$terms = preg_split('#\s+#', $search);
		
		foreach ($terms as $term)
		{
			// remove any non-standard characters
			$term = preg_replace('#[^a-z0-9\-\+]#i', '%', trim($term));
			if (!trim($term)) continue;

			// minimum length of term
			if (strlen($term) < 3) continue;
			
			// check for + or - at the start
			if (preg_match('#^[\+\-]#', $term))
			{
				$included = $term[0] === '+';
				$term = substr($term, 1);
				if (!$included) continue;
			}

			// increase the relevance for this linked
			// for each tag that matched
			$sql = "UPDATE nr_contact_linked_tags
				SET relevance = relevance + 1 
				WHERE value LIKE '{$term}%'
				AND class = ? AND linked = ?";
			$this->db->query($sql, array($class, $linked));
		}
	}
	
}

?>