<?php

abstract class Search_Builder {

	use Search_Util_Trait;

	protected $db = null;
	protected $indexTable = 'nr_search_index';
	protected $termsTable = 'nr_search_terms';	
	protected $otherField = 'id';

	public function __construct(CI_DB $db)
	{
		$this->db = $db;
	}

	public function build_terms(StringKey_Array &$terms, $text, $quality, $maxQuality = 0)
	{
		if ($maxQuality < $quality)
			$maxQuality = $quality;

		$newTerms = $this->extract_terms($text, false);

		foreach ($newTerms as $term)
		{
			if (isset($terms[$term]))
			{
				if ($terms[$term] >= $maxQuality) continue;
				$terms[$term] = min($maxQuality, $terms[$term] + $quality);
			}
			else
			{
				$terms[$term] = $quality;
			}
		}
	}

	public function insert_terms(StringKey_Array &$terms)
	{
		if (!count($terms))
			return;

		$inserts = array();
		foreach ($terms as $term => $quality)
			$inserts[] = sql_insert_line(array($term, 1));
		$inserts = comma_separate($inserts);

		// insert all new search terms into database
		// and increase count for existing terms
		$sql = "INSERT INTO {$this->termsTable} 
			(term, count) VALUES {$inserts}
			ON DUPLICATE KEY UPDATE count = count + 1";
		$this->db->query($sql);
		unset($inserts);
	}

	public function build_index(StringKey_Array &$terms, $otherValue)
	{	
		if (!count($terms))
			return;

		$otherValue = escape_and_quote($otherValue);
		$termsList = sql_in_list($terms->keys());
		$inserts = array();

		// find the ID value for each term we need
		$sql = "SELECT id, term FROM {$this->termsTable}
			WHERE term IN ({$termsList})";
		$mTerms = Model_Search_Term::from_sql_all($sql);
		
		foreach ($mTerms as $mTerm)
		{
			$inserts[] = sql_insert_line(array(
				$mTerm->id,
				$otherValue,
				$terms[$mTerm->term],
			));
		}

		// record term quality within index
		$inserts = comma_separate($inserts);
		$sql = "INSERT IGNORE INTO {$this->indexTable} 
			(search_term_id, {$this->otherField}, quality)
			VALUES {$inserts} ON DUPLICATE KEY 
			UPDATE quality = VALUES(quality)";
		$this->db->query($sql);
	}

	public function remove($otherValue)
	{
		$sql = "DELETE FROM {$this->indexTable}
			WHERE {$this->otherField} = ?";
		$this->db->query($sql, array($otherValue));
	}

}
