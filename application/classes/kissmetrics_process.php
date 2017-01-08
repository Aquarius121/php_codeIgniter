<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class KissMetrics_Process {
	
	protected $has_identified = false;
	protected $has_loaded = false;
	
	// whether to create an alias 
	// for the identities found
	protected $create_alias = true;
	
	public $config;
	public $user;
	
	public function __construct($user = NR_DEFAULT)
	{
		if ($user === NR_DEFAULT)
		     $this->user = Auth::user();
		else $this->user = $user;
		
		// only create an alias for non-admin user
		// because admin mode actions should not 
		// trigger an alias to the standard user
		$this->create_alias = !Auth::is_admin_mode();
	}
	
	protected function load()
	{
		if (!$this->has_loaded)
		{
			$ci =& get_instance();
			$this->config = $ci->conf('kissmetrics');
			KM::init($this->config['api_key']);
			$this->has_loaded = true;
		}
	}
	
	public function identify($identity = NR_DEFAULT)
	{
		if (!$this->has_identified)
		{
			$this->load();
			if ($identity === NR_DEFAULT)
				$identity = $this->identity();
			if ($identity->email)
			     KM::identify($identity->email);
			else KM::identify($identity->anon);
			if ($identity->create_alias)
				$this->alias($identity);
			$this->has_identified = true;
		}
	}
	
	public function alias($identity)
	{
		$identity_hash = md5(serialize($identity));
		if (Data_Cache_LT::read($identity_hash)) return;
		Data_Cache_LT::write($identity_hash, 1);
		KM::alias($identity->anon, $identity->email);
	}
	
	public function record($name, $data = array())
	{
		$this->load();
		$this->identify();
		
		if (is_object($data)) $data = get_object_vars($data);
		if (!is_array($data)) $data = array();
		KM::record($name, $data);
	}
	
	public function set($data = array())
	{
		$this->load();
		$this->identify();
		
		if (is_object($data)) $data = get_object_vars($data);
		if (!is_array($data)) $data = array();
		KM::set($name, $data);
	}
	
	public function identity()
	{
		$identity = new stdClass();
		$identity->email = null;
		$user = $this->user;
		if ($user && !empty($user->email))
			$identity->email = $user->email;
		$identity->anon = $this->anon_identity();
		$identity->create_alias = $this->create_alias && 
			$identity->anon && $identity->email;
		return $identity;
	}
	
	protected function anon_identity()
	{
		$ci =& get_instance();
		if ($ci->input->is_cli_request()) return null;
		$cookie = new Cookie('kiss_anon_id');
		if ($identity = $cookie->get())
			return $identity;
		$identity = UUID::create();
		$cookie->set($identity, 31536000);
		return $identity;
	}
	
	public static function __identity()
	{
		$process = new static();
		$identity = $process->identity();
		if ($identity->email) return $identity->email;
		if ($identity->anon) return $identity->anon;
		return null;
	}
	
	protected function event($name, $data = array())
	{
		$event = new Scheduled_Iella_Event();
		$event->data->data = $data;
		$event->data->name = $name;
		$event->data->identity = $this->identity();
		$event->schedule('km_record');
	}
	
}