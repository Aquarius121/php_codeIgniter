<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cookie {
	
	protected $name;
	protected $value;
	protected $path;
	protected $domain;
	protected $is_secure = false;
	protected $is_http_only = false;	
	
	public function __construct($name, $path = null, $domain = null)
	{
		$this->name = $name;
		$this->path = $path;
		$this->domain = $domain;
	}
	
	protected function conf_check()
	{
		$ci =& get_instance();
		if ($this->path === null)
			$this->path = $ci->env['session_path'];
		if ($this->domain === null)
			$this->domain = $ci->env['session_domain'];
	}
	
	public function set($value, $expires = false)
	{
		if ($expires === false)
		     $expires = 0;
		else $expires = time() + $expires;
		$this->conf_check();
		
		setcookie($this->name, $value, $expires, 
			$this->path, $this->domain, $this->is_secure,
			$this->is_http_only);
	}
		
	public function get()
	{
		$ci =& get_instance();
		if (isset($ci->env['cookies'][$this->name]))
			return $ci->env['cookies'][$this->name];
		return null;
	}
	
	public function write($value, $expires = false)
	{
		$this->set($value, $expires);
	}
	
	public function read()
	{
		return $this->get();
	}
	
	public function delete()
	{
		$this->write(null, -1);
	}
	
	public function set_is_secure($is_secure)
	{
		$this->is_secure = $is_secure;
	}
	
	public function set_is_http_only($is_http_only)
	{
		$this->is_http_only = $is_http_only;
	}
	
	public function & reference()
	{
		$ci =& get_instance();
		return $ci->env['cookies'];
	}
		
}

?>