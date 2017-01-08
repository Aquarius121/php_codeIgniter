<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model extends Model_Base implements Serializable {
	
	use Model_Prepare_Trait;

	protected static $__auto_fields = array();
	protected static $__cache_enabled_classes = array();
	protected static $__cache_enabled = false;
	protected static $__cache_duration = 60;
	protected static $__db_class = 'default';
	protected static $__fields = null;
	protected static $__primary = 'id';
	protected static $__table = null;
	protected static $__allow_zero_id = false;

	// properties that appear in here are
	// compressed in the database and should
	// be decompressed upon load and
	// compressed again on save
	protected static $__compressed = array();
	
	const CMP_EQUAL                    = '=';
	const CMP_LESS_THAN                = '<';
	const CMP_GREATER_THAN             = '>';
	const CMP_GREATER_THAN_OR_EQUAL    = '>=';
	const CMP_LESS_THAN_OR_EQUAL       = '<=';
	const CMP_NOT_EQUAL                = '!=';
	const CMP_STRING_LIKE              = 'LIKE';
	const CMP_STRING_REGEX             = 'REGEXP';
	const ORDER_DESC                   = 'DESC';
	const ORDER_ASC                    = 'ASC';

	// minimum length for compression
	const COMPRESS_MIN_LENGTH = 128;
	
	public $id;
		
	public function __construct()
	{
		if (static::$__primary !== null && 
		    !is_array(static::$__primary))
			$this->{static::$__primary} = null;
	}
	
	public function __get($name)
	{
		if ($name === 'db')
			return $this->db();
		return parent::__get($name);
	}
	
	public function db($cached = false)
	{
		return static::__db($cached);
	}

	public static function __db($cached = false)
	{
		static $instance;
		if ($instance === null)
			$instance = get_instance()->load_db(static::$__db_class);
		if ($cached && static::__cache_enabled())
			return $instance->cached(static::$__cache_duration);
		return $instance;
	}
	
	public static function enable_cache()
	{
		$class = strtolower(get_called_class());
		static::$__cache_enabled_classes[$class] = true;
	}
	
	public static function disable_cache()
	{
		$class = strtolower(get_called_class());
		static::$__cache_enabled_classes[$class] = false;
		static::$__cache_enabled = false;
	}
	
	public function values($values = null)
	{
		if ($values === null)
		{
			// return this as object
			$values = new stdClass();
			foreach (static::__fields() as $field)
				$values->{$field} = $this->{$field};
			return $values;
		}
		else
		{
			// store values into this
			foreach ($values as $k => $v)
				if ($k !== static::$__primary) 
					$this->{$k} = $v;
		}
	}

	public function overlay($values)
	{
		if (!$values) return;
		
		$fields = $this->fields();

		// store values into this
		// but don't include fields
		// that this object already has
		foreach ($values as $k => $v)
		{
			if (in_array($k, $fields)) continue;
			if ($k !== static::$__primary) 
				$this->{$k} = $v;
		}
	}
	
	public function serialize($__source = false)
	{
		$data = $this->values();
		if ($__source) $data->__source = $this->__source;
		return serialize($data);
	}

	public function unserialize($str)
	{
		$values = unserialize($str);
		foreach ($values as $k => $v)
			$this->{$k} = $v;
	}

	public static function __unserialize($str)
	{
		$instance = new static();
		$instance->unserialize($str);
		return $instance;
	}

	public function is_new()
	{
		return $this->__is_new();
	}
	
	protected function __is_new()
	{
		return !$this->__source;
	}
	
	public function fields()
	{
		return static::__fields();
	}

	public static function __new_save()
	{
		$instance = new static();
		$instance->save();
		return $instance;
	}

	public static function __table()
	{
		return static::$__table;
	}

	public static function __fields()
	{
		if (static::$__fields) 
			return static::$__fields;
		
		$table = static::$__table;
		if (isset(static::$__auto_fields[$table])) 
			return static::$__auto_fields[$table];
		
		$sql = "SHOW COLUMNS FROM {$table}";
		$query = static::__db()->query($sql);
		static::$__auto_fields[$table] = array();
		foreach ($query->result() as $field)
			static::$__auto_fields[$table][] = $field->Field;
		
		return static::$__auto_fields[$table];
	}

	public static function __prefixes($table = null, $prefix = null, $fields = array())
	{
		if ($table === null) $table = static::$__table;
		if ($prefix === null) $prefix = preg_replace('#^model_#i', 
			null, strtolower(get_called_class()));
		if ($prefix === false) $prefix = $table;
		$field_sql = array();
		foreach (static::__fields() as $field)
			if (in_array($field, $fields) || !count($fields))
				$field_sql[] = sprintf('%s.%s as $$%s$%s', 
					$table, $field, $prefix, $field);
		return comma_separate($field_sql);
	}
	
	public function delete()
	{
		if (!static::$__primary && !is_array(static::$__primary))
			throw new Exception();
		
		if (is_array(static::$__primary))
		{
			foreach (static::$__primary as $k) 
				if ($this->$k === null) return;
			$condition = array();
			foreach (static::$__primary as $k) 
				$condition[$k] = $this->__source->$k;
			$this->db()->delete(static::$__table, $condition);
		}
		else
		{
			if ($this->{static::$__primary} === null) return;
			$this->db()->delete(static::$__table, 
				array(static::$__primary => 
				$this->{static::$__primary}));
		}
	}

	protected static function load_data_transform(&$load_data)
	{
		parent::load_data_transform($load_data);

		// decompress any compressed data
		foreach ($load_data as $k => &$v)
			if (in_array($k, static::$__compressed))
				if (GZIP::has_header($v))
					$v = GZIP::decode($v);
	}

	protected function save_data_transform(&$save_data)
	{
		foreach ($save_data as $k => &$v)
		{
			// all objects to string
			if (is_object($v))
				$v = $v->__toString();

			// compress any fields that need it
			// * skip any field that is quite short
			// * skip any field with no value
			if (in_array($k, static::$__compressed) 
				&& $v && strlen($v) >= static::COMPRESS_MIN_LENGTH)
				$v = GZIP::encode($v);
		}
	}
	
	public function save()
	{
		if (!static::$__primary && 
		    !is_array(static::$__primary) &&
		     $this->__source)
			throw new Exception();
		
		$save_data = array();
		
		if ($this->__source)
		{
			foreach ($this->__fields() as $k) 
				if (@$this->__source->$k != $this->$k)
					$save_data[$k] = $this->$k;
			
			if (is_array(static::$__primary))
			{
				$condition = array();
				foreach (static::$__primary as $k) 
					$condition[$k] = $this->__source->$k;
			}
			else
			{
				$condition = array(static::$__primary => 
					$this->{static::$__primary});
			}
			
			$this->save_data_transform($save_data);
			if (!count($save_data)) return;
			$this->db()->update(static::$__table, 
				$save_data, $condition);
		}
		else
		{
			if (static::$__primary && !is_array(static::$__primary))
				$save_data[static::$__primary] = null;
			foreach ($this->__fields() as $k)
				if (isset($this->$k)) 
					$save_data[$k] = $this->$k;
				
			$this->save_data_transform($save_data);
			if (!count($save_data)) return;
			$this->__source = new stdClass();
			$this->db()->insert(static::$__table, $save_data);
			if (!static::$__primary) return;
			if (!is_array(static::$__primary) && !$this->{static::$__primary})
				$this->{static::$__primary} = $this->db()->insert_id();
		}
		
		foreach ($this->__fields() as $k) 
			$this->__source->$k = $this->$k;
	}
	
	public function diff()
	{
		if (!static::$__primary && 
		    !is_array(static::$__primary))
			throw new Exception();
		
		$diff = new stdClass();
		$diff->original = array();
		$diff->modified = array();
		
		if ($this->__source)
		{
			foreach ($this->__fields() as $k) 
			{
				if (@$this->__source->$k != $this->$k)
				{
					$diff->original[$k] = $this->__source->$k;
					$diff->modified[$k] = $this->$k;
				}
			}
		}
		else
		{
			foreach ($this->__fields() as $k)
			{
				if (isset($this->$k)) 
				{
					$diff->original[$k] = null;
					$diff->modified[$k] = $this->$k;
				}
			}
		}
		
		return $diff;
	}
	
	public function has_modified_value($field)
	{
		if (!$this->__source) return true;
		if (!isset($this->{$field}) && isset($this->__source->{$field})) return true;
		if (isset($this->{$field}) && !isset($this->__source->{$field})) return true;
		if ($this->{$field} != $this->__source->{$field}) return true;
		return false;
	}
	
	public function reload()
	{
		if (!static::$__primary)
			throw new Exception();
		
		if (is_array(static::$__primary))
		{
			$criteria = array();
			foreach (static::$__primary as $k)
				$criteria[] = array($k, $this->$k);
			$loaded = static::find($criteria);
		}
		else
		{
			$loaded = static::find_id($this->{static::$__primary});
		}
		
		if (!$this->__source) 
			$this->__source = new stdClass();
		foreach ($loaded->__source as $k => $v)
			$this->__source->$k = $this->$k = $v;
	}
	
	public static function find_id($id)
	{
		if (!$id && !static::$__allow_zero_id) return false;
		if (!static::$__primary && !is_array(static::$__primary))
			return false;
		
		$dbi = static::__db(true)->select('*')
			->from(static::$__table);
		
		if (is_array(static::$__primary))
		{
			foreach (static::$__primary as $i => $k)
				$dbi = $dbi->where($k, $id[$i]);
		}
		else
		{
			$dbi = $dbi->where(static::$__primary, $id);
		}
			
		$result = $dbi->get();
		$object = static::from_db($result);
		$result->free_result();

		return $object;
	}
	
	protected static function __cache_enabled()
	{
		if (static::$__cache_enabled) return true;
		$class = strtolower(get_called_class());
		if (isset(static::$__cache_enabled_classes[$class]) && 
				static::$__cache_enabled_classes[$class])
			return true;
		return false;
	}

	public static function find($name, $value = null)
	{
		// find for criteria
		if (is_array($name))
		{
			$found = static::find_all($name, null, 1);
			if (!$found) return false;
			return $found[0];
		}
		
		// find for id
		if ($value === null)
			return static::find_id($name);
					
		$dbi = static::__db(true)->select('*')
			->from(static::$__table)
			->where($name, $value)
			->limit(1);
			
		$result = $dbi->get();
		$ob = static::from_db($result);
		$result->free_result();
		return $ob;
	}

	public static function find_all_in_list($list)
	{
		if (is_array(static::$__primary))
			throw new Exception();

		if (!count($list))
			return array();

		$table = static::$__table;
		$primary = static::$__primary;
		$list = sql_in_list($list);
		$sql = "SELECT * FROM {$table} WHERE {$primary} IN ({$list})";
		return static::from_sql_all($sql);
	}
	
	// criteria should be an array of the form:
	// [[field,op,value],[field,value]]
	// order should be an array of the form:
	// [field,asc|desc]
	public static function find_all($criteria = array(), $order = null, $limit = false, $count = false)
	{
		$dbi = static::__db(true)
			->select(value_if_test($count, '1', '*'))
			->from(static::$__table);
			
		if ($criteria === null)
			$criteria = array();
		
		// just one criteria (so not wrapped in array)
		if (isset($criteria[0]) && !is_array($criteria[0]))
			$criteria = array($criteria);
		
		foreach ($criteria as $criterion)
		{
			if (count($criterion) === 0) continue;
			if (count($criterion) === 1)
			{
				$left = $criterion[0];
				$right = null;
			}
			else if (count($criterion) === 2)
			{
				$left = $criterion[0];
				$right = $criterion[1];
			}
			else
			{
				$left = "{$criterion[0]} {$criterion[1]}";
				$right = $criterion[2];				
			}
			
			// left, right, escape column names
			$dbi->where($left, $right, $right !== null);
		}
		
		// count results instead of fetch
		if ($count) return $dbi->count_all_results();
		
		if ($order !== null)
		{
			if (is_array($order))
			{
				if (is_array($order[0]))
				{
					foreach ($order as $order_f)
						// order by the given field
						$dbi->order_by($order_f[0], $order_f[1]);
				}
				else
				{
					// order by the given field
					$dbi->order_by($order[0], $order[1]);
				}				
			}				
			else 
			{
				// order by the given field
				$dbi->order_by($order, 'asc');
			}
		}			
		
		if ($limit !== false)
			// limit no results
			$dbi->limit($limit);
		
		$result = $dbi->get();	
		$obs = static::from_db_all($result);
		$result->free_result();
		return $obs;
	}
	
	// criteria should be an array of the form:
	// [[field,op,value],[field,value]]
	public static function count($criteria = array())
	{
		return static::find_all($criteria, null, false, true);
	}
	
	public static function count_all($criteria = array())
	{
		return static::count($criteria);
	}

	// short hand for running an sql query then loading model
	public static function from_sql($sql, $params = array(), $prefixes = array())
	{
		if (!is_array($params))
		{
			$params = func_get_args();
			$params = array_slice($params, 1);
		}

		$sql = static::prepare($sql);
		$dbr = static::__db(true)->query($sql, $params);
		return static::from_db($dbr, $prefixes);
	}

	// short hand for running an sql query then loading model (all)
	public static function from_sql_all($sql, $params = array(), $prefixes = array())
	{
		if (!is_array($params))
		{
			$params = func_get_args();
			$params = array_slice($params, 1);
		}

		$sql = static::prepare($sql);
		$dbr = static::__db(true)->query($sql, $params);
		return static::from_db_all($dbr, $prefixes);
	}

}
