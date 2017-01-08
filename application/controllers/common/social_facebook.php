<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Facebook\Facebook;
class Social_Facebook_Controller extends CIL_Controller {
	
	protected $facebook_config;
	
	protected static $required_perms = array(
		'user_friends'
	);
	protected $key_prefix = "influencer_";
	public function __construct() {
		parent::__construct();

		$this->facebook_config = $this->conf('social_facebook_app');
		$this->fb_appId = $this->facebook_config['api']['appId'];
		$this->fb_appSecret = $this->facebook_config['api']['secret'];
		$this->facebook = new Facebook(array('app_id' => $this->fb_appId,'app_secret' => $this->fb_appSecret));
		$this->user = Auth::user();
	}

	protected function login_url($params = array())	{
		// remove any existing session
		$helper = $this->facebook->getRedirectLoginHelper();
		
		$base_url = $this->facebook_config['base_url'];		
		$redirect_uri = $base_url."/callback";
		$permissions = static::$required_perms;
		return $helper->getLoginUrl($redirect_uri,$permissions);
	}
	
	public function callback() {
		
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

		if (! isset($accessToken)) {
			if ($helper->getError()) {
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
			} else {
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
			exit;
		}

		$oAuth2Client = $this->facebook->getOAuth2Client();
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		$tokenMetadata->validateAppId($this->fb_appId); 
		$tokenMetadata->validateExpiration();

		if (! $accessToken->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
				exit;
			}
		}

		$tokenValue = $accessToken->getValue();
		$expire = $accessToken->getExpiresAt()->getTimestamp();
		$fb_user = $this->getUserDetails($tokenValue);
		$fb_friend = $this->getFriends($tokenValue);

		$social_key = $this->key_prefix . $this->user->id;

		$this->influencer_save($this->user->id, $social_key, $fb_friend);
		$this->auth_save($social_key, $fb_user['id'], $fb_user['name'], $tokenValue, $expire);
	}

	public function influencer_save($user_id, $social_key, $friend_count){
		$this->db->query(
			"INSERT INTO nr_influencer_facebook (user_id, social_key, fb_friend_count) 
			VALUES ({$user_id}, '{$social_key}', {$friend_count})
			ON DUPLICATE KEY UPDATE fb_friend_count = {$friend_count}");
	}

	public function auth_save($social_key, $fb_user_id, $fb_user_name, $fb_access_token, $fb_token_expire){
		$this->db->query(
			"INSERT INTO nr_social_auth_facebook (id, access_token, fb_user_id, fb_user_name, token_expire) 
			VALUES ('{$social_key}', '{$fb_access_token}', '{$fb_user_id}', '{$fb_user_name}', {$fb_token_expire})
			ON DUPLICATE KEY UPDATE access_token = '{$fb_access_token}', token_expire = {$fb_token_expire},
				fb_user_name = '{$fb_user_name}'");
	}

	public function getUserDetails($token) {

		try {
			// Returns a `Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me', $token);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		$body = $response->getDecodedBody();
		// $fb_user_id = $body['id'];
		// $fb_user_name = $body['name'];

		return array("id" => $body['id'], "name" => $body['name']);
	}

	public function getFriends($token) {
		try {
			// Returns a `Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me/friends',$token);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		
		$body = $response->getDecodedBody();
		return $body["summary"]["total_count"];
	}

	public function renewToken($oldToken) {

		$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$this->fb_appId."&client_secret=".$this->fb_appSecret."&grant_type=fb_exchange_token&fb_exchange_token=".$oldToken;

		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_URL, $token_url);
		$contents = curl_exec($c);
		$err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
		curl_close($c);

		$res = explode("&",$contents);

		$newToken = explode("=", $res[0])[1];
		$expire = time() + intVal(explode("=", $res[1])[1]);

		return array("token" => $newToken, "expire" => $expire);
	}

	public function update() {
		$sql = "SELECT * FROM nr_influencer_facebook, nr_social_auth_facebook where nr_influencer_facebook.social_key = nr_social_auth_facebook.id";
			
		$query = $this->db->query($sql);
		$results = Model_Influencer_Facebook::from_db_all($query);
		foreach ($results as $result)
		{
			if(time() < $result->token_expire){
				$friend_count = $this->getFriends($result->access_token);

				$this->influencer_save($result->user_id, $result->social_key, $friend_count);
				//$this->auth_save($social_key, $result->fb_user_id, $result->fb_user_name, $result->access_token, $result->token_expire);
				
			} else {
				$token = $this->renewToken($result->access_token);
				$friend_count = $this->getFriends($token['token']);
				
				$this->influencer_save($result->user_id, $result->social_key, $friend_count);
				$this->auth_save($result->social_key, $result->fb_user_id, $result->fb_user_name, $token['token'], $token['expire']);
			}
		}
	}
	
	public function index()	{
		$url = $this->login_url();
		$this->redirect($url, false);
	}
	
}