<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class News_Center_Listing extends Website_Base {
	
	protected $limit = 10;
	protected $offset = 0;
	protected $rss_limit = 50;
	protected $rss_enabled = false;
	protected $chunkination;
	protected $query_cache_ttl = 300;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->offset = (int)
			$this->input->get('offset');
	}
	
	protected function find_results($sql, $params = null)
	{	
		$results = array();
		$id_filter = array();
		$orderQuality = array();
		$query = $this->db->cached->query($sql, $params, $this->query_cache_ttl);

		foreach ($query->result() as $result)
		{
			if (!isset($id_filter[$result->type]))
				$id_filter[$result->type] = array();
			$id_filter[$result->type][] = $result->id;
			if (isset($result->orderQuality))
			     $orderQuality[$result->id] = $result->orderQuality;
			else $orderQuality = 0;
		}

		$total_results = $query->found_rows();
		$this->chunkination->set_total($total_results);
		if ($this->chunkination->is_out_of_bounds())
			return array();

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
				WHERE c.id IN ({$ids})
				ORDER BY c.date_publish DESC";
			
			$query = $this->db->query($sql);
			foreach ($query->result() as $result)
				$results[] = Model_Content::from_db_object($result);			
		}

		foreach ($results as $result)
			$result->quality = $orderQuality[$result->id];

		$class = get_class($this);
		$method = 'combined_sort';
		$callable = array($class, $method);
		usort($results, $callable);

		return $results;
	}

	public static function combined_sort($a, $b)
	{
		if ($a->quality > $b->quality) return -1;
		if ($a->quality < $b->quality) return +1;
		if ($a->ts > $b->ts) return -1;
		if ($a->ts < $b->ts) return +1;
		return 0;
	}
}
