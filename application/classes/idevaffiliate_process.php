<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class IDevAffiliate_Process {
	
	protected $config;
	
	public function __construct()
	{
		$ci =& get_instance();
		$this->config = $ci->conf('idevaffiliate');
	}
	
	public function sale($transaction, $user)
	{
		$url = "{$this->config['base_url']}/sale.php";
		$request = new HTTP_Request($url);
		$request->data['profile'] = $this->config['profile'];
		$request->data['idev_saleamt'] = $transaction->price;
		$request->data['idev_ordernum'] = $transaction->id;
		$request->data['ip_address'] = $user->remote_addr;
		return $request->get();
	}
	
}

?>