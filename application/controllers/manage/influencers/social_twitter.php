<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Social_Twitter_Controller extends CIL_Controller {
	
	protected $twitter_config;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->twitter_config = $this->conf('social_twitter_app');
		$api_key = $this->twitter_config['api']['key'];
		$api_secret = $this->twitter_config['api']['secret'];
		$this->twitter = new Twitter($api_key, $api_secret);
	}
	
	protected function failed()
	{
		$view_data = array('url' => $this->login_url());
		$this->load->view('common/twitter_denied', $view_data);
		return;
	}
	
	protected function login_url()
	{
		$newsroom_name = $this->newsroom->name;
		$base_url = $this->twitter_config['base_url'];
		$redirect_uri = "{$base_url}/callback";
		echo $redirect_uri;
		//exit;
		//$this->twitter->setAccessToken(null);
		//-C206AAAAAAAx0J1AAABWFS6PfA
		//DDMLTc4cVnIqg3ixUa3lEoj7L2yxrcFY
		$t_token = $this->twitter->getRequestToken($redirect_uri);
		$this->session->set('twitter_app_t_token', $t_token);
		return $this->twitter->getAuthorizeURL($t_token, false);
	}
	
	public function callback()
	{
		$oauth_verifier = $this->input->get('oauth_verifier');
		$t_token = $this->session->get('twitter_app_t_token');
		
		if (empty($oauth_verifier))
			return $this->failed();
		
		if (empty($t_token['oauth_token_secret']))
			return $this->failed();
		
		$this->session->delete('twitter_app_t_token');
		$this->twitter->setAccessToken($t_token);

		
		
		if (!($cred_token = $this->twitter->getAccessToken($oauth_verifier)))
			return $this->failed();
		var_dump($this->twitter->get('https://api.twitter.com/1.1/users/show.json?screen_name='.$cred_token['screen_name']));		
		// $this->db->query(
		// 	"INSERT INTO nr_social_auth_twitter (company_id, 
		// 	 oauth_token, oauth_token_secret, username, date_renewed) 
		// 	 VALUES (?, ?, ?, ?, UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE 
		// 	 oauth_token = ?, oauth_token_secret = ?, 
		// 	 username = ?, date_renewed = UTC_TIMESTAMP()",
		// 	array($this->newsroom->company_id, 
		// 	      $cred_token['oauth_token'],
		// 	      $cred_token['oauth_token_secret'],
		// 	      $cred_token['screen_name'],
		// 	      $cred_token['oauth_token'],
		// 	      $cred_token['oauth_token_secret'],
		// 	      $cred_token['screen_name']));
		
		// $relative_url = 'manage/newsroom/social/auth_complete';
		// $url = $this->newsroom->url($relative_url);
		// $this->redirect($url, false);
	}
	
	public function index()
	{	
		$url = $this->login_url();
		$this->redirect($url, false);
	}
	
}

?>