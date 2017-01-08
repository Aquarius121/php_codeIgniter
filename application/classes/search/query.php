<?php

abstract class Search_Query {

	use Search_Util_Trait;

	protected $db             = null;
	protected $buildTable     = null;
	protected $indexTable     = 'nr_search_index';
	protected $termsTable     = 'nr_search_term';
	protected $otherField     = 'id';
	protected $buildLimit     = 10000;
	protected $initialInsert  = false;
	protected $queryTime      = 0;	
	protected $terms          = null;
	
	// With inversion enabled it is possible
	// to query with entirely negative terms
	// (that is terms that should not match)
	// such as the complete query "-business" 
	// that should return all results that
	// do not contain the term "business".
	// ----------------------------------
	// When activated the build table will no
	// longer contain results that match
	// but instead will contain results that
	// do not match. Any external SQL queries
	// will need to check the inverted
	// status and behave accordingly. 
	// ---------------------------------
	// The results() function and count()
	// function automatically considers
	// the status of inversion but count()
	// may suffer from poor performance
	// with lots of results.

	protected $inverted       = false;
	protected $allowInversion = false;

	public function __construct(CI_DB $db)
	{
		$token = md5(microtime(true));
		$table = sprintf('nr_search_ct_%s', $token);
		$this->buildTable = $table;
		$this->db = $db;

		$this->create_table();
	}

	// maximum number of results in each 
	// step of the build process
	public function set_build_limit($limit)
	{
		$this->buildLimit = (int) $limit;
	}

	public function __destruct()
	{
		$this->destroy_table();
	}

	public function results_table()
	{
		return $this->buildTable;
	}

	public function table()
	{
		return $this->buildTable;
	}

	public function terms()
	{
		return $this->terms;
	}

	public function time()
	{
		return $this->queryTime;
	}

	public function count()
	{
		if ($this->inverted())
		{
			$sql = "SELECT COUNT(*) AS count
				FROM {$this->indexTable} si
				LEFT JOIN {$this->buildTable} bt
				ON bt.{$this->otherField} = si.{$this->otherField}
				WHERE bt.{$this->otherField} IS NULL";
		}
		else
		{
			$sql = "SELECT COUNT(*) AS count
				FROM {$this->buildTable}";
		}

		$count = (int) $this->db->query($sql)->row()->count;
		return $count;
	}

	public function enable_inversion()
	{
		$this->allowInversion = true;
	}

	public function inverted()
	{
		return (bool) $this->inverted;
	}

	public function results($offset = 0, $limit = 100)
	{
		$offset = (int) $offset;
		$limit = (int) $limit;
		
		if ($this->inverted())
		{
			$sql = "SELECT si.{$this->otherField}, 0 as quality
				FROM {$this->indexTable} si
				LEFT JOIN {$this->buildTable} bt
				ON bt.{$this->otherField} = si.{$this->otherField}
				WHERE bt.{$this->otherField} IS NULL
				LIMIT {$offset}, {$limit}";
		}
		else
		{
			$sql = "SELECT * FROM {$this->buildTable}
				ORDER BY quality DESC
				LIMIT {$offset}, {$limit}";
		}

		$dbr = $this->db->query($sql);
		$results = array();

		foreach ($dbr->result() as $result)
			$results[] = (object) get_object_vars($result);

		return $results;
	}

	public function query($query, $limit = true)
	{
		if ($limit === true)
			$limit = (int) $this->buildLimit;
		if ($limit === false)
			$limit = PHP_INT_MAX;		

		$tsStart = microtime(true);
		$terms = $this->parse_query($query);
		$this->resolve_terms($terms);
		$this->terms = clone $terms;
		
		if (!count($terms->positive) && 
			 !count($terms->negative))
			return;

		$this->find_results($terms, $limit);
		$tsEnd = microtime(true);

		// record time for query in milliseconds
		$this->queryTime = ($tsEnd - $tsStart) * 1000;
	}

