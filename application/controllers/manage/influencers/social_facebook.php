<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'vendor/facebook/graph-sdk/src/Facebook/autoload.php';
class Social_Facebook_Controller extends CIL_Controller {
	
	protected $facebook_config;
	
	protected static $required_perms = array(
		'publish_actions',
		'manage_pages',		
		'publish_pages',
	);
	
	public function __construct()
	{
		parent::__construct();

		$this->facebook_config = $this->conf('social_facebook_app');
		$this->facebook = new Facebook\Facebook(array('app_id' => $this->facebook_config['api']['appId'],'app_secret' => $this->facebook_config['api']['secret']));
	}

	protected function login_url($params = array())
	{
		// remove any existing session
		$helper = $this->facebook->getRedirectLoginHelper();
		
		$base_url = $this->facebook_config['base_url'];		
		$redirect_uri = "{$base_url}/index.php/callback";
		//$params['scope'] = static::$required_perms;
		$params['redirect_uri'] = $redirect_uri;
		return $helper->getLoginUrl($redirect_uri,[]);
	}
	
	public function callback()
	{
		
		$helper = $this->facebook->getRedirectLoginHelper();
		try {
		  $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		if (isset($accessToken)) {
		  // Logged in!
		  $_SESSION['facebook_access_token'] = (string) $accessToken;

		  // Now you can redirect to another page and use the
		  // access token from $_SESSION['facebook_access_token']
		}
		$access_token = $this->facebook->getAccessToken();
		var_dump($access_token->getValue());
		// $this->facebook->destroySession();
		
		// $this->db->query(
		// 	"INSERT INTO nr_social_auth_facebook (company_id, 
		// 	 access_token, page, date_renewed) VALUES (?, ?, NULL, UTC_TIMESTAMP())
		// 	 ON DUPLICATE KEY UPDATE access_token = ?, 
		// 	 page = NULL, date_renewed = UTC_TIMESTAMP()",
		// 	array($this->newsroom->company_id, 
		// 	      $access_token,
		// 	      $access_token));
		
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