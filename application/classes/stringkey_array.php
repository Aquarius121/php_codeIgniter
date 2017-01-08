<?php 

// provides the same functionality as an array() but 
// ensures that the keys always remain strings 
// even when a numeric value is used

class StringKey_Array implements ArrayAccess, Countable, IteratorAggregate {

	protected $__data;
	protected $__count;

	public function __construct()
	{
		$this->__data = new stdClass();
		$this->__count = 0;
	}

	public function offsetSet($offset, $value)
	{
		$offset = (string) $offset;
		if (!isset($this->__data->{$offset}))
			$this->__count++;
		$this->__data->{$offset} = $value;
	}

	public function offsetExists($offset)
	{
		$offset = (string) $offset;
		return isset($this->__data->{$offset});
	}

	public function offsetUnset($offset)
	{
		$offset = (string) $offset;	
			
		if (isset($this->__data->{$offset}))
		{
			unset($this->__data->{$offset});
			$this->__count--;
		}
	}

	public function & offsetGet($offset)
	{
		$offset = (string) $offset;
		if (isset($this->__data->{$offset}))
			return $this->__data->{$offset};
		throw new OutOfBoundsException();
	}

	public function getIterator()
	{
		return new ArrayIterator($this->__data);
	}

	public function count()
	{
		return $this->__count;
	}

	public function keys()
	{
		$list = array();
		foreach ($this->__data as $k => $v)
			$list[] = $k;
		return $list;
	}

	public function values()
	{
		$list = array();
		foreach ($this->__data as $k => $v)
			$list[] = $v;
		return $list;
	}

	public function to_array()
	{
		return get_object_vars($this->__data);
	}

}
