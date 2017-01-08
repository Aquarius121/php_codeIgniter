<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Content_Hash extends Model {
	
	protected static $__table = 'nr_content_hash';
	protected static $__primary = array('hash', 'content_id');

	public static function delete_for_content($content)
	{
		$table = static::$__table;
		if ($content instanceof Model_Content)
			$content = $content->id;

		$sql = "DELETE FROM {$table} WHERE content_id = ?";
		static::__db()->query($sql, array($content));
	}

	public static function insert_for_content($content, $data)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;

		// remove any non-alphanumeric to help with matching
		$data = preg_replace('#[^a-z0-9]#i', null, $data);
		$data = trim(strtolower($data));
		if (!$data) return;

		$data_hash = new Data_Hash();
		$data_hash->data = $data;
		$hash = $data_hash->hash();

		$instance = new static();
		$instance->hash = $hash;
		$instance->content_id = $content;
		$instance->save();
	}

	public static function find_duplicates_for_content($content, $limit = 10)
	{
		$table = static::$__table; 
		if ($content instanceof Model_Content)
			$content = $content->id;
		$content = (int) $content;
		$limit = (int) $limit;

		$hashes = array();
		$content_hashes = static::find_all(array('content_id', $content));
		if (!count($content_hashes)) return array();
		foreach ($content_hashes as $content_hash)
			$hashes[] = $content_hash->hash;
		$hashes_in_list = sql_in_list($hashes);

		$sql = "SELECT c.* FROM {$table} ch
			INNER JOIN nr_content c
			ON ch.content_id = c.id AND (
				c.is_under_review = 1 OR
				c.is_published = 1)
			AND ch.hash IN ({$hashes_in_list})
			AND c.id != {$content}
			GROUP BY c.id
			LIMIT {$limit}";

		$dbr = static::__db()->query($sql);
		return Model_Content::from_db_all($dbr);
	}
	
}

?>