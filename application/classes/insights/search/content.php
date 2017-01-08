<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Insights_Search_Content {

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
		$response = new Raw_Data();

		$types_filter = null;
		$terms_filter = null;
		$beats_filter = null;
		$date_from_filter = null;
		$date_to_filter = null;
		$optional_ordering = null;

		if ($params->terms)
		{
			$response->terms = $params->terms;
			$sqwc = new Search_Query_With_Content($this->db);
			$sqwc->query($params->terms, false);
			$table = $sqwc->table();
			$terms_filter = "INNER JOIN {$table} tf ON tf.content_id = c.id";
			$optional_ordering = "tf.quality DESC,";
		}
		else
		{
			$response->terms = null;
		}

		if ($params->types)
		{
			$types = array();
			foreach ($params->types as $type)
				if (Model_Content::is_internal_type($type))
					$types[] = $type;
			$response->types = $types;
			$types = sql_in_list($types);
			$types_filter = "AND c.type IN ({$types})";
		}
		else
		{
			$types = Model_Content::internal_types();
			$response->types = $types;
			$types = sql_in_list($types);
			$types_filter = "AND c.type IN ({$types})";
		}

		if ($params->beats)
		{
			$beats = (array) $params->beats;
			$beats = array_map('intval', $beats);
			$response->beats = $beats;
			$beats = sql_in_list($beats);
			$beats_filter = "INNER JOIN 
				nr_beat_x_content bxc
				ON bxc.content_id = c.id
				AND bxc.beat_id IN ({$beats})";
		}
		else
		{
			$response->beats = array();
		}

		if ($params->date_from)
		{
			$date = escape_and_quote($params->date_from);
			$date_from_filter = "AND c.date_publish >= {$date}";
			$response->date_from = $params->date_from;
		}
		else
		{
			$response->date_from = (string) Date::first();
		}

		if ($params->date_to)
		{
			$date = escape_and_quote($params->date_to);
			$date_to_filter = "AND c.date_publish <= {$date}";
			$response->date_to = $params->date_to;
		}
		else
		{
			$response->date_to = (string) Date::utc();
		}

		$response->unfiltered = true
			&& $response->types == Model_Content::internal_types()
			&& count($response->terms) === 0;

		$sql = "SELECT c.id, c.type
			FROM nr_content c 
			{$terms_filter}
			{$beats_filter}
			WHERE c.is_published = 1
			{$types_filter}
			{$date_from_filter}
			{$date_to_filter}
			ORDER BY
				{$optional_ordering}
				c.date_publish DESC
			LIMIT {$offset}, {$limit}";

		$results = $this->fetch_results($sql);
		$response->results = $results;
		return $response;
	}

	protected function fetch_results($sql)
	{	
		$results = array();
		$id_filter = array();
		$query = $this->db->cached($this->query_cache_ttl)->query($sql);

		foreach ($query->result() as $result)
		{
			if (!isset($id_filter[$result->type]))
				$id_filter[$result->type] = array();
			$id_filter[$result->type][] = $result->id;
		}

		foreach ($id_filter as $type => $ids)
		{			
			$ids = sql_in_list($ids);
			$sql = "SELECT *,
				UNIX_TIMESTAMP(c.date_publish) as ts
				FROM nr_content c 
				LEFT JOIN nr_content_data cd 
				ON c.id = cd.content_id
				LEFT JOIN nr_pb_{$type} tl
				ON c.id = tl.content_id
				WHERE c.id IN ({$ids})";
			
			// this cannot use from_db_all because
			// its adding to the previous loop
			$query = $this->db->query($sql);
			foreach ($query->result() as $result)
				$results[] = Model_Content::from_db_object($result);
		}

		$class = get_class($this);
		$method = 'combined_sort';
		$callable = array($class, $method);
		usort($results, $callable);

		return $results;
	}
	
	public static function combined_sort($a, $b)
	{
		if ($a->ts > $b->ts) return -1;
		if ($a->ts < $b->ts) return +1;
		return 0;
	}

}
