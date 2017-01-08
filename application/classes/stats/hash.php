<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_Hash extends Data_Hash {

	protected static $db;

	protected static function stat_db()
	{
		if (static::$db) return static::$db;
		return static::$db = get_instance()->load_db('stat');
	}

	public function hash($_ = null)
	{
		return parent::hash('sha1');
	}

	public function context()
	{
		return static::__context($this->hash());
	}

	public function context_encoded()
	{
		return Stats_Engine::context_encode(static::__context($this->hash()));
	}

	public static function __cache($context, $hash)
	{
		Stats_Hash_Cache::write($context, $hash);
	}

	public static function __context_batch($hashes, $values = false)
	{
		// check the cache first
		$contexts = array();
		foreach ($hashes as $hash)
			$contexts[$hash] = static::__context_from_cache($hash);

		// batch load from db
		$miss_hashes = array();
		foreach ($hashes as $hash)
			if ($contexts[$hash] == false) 
				$miss_hashes[] = $hash;
		$miss_contexts = static::__context_from_db_batch($miss_hashes);
		$contexts = array_replace($contexts, $miss_contexts);
		foreach ($miss_contexts as $hash => $context)
			if ($context) static::__cache($context, $hash);

		// create any missing hashes
		foreach ($contexts as $hash => &$context)
		{
			if ($context !== false) continue;
			// create a new context
			$context = static::__new_context($hash);
			static::__cache($context, $hash);
		}

		if ($values)
		     return array_values($contexts);
		else return $contexts;
	}
	
	public static function __context($hash)
	{
		// check the cache first
		$context = static::__context_from_cache($hash);
		if ($context) return $context;

		// load an existing context from the database
		if ($context = static::__context_from_db($hash)) 
		{
			static::__cache($context, $hash);
			return $context;
		}

		// create a new context
		$context = static::__new_context($hash);
		static::__cache($context, $hash);
		return $context;
	}

	public static function __hash_batch($contexts, $values = false)
	{
		// check the cache first
		$hashes = array();
		foreach ($contexts as $context)
			$hashes[$context] = static::__hash_from_cache($context);

		// batch load from db
		$miss_contexts = array();
		foreach ($contexts as $context)
			if ($hashes[$context] === false) 
				$miss_contexts[] = $context;
		$miss_hashes = static::__hash_from_db_batch($miss_contexts);
		$hashes = array_replace($hashes, $miss_hashes);
		foreach ($miss_hashes as $context => $hash)
			if ($hash) static::__cache($context, $hash);

		if ($values)
		     return array_values($hashes);
		else return $hashes;
	}

	// *** does not have the same behaviour 
	// as the Data_Hash::__hash function
	public static function __hash($context, $_ = null)
	{
		// ensure correct type
		$context = (int) $context;

		// check the cache first
		$hash = static::__hash_from_cache($context);
		if ($hash) return $hash;

		// load an existing hash from the database
		if ($hash = static::__hash_from_db($context)) 
		{
			static::__cache($context, $hash);
			return $hash;
		}

		return false;
	}

	protected static function __context_from_cache($hash)
	{
		$context = Stats_Hash_Cache::context($hash);
		if ($context) return $context;
		return false;
	}

	protected static function __context_from_db($hash)
	{
		// load an existing context from the database
		$sql = "select context from hash_context where hash = ?";
		$result = static::stat_db()->query($sql, array($hash))->row();
		if (isset($result->context)) return (int) $result->context;
		return false;
	}

	protected static function __context_from_db_batch($hashes)
	{
		if (!count($hashes))
			return array();
		$contexts = array();
		foreach ($hashes as $hash)
			$contexts[$hash] = false;

		// load existing contexts from the database
		$hashes_in_list = sql_in_list($hashes);
		$sql = "select hash, context from hash_context 
			where hash in (${hashes_in_list})";
		$dbr = static::stat_db()->query($sql);
		foreach ($dbr->result() as $result)
			$contexts[$result->hash] = (int) $result->context;
		return $contexts;
	}

	protected static function __hash_from_cache($context)
	{
		$hash = Stats_Hash_Cache::hash($context);
		if ($hash) return $hash;
		return false;
	}

	protected static function __hash_from_db($context)
	{
		// load an existing context from the database
		$sql = "select hash from context_hash where context = ?";
		$result = static::stat_db()->query($sql, array($context))->row();
		if (isset($result->hash)) return $result->hash;
		return false;
	}

	protected static function __hash_from_db_batch($contexts)
	{
		if (!count($contexts))
			return array();
		$hashes = array();
		foreach ($contexts as $context)
			$hashes[$context] = false;

		// load existing hashes from the database
		$hashes_in_list = sql_in_list($contexts);
		$sql = "select hash, context from hash_context 
			where context in (${hashes_in_list})";
		$dbr = static::stat_db()->query($sql);
		foreach ($dbr->result() as $result)
			$hashes[(int) $result->context] = $result->hash;
		return $hashes;
	}

	protected static function __new_context($hash)
	{
		// create in the context_hash table 
		$sql = "insert into context_hash (hash) values (?)";
		static::stat_db()->query($sql, array($hash));
		$context = (int) static::stat_db()->insert_id();

		// create in the hash_context table 
		$sql = "insert ignore into hash_context values (?, ?)";
		static::stat_db()->query($sql, array($hash, $context));

		return $context;
	}

}

?>