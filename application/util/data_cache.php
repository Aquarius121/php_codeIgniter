<?php

class Data_Cache {

	protected $connection_pool;
	protected $connection_pool_size;
	protected $servers;

	public function __construct($servers)
	{
		$this->servers = $servers;
		$this->connection_pool = array();
		$this->connection_pool_size = count($servers);
		foreach ($servers as $server)
			$this->connection_pool[] = null;
	}

	protected function server_index($key) 
	{
		$dec_hash = base_convert(substr(md5($key), 0, 8), 16, 10);
		$server_index = $dec_hash % $this->connection_pool_size;
		return $server_index;
	}

	protected function connection($key)
	{
		$server_index = $this->server_index($key);
		if ($this->connection_pool[$server_index])
			return $this->connection_pool[$server_index];

		$server = $this->servers[$server_index];
		$connection = new Memcache();
		if ($server[2])
		     $connection->pconnect($server[0], $server[1]);
		else $connection->connect($server[0], $server[1]);
		$this->connection_pool[$server_index] = $connection;
		return $connection;
	}
	
	public function delete($name)
	{
		$connection = $this->connection($name);
		$connection->delete($name);
	}

	public function read($name)
	{
		$connection = $this->connection($name);
		$response = $connection->get($name);
		return $response;
	}	
	
	public function read_object($name)
	{
		$value = $this->read($name);
		if (!$value) return null;
		return json_decode($value);
	}	
	
	public function write($name, $value, $flag = MEMCACHE_COMPRESSED, $expire = 86400)
	{
		$connection = $this->connection($name);
		$response = $connection->set($name, $value, $flag, $expire);
		return $response;
	}
	
	public function write_object($name, $object, $flag = MEMCACHE_COMPRESSED, $expire = 86400)
	{
		return $this->write($name, json_encode($object), $flag, $expire);
	}
	
}

?>