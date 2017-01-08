<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Session {
	
	protected $data = array();
	protected $closed = false;

	protected $duration;
	protected $path;
	protected $domain;
	protected $cookie;
	
	public function __construct($options)
	{
		foreach ($options as $k => $v)
			$this->$k = $v;
				
		if (!$this->id())	
			$this->start();
		
		$this->data =& $_SESSION;
		if (!isset($this->data['session_refresh']))
			$this->data['session_refresh'] = time();
		$refresh_time = (int) $this->data['session_refresh'];
		$elapsed_time = time() - $refresh_time;
		
		if ($elapsed_time >= ($this->duration / 2))
		{
			$this->data['session_refresh'] = time();
			session_regenerate_id(true);
		}
	}
	
	public function id()
	{
		return session_id();
	}
	
	public function close()
	{
		$this->commit();
	}
	
	public function commit()
	{
		Data_Cache_LT_Session_Handler::commit();
	}
	
	public function reload()
	{
		Data_Cache_LT_Session_Handler::reload();
	}
	
	public function & reference()
	{
		return $this->data;
	}
	
	public function delete($name)
	{
		unset($this->data[$name]);
	}
	
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	public function get($name)
	{
		if (!isset($this->data[$name])) return null;
		return $this->data[$name];
	}
	
	public function write($name, $value)
	{
		return $this->set($name, $value);
	}
	
	public function read($name)
	{
		return $this->get($name);
	}
	
	public function start()
	{
		session_set_cookie_params($this->duration, 
			$this->path, $this->domain);
		session_name($this->cookie);
		session_start();
	}
	
}
