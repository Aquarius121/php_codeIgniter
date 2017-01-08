<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart_Item_Hash extends Cart_Item {
	
	protected $token;
	protected $hash;
	
	public function __construct($hash)
	{
		parent::__construct();
		$this->token = md5(UUID::create());
		$this->hash = $hash;
	}
	
	public function hash()
	{
		return $this->hash;
	}

	public function token()
	{
		return $this->token;
	}

}

?>