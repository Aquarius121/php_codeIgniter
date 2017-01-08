<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Raw_Data_Trait {

	protected $__raw_data_cache_object = array();
	protected $__raw_data_cache_hash = array();

	public function raw_data($data = NR_DEFAULT, $prop = 'raw_data')
	{
		if ($data === NR_DEFAULT)
		{
			if (isset($this->{$prop}))
			{
				if (isset($this->__raw_data_cache_hash[$prop]) &&
					$this->__raw_data_cache_hash[$prop] === md5($this->{$prop}))
					return $this->__raw_data_cache_object[$prop];
				$decoded_raw_data = $this->raw_data_decode($this->{$prop});
				$this->__raw_data_cache_object[$prop] = $this->to_raw_data_object($decoded_raw_data);
				$this->__raw_data_cache_hash[$prop] = md5($this->{$prop});
				return $this->__raw_data_cache_object[$prop];
			}

			return null;
		}
			
		if ($data === null)
		     $this->{$prop} = null;
		else $this->{$prop} = $this->raw_data_encode($data);
	}

	public function raw_data_object($prop = 'raw_data')
	{
		$raw_data = $this->raw_data(NR_DEFAULT, $prop);
		if (!($raw_data instanceof Raw_Data))
			return new Raw_Data();
		return $raw_data;
	}

	public function raw_data_read($prop = 'raw_data')
	{
		return $this->raw_data(NR_DEFAULT, $prop);
	}

	public function raw_data_write($prop = 'raw_data', $data = null)
	{
		if (!is_string($prop)) throw new Exception();
		return $this->raw_data($data, $prop);
	}

	protected function raw_data_encode($object)
	{
		return Raw_Data::encode($object);
	}

	protected function raw_data_decode($data)
	{
		return Raw_Data::decode($data);
	}

	protected function to_raw_data_object($std)
	{
		if (!is_object($std) && 
			 !is_array($std))
			return $std;

		$raw_data = new Raw_Data();
		foreach ($std as $k => $v)
			$raw_data->$k = $v;
		return $raw_data;
	}

}