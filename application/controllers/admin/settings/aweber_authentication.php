<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Aweber_Authentication_Controller extends Admin_Base {
	
	public function index()
	{
		$aweber = AWeber_Client_Factory::create();
		
		if ($this->input->get('oauth_token') && $this->input->get('oauth_verifier'))
		{
			$aweber->user->requestToken = $this->input->get('oauth_token');
			$aweber->user->verifier = $this->input->get('oauth_verifier');
			$aweber->user->tokenSecret = $this->session->get('aweber_authentication_secret');
			list($access_key, $access_secret) = $aweber->getAccessToken();
			var_dump(array('key' => $access_key, 'secret' => $access_secret));
			die();
		}
		
		$url = $this->common()->url($this->uri->uri_string);
		list($request_key, $request_secret) = $aweber->getRequestToken($url);
		$this->session->set('aweber_authentication_secret', $request_secret);
		$this->redirect($aweber->getAuthorizeUrl(), false);
	}
	
}

?>