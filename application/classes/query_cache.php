<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Query_Cache {

	protected $db;
	protected $lifetime;
	protected static $__disabled = false;

	protected static $internal_cache = array();
	protected static $internal_cache_list = array();
	protected static $internal_cache_expires = array();

	const INTERNAL_CACHE_SIZE = 32;
	const INTERNAL_CACHE_MAX_LENGTH = 32768;

	public static function disable()
	{
		static::$__disabled = true;
	}

	public function __construct($db, $lifetime = 86400)
	{
		$this->db = $db;
		$this->lifetime = $lifetime;
	}

	public function __call($name, $arguments)
	{
		if (method_exists($this->db, $name))
		{
			$return = call_user_func_array(
				array($this->db, $name), $arguments);
			if ($return === $this->db)
				return $this;
			return $return;
		}

		return;
	}

	public function db()
	{
		return $this->db;
	}

	public static function __hash($qcd)
	{
		$dh = new Data_Hash();
		$dh->database = $qcd->database;
		$dh->params = $qcd->params;
		$dh->sql = $qcd->sql;
		return $dh->hash_hex();
	}

	public function query($sql, $params = array(), $lifetime = -1)
	{
		if ($lifetime === -1)
			$lifetime = $this->lifetime;
	
		$qcd = new Query_Cache_Data();
		$qcd->sql = $sql;
		$qcd->params = $params;
		$qcd->database = $this->db->database;
		$qcd->lifetime = $lifetime;

		$hex = static::__hash($qcd);

		if (static::$__disabled)
		{
			$qcd_string = false;
		}
		else
		{
			if (!($qcd_string = $this->read_internal($hex)))
			{
				$qcd_string = Data_Cache_ST::read($hex);
				// save internally, but at most for 1 second
				// as we do not know the desired lifetime
				$this->write_internal($hex, $qcd_string, 1);
			}
		}
		 
		if ($qcd_string)
		{
			$qcd = unserialize($qcd_string);
			// something went wrong, we have bad data
			if (!($qcd instanceof Query_Cache_Data))
				return $this->db->query($sql, $params);
			$qcr = new Query_Cache_Result($qcd, $this);
			return $qcr;
		}
		else
		{
			$records = array();
			$dbr = $this->db->query($sql, $params);
			foreach ($dbr->result() as $record)
				$records[] = $record;

			$qcd->records = $records;
			$qcd_string = serialize($qcd);
			Data_Cache_ST::write($hex, $qcd_string, $lifetime);
			$this->write_internal($hex, $qcd_string, $lifetime);
			$qcr = new Query_Cache_Result($qcd, $this);
			return $qcr;
		}
	}

	protected function write_internal($hex, $qcd_string, $lifetime)
	{
		if (strlen($qcd_string) > static::INTERNAL_CACHE_MAX_LENGTH) return;
		static::$internal_cache_expires[$hex] = Date::seconds($lifetime);
		static::$internal_cache_list[$hex] = microtime(true);
		static::$internal_cache[$hex] = $qcd_string;
		static::clean_internal();
	}

	protected function read_internal($hex)
	{
		if (isset(static::$internal_cache[$hex]) &&
			 static::$internal_cache_expires[$hex] > Date::utc())
		{
			static::$internal_cache_list[$hex] = microtime(true);
			return static::$internal_cache[$hex];
		}
	}

	protected static function clean_internal()
	{
		foreach (static::$internal_cache_expires as $hex => $expires)
		{
			if (Date::utc() > $expires)
			{
				unset(static::$internal_cache_expires[$hex]);
				unset(static::$internal_cache_list[$hex]);
				unset(static::$internal_cache[$hex]);
			}
		}

		// remove from cache using LRU pattern
		if (count(static::$internal_cache_list) > static::INTERNAL_CACHE_SIZE)
		{
			$lowest_time = PHP_INT_MAX;
			$lowest_hex = false;

			foreach (static::$internal_cache_list as $hex => $time)
			{
				if ($time < $lowest_time)
				{
					$lowest_time = $time;
					$lowest_hex = $hex;
				}
			}

			unset(static::$internal_cache_expires[$hex]);
			unset(static::$internal_cache_list[$hex]);
			unset(static::$internal_cache[$hex]);
		}
	}

	public function get()
	{
		return $this->query($this->db->get_sql());
	}

}