	protected function resolve_terms(&$terms)
	{
		$index = array();
		$termsSql = sql_in_list(array_merge(
			$terms->positive, $terms->negative));

		$sql = "SELECT * FROM {$this->termsTable} WHERE term IN ($termsSql)";
		$mTerms = Model_Search_Term::from_sql_all($sql);
		foreach ($mTerms as $mTerm)
			$index[$mTerm->term] = $mTerm;

		foreach ($terms->positive as $k => $term)
		{
			if (isset($index[$term]))
			     $terms->positive[$k] = $index[$term];
			else unset($terms->positive[$k]);
		}

		foreach ($terms->negative as $k => $term)
		{
			if (isset($index[$term]))
			     $terms->negative[$k] = $index[$term];
			else unset($terms->negative[$k]);
		}

		$terms->positive = array_filter($terms->positive);
		$terms->negative = array_filter($terms->negative);

		usort($terms->positive, function($a, $b) {
			return spaceship($a->count, $b->count);
		});

		usort($terms->negative, function($a, $b) {
			return -1 * spaceship($a->count, $b->count);
		});

		return $terms;
	}

	protected function find_results($terms, $limit)
	{		
		$positive = array_map(function($mTerm) {
			return (int) $mTerm->id;
		}, $terms->positive);

		$negative = array_map(function($mTerm) {
			return (int) $mTerm->id;
		}, $terms->negative);

		if (!count($positive))
		{
			if (!$this->allowInversion) return;
			$this->inverted = true;
			$positive = $negative;
			$negative = array();
		}

		$positiveJoins   = array();
		$positiveQuality = array();
		$negativeJoins   = array();
		$negativeNulls   = array();

		for ($i = 1; $i < count($positive); $i++)
		{
			$id = $positive[$i];
			$alias = sprintf('siP%d', $i);
			$join = "INNER JOIN {$this->indexTable} {$alias}
				ON  {$alias}.{$this->otherField}
				      = siP0.{$this->otherField}
				AND {$alias}.search_term_id = {$id}";
			$plusQuality = "+ {$alias}.quality";
			$positiveJoins[] = $join;
			$positiveQuality[] = $plusQuality;
		}

		for ($i = 0; $i < count($negative); $i++)
		{
			$id = $negative[$i];
			$alias = sprintf('siN%d', $i);
			$join = "LEFT JOIN {$this->indexTable} {$alias}
				ON  {$alias}.{$this->otherField}
				      = siP0.{$this->otherField}					    
				AND {$alias}.search_term_id = {$id}";
			$nullCheck = "AND {$alias}.search_term_id IS NULL";
			$negativeJoins[] = $join;
			$negativeNulls[] = $nullCheck;
		}

		$positiveJoins   = implode(PHP_EOL, $positiveJoins);
		$positiveQuality = implode(PHP_EOL, $positiveQuality);
		$negativeJoins   = implode(PHP_EOL, $negativeJoins);
		$negativeNulls   = implode(PHP_EOL, $negativeNulls);

		$sql = "INSERT IGNORE INTO {$this->buildTable}
			SELECT siP0.{$this->otherField},
				(siP0.quality {$positiveQuality}) AS quality
			FROM {$this->indexTable} siP0
			{$positiveJoins}
			{$negativeJoins}
			WHERE siP0.search_term_id = {$positive[0]}
				   {$negativeNulls}
			ORDER BY siP0.{$this->otherField} DESC
			LIMIT {$limit}";

		$this->db->query($sql);
	}

	protected function destroy_table()
	{
		// destroy temporary table
		$sql = "DROP TABLE IF EXISTS {$this->buildTable}";
		$this->db->query($sql);
	}

	protected function create_table()
	{
		// create temporary table 
		$sql = "CREATE TEMPORARY TABLE 
			IF NOT EXISTS {$this->buildTable} (
				{$this->otherField} INT, 
				quality MEDIUMINT,
				PRIMARY KEY ({$this->otherField}),
				INDEX quality (quality)
			) ENGINE=MEMORY";
		$this->db->query($sql);
	}

}



