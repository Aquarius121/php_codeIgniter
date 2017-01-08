<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Media_Database_Controller_Trait {

	protected function process_results($results)
	{
		foreach ($results as $result)
			if (!empty($result->profile))
				$result->profile_data = $result->profile->raw_data();
		return $results;
	}
	
	protected function process_response($response)
	{
		return $response;
	}

	protected function normalize_options(&$options)
	{
		if (empty($options->chunk)) $options->chunk = 1;
		if (empty($options->search)) $options->search = null;
		if (!isset($options->sort_column)) $options->sort_column = null;
		if (!isset($options->sort_reverse)) $options->sort_reverse = false;
		if (!isset($options->unique_only)) $options->unique_only = false;
		if (!isset($options->pictures_only)) $options->pictures_only = false;
		if (empty($options->chunk_size))	$options->chunk_size = 1;
		if (!isset($options->filters)) $options->filters = new stdClass();
		if (!isset($options->filters->beats) || !is_array($options->filters->beats))
			$options->filters->beats = array();
		if (!isset($options->filters->roles) || !is_array($options->filters->roles))
			$options->filters->roles = array();
		if (!isset($options->filters->coverages) || !is_array($options->filters->coverages))
			$options->filters->coverages = array();
		if (!isset($options->filters->media_types) || !is_array($options->filters->media_types))
			$options->filters->media_types = array();
		if (!isset($options->filters->countries) || !is_array($options->filters->countries))
			$options->filters->countries = array();
		if (!isset($options->filters->regions) || !is_array($options->filters->regions))
			$options->filters->regions = array();
		if (!isset($options->filters->localities) || !is_array($options->filters->localities))
			$options->filters->localities = array();
		if (!isset($options->filters->zip) || !is_array($options->filters->zip))
			$options->filters->zip = array();
		
		$options->chunk = (int) $options->chunk;
		$options->chunk_size = (int) $options->chunk_size;
	}

	protected function execute($_options)
	{
		if (is_object($_options))
		     $options = clone $_options;
		else $options = new stdClass();
		$this->normalize_options($options);
		$this->__execute_internal($options);
	}

	private function __execute_internal(&$options)
	{
		$chunkination = new Chunkination($options->chunk);
		$chunkination->set_chunk_size($options->chunk_size);
		$limit_str = $chunkination->limit_str();
		
		$filter = new stdClass();
		$filter->hash = md5(microtime(true));
		$filter->callbacks = array();
		$filter->condition = 1;
		$filter->tables = null;
		
		$this->add_search($options->search, $filter);
		$this->add_filter('beats', $options->filters->beats, $filter);
		$this->add_filter('roles', $options->filters->roles, $filter);
		$this->add_filter('coverages', $options->filters->coverages, $filter);
		$this->add_filter('countries', $options->filters->countries, $filter);
		$this->add_filter('regions', $options->filters->regions, $filter);
		$this->add_filter('localities', $options->filters->localities, $filter);
		$this->add_filter('media_types', $options->filters->media_types, $filter);
		
		$sort_order = $this->add_sort_order($options);

		// filters for listing options
		$unique_filter = null;
		$pictures_filter = null;

		if ($options->unique_only)
		{
			// prevent multiple contacts
			// with the same email from display
			$unique_filter = "GROUP BY c.email";
		}
		
		if ($options->pictures_only)
		{
			// prevent multiple contacts
			// with the same email from display
			$pictures_filter = "INNER JOIN 
				nr_contact_picture cp 
				ON c.id = cp.contact_id";
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id
			FROM nr_contact c
			{$filter->tables}
			{$pictures_filter}
			WHERE c.is_media_db_contact = 1
			AND {$filter->condition}
			{$unique_filter}
			ORDER BY {$sort_order}
			{$limit_str}";

		$id_list = array();
		$db_result = $this->db->query($sql);
		foreach ($db_result->result() as $row)
			$id_list[] = $row->id;
				
		$m_contacts = array();
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		// check for out of bounds
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds()) 
		{
			$options->chunk = 1;
			// call this function again with corrected options
			return $this->__execute_internal($options);
		} 
		
		$m_contacts = $this->__execute_internal_load($id_list, $sort_order);		
		$m_contacts = $this->process_results($m_contacts);	
		foreach ($filter->callbacks as $callback)
			$callback($this, $options, $filter);
		
		$response = new stdClass();
		$template = 'shared/media_database/chunkination';
		$response->chunkination = $chunkination;
		$response->chunkination_html = $chunkination->render($template);
		$response->chunk = $options->chunk;
		$response->results = $m_contacts;
		$response->total = $chunkination->total();
		if (isset($options->request_index))
			$response->request_index = $options->request_index;
		
		$response = $this->process_response($response);
		$this->json($response);
	}

	protected function __execute_internal_load($id_list, $sort_order = 1)
	{
		if (count($id_list))
		{
			$profile_prefixes = Model_Contact_Profile::__prefixes('profile', 'profile');
			$id_str = sql_in_list($id_list);
			$sql = "SELECT c.*, 
				cp.finger AS picture__finger,
				cp.thumb AS picture__thumb,
				cp.original AS picture__original,
				re.id AS region__id,
				re.name AS region__name,
				re.abbr AS region__abbr,
				lo.id AS locality__id,
				lo.name AS locality__name,
				cn.id AS country__id,
				cn.name AS country__name,
				cr.id AS contact_role__id,
				cr.role AS contact_role__role,
				cmt.id AS contact_media_type__id,
				cmt.media_type AS contact_media_type__media_type,
				b1.id AS beat_1__id,
				b1.beat_group_id AS beat_1__beat_group_id,
				b1.name AS beat_1__name,
				b2.id AS beat_2__id,
				b2.beat_group_id AS beat_2__beat_group_id,
				b2.name AS beat_2__name,
				b3.id AS beat_3__id,
				b3.beat_group_id AS beat_3__beat_group_id,
				b3.name AS beat_3__name,
				{$profile_prefixes}
				FROM nr_contact c 
				LEFT JOIN nr_contact_picture cp ON c.id = cp.contact_id
				LEFT JOIN nr_region re ON c.region_id = re.id
				LEFT JOIN nr_locality lo ON c.locality_id = lo.id
				LEFT JOIN nr_country cn ON c.country_id = cn.id
				LEFT JOIN nr_contact_role cr ON c.contact_role_id = cr.id
				LEFT JOIN nr_contact_media_type cmt ON c.contact_media_type_id = cmt.id
				LEFT JOIN nr_beat b1 ON c.beat_1_id = b1.id
				LEFT JOIN nr_beat b2 ON c.beat_2_id = b2.id
				LEFT JOIN nr_beat b3 ON c.beat_3_id = b3.id
				LEFT JOIN nr_contact_x_contact_profile cxcp ON cxcp.contact_id = c.id
				LEFT JOIN nr_contact_profile profile ON profile.id = cxcp.contact_profile_id
				WHERE c.id IN ({$id_str})
				GROUP BY c.id 
				ORDER BY {$sort_order}";
							
			$constructs = array();
			$constructs['region'] = 'Model_Region';
			$constructs['locality'] = 'Model_Locality';
			$constructs['country'] = 'Model_Country';
			$constructs['contact_role'] = 'Model_Contact_Role';
			$constructs['contact_media_type'] = 'Model_Contact_Media_Type';
			$constructs['beat_1'] = 'Model_Beat';
			$constructs['beat_2'] = 'Model_Beat';
			$constructs['beat_3'] = 'Model_Beat';
			$constructs['picture'] = 'Model_Contact_Picture';
			$constructs['profile'] = 'Model_Contact_Profile';
			
			$db_result = $this->db->query($sql);
			$m_contacts = Model_Contact::from_db_all($db_result, $constructs);
			return $m_contacts;
		}

		return array();
	}
	
	protected function execute_id_list($_options)
	{
		if (is_object($_options))
		     $options = clone $_options;
		else $options = new stdClass();
		$this->normalize_options($options);
		
		$filter = new stdClass();
		$filter->hash = md5(microtime(true));
		$filter->callbacks = array();
		$filter->condition = 1;
		$filter->tables = null;
		
		$this->add_search($options->search, $filter);
		$this->add_filter('beats', $options->filters->beats, $filter);
		$this->add_filter('roles', $options->filters->roles, $filter);
		$this->add_filter('coverages', $options->filters->coverages, $filter);
		$this->add_filter('countries', $options->filters->countries, $filter);
		$this->add_filter('regions', $options->filters->regions, $filter);
		$this->add_filter('localities', $options->filters->localities, $filter);
		$this->add_filter('zips', $options->filters->zips, $filter);
		$this->add_filter('media_types', $options->filters->media_types, $filter);
		
		$sort_order = $this->add_sort_order($options);

		if (@$this->vd->is_admin_panel)
		     $limit = 1000000;
		else $limit = 100000;

		// filters for listing options
		$unique_filter = null;
		$pictures_filter = null;

		if ($options->unique_only)
		{
			// prevent multiple contacts
			// with the same email from display
			$unique_filter = "GROUP BY c.email";
		}
		
		if ($options->pictures_only)
		{
			// prevent multiple contacts
			// with the same email from display
			$pictures_filter = "INNER JOIN 
				nr_contact_picture cp 
				ON c.id = cp.contact_id";
		}
			
		$sql = "SELECT c.id
			FROM nr_contact c 
			{$filter->tables}
			{$pictures_filter}
			WHERE c.is_media_db_contact = 1
			AND {$filter->condition}
			{$unique_filter}
			ORDER BY {$sort_order}
			LIMIT {$limit}";

		$results = array();
		$db_result = $this->db->query($sql);
		foreach ($db_result->result() as $record)
			$results[] = $record->id;
		return $results;
	}
	
	protected function add_filter($name, $values, $filter)
	{
		$filter_name = "media_database_filter_{$name}";
		if (!class_exists($filter_name)) throw new Exception();
		$filter_ob = new $filter_name($this->db);
		$filter_value = $filter_ob->build($values);
		$filter->condition = sprintf('%s AND (%s)', 
			$filter->condition, $filter_value);
		return $filter;
	}
	
	protected function add_search($search, $filter)
	{
		if (empty($search)) return 1;
		
		// create a temporary table for tag matches
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS
			mdb_ct_{$filter->hash} (
				contact_id INT(11),
				PRIMARY KEY (contact_id)
			) ENGINE=MEMORY";
		$this->db->query($sql);
		
		// split the search into terms
		$terms = preg_split('#\s+#', $search);
		$excluded_terms = array();
		$included_terms = array();
		$valid_terms = array();
		
		foreach ($terms as $index => $term)
		{
			$term = trim($term);
			if (!$term) continue;

			// term is valid, adding
			// * we make all tags uniform
			//   so additional characters are 
			//   now accepted as valid
			$valid_terms[] = $term;

			// check for + or - at the start
			if (preg_match('#^[\+\-]#', $term))
			{
				$included = $term[0] === '+';
				$term = substr($term, 1);
				
				if (!$included)
				{
					$term = Tag::uniform($term);
					$excluded_terms[] = $term;
					continue;
				}
			}
			
			if (!count($included_terms))
			{
				$term = Tag::uniform($term);
				$included_terms[] = $term;

				// insert contacts that have this term
				$term = escape_and_quote($term);
				$sql = "INSERT IGNORE INTO mdb_ct_{$filter->hash}
					SELECT contact_id FROM nr_contact_tag WHERE
					value = {$term}";
				$this->db->query($sql);
			}
			else
			{
				$term = Tag::uniform($term);
				$included_terms[] = $term;

				// remove all contacts without this term
				$term = escape_and_quote($term);
				$sql = "DELETE cth FROM mdb_ct_{$filter->hash} cth
					LEFT JOIN nr_contact_tag ct 
					ON cth.contact_id = ct.contact_id
					AND ct.value = {$term}
					WHERE ct.contact_id IS NULL";
				$this->db->query($sql);
			}
		}

		// no valid terms => all results
		if (!count($valid_terms))
			return 1;
		
		// just excluded terms?
		// => add all contacts
		if (!count($included_terms))
		{
			$sql = "INSERT IGNORE INTO mdb_ct_{$filter->hash}
				SELECT id FROM nr_contact WHERE is_media_db_contact = 1";
			$this->db->query($sql);
		}

		foreach ($excluded_terms as $term)
		{
			// remove all contacts with this term
			$term = escape_and_quote($term);
			$sql = "DELETE cth FROM mdb_ct_{$filter->hash} cth
				LEFT JOIN nr_contact_tag ct 
				ON cth.contact_id = ct.contact_id
				AND ct.value = {$term}
				WHERE ct.contact_id IS NOT NULL";
			$this->db->query($sql);	
		}
			
		// remove the temporary table when we are done
		$filter->callbacks[] = function($ci, $options, $filter) {
			$sql = "DROP TABLE IF EXISTS mdb_ct_{$filter->hash}";
			$ci->db->query($sql);
		};
		
		// join with temporary table
		$filter->tables = "{$filter->tables} INNER JOIN 
			mdb_ct_{$filter->hash} cth
			ON c.id = cth.contact_id";
		
		return $filter;
	}
	
	protected function add_filter_modal()
	{
		$add_filter_modal = new Modal();
		$add_filter_modal->set_title('Filter Results');
		$modal_view = 'shared/media_database/add_filter_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$add_filter_modal->set_content($modal_content);
		$modal_view = 'shared/media_database/add_filter_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$add_filter_modal->set_footer($modal_content);
		$add_filter_modal->set_id('md-add-filter-modal');
		$this->add_eob($add_filter_modal->render(500, 400));
		$this->vd->add_filter_modal_id = $add_filter_modal->id;
	}

	protected function add_options_modal()
	{
		$content_view = 'shared/media_database/list_options_modal';
		$modal_content = $this->load->view($content_view, null, true);
		$options_modal = new Modal();
		$options_modal->set_title('View Options');
		$options_modal->set_content($modal_content);
		$options_modal->set_id('md-options-modal');
		$this->add_eob($options_modal->render(400, 400));
		$this->vd->options_modal_id = $options_modal->id;
	}
	
	protected function add_sort_order($options)
	{
		if (!$options->sort_column) return '1 desc';
		if ($options->sort_column == 'contact' && !$options->sort_reverse)
			return 'c.first_name asc, c.last_name asc';
		if ($options->sort_column == 'contact' && $options->sort_reverse)
			return 'c.first_name desc, c.last_name desc';
		if ($options->sort_column == 'company' && !$options->sort_reverse)
			return 'c.company_name asc';
		if ($options->sort_column == 'company' && $options->sort_reverse)
			return 'c.company_name desc';
		if ($options->sort_column == 'location' && !$options->sort_reverse)
			return 'lo.name asc, re.name asc, cn.name asc';
		if ($options->sort_column == 'location' && $options->sort_reverse)
			return 'lo.name desc, re.name desc, cn.name desc';
		if ($options->sort_column == 'beats' && !$options->sort_reverse)
			return 'b1.name asc, b2.name asc, b3.name asc';
		if ($options->sort_column == 'beats' && $options->sort_reverse)
			return 'b1.name desc, b2.name desc, b3.name desc';
	}

}

?>