<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_Controller extends CIL_Controller {
	
	protected $twitter_config;
	protected $key_prefix = "influencer_";
	
	public function __construct() {
		parent::__construct();
		
		$this->twitter_config = $this->conf('social_twitter_app');
		$api_key = $this->twitter_config['api']['key'];
		$api_secret = $this->twitter_config['api']['secret'];
		$this->twitter = new Twitter($api_key, $api_secret);
		$this->user = Auth::user();
	}
	
	protected function failed() {
		echo 'Twitter SDK returned an Error';
		exit;
	}
	
	protected function login_url() {
		$newsroom_name = $this->newsroom->name;
		$base_url = $this->twitter_config['base_url'];
		$redirect_uri = "{$base_url}/callback";
		$t_token = $this->twitter->getRequestToken($redirect_uri);
		$this->session->set('twitter_app_t_token', $t_token);
		return $this->twitter->getAuthorizeURL($t_token, false);
	}
	
	public function callback() {
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
		try {
			$userDetail = $this->getUserDetails($cred_token['screen_name']);
		} catch(Exception $e) {
			echo 'Error: ' . $e->getMessage();
			exit;
		}
		$social_key = $this->key_prefix . $this->user->id;

		$this->influencer_save($this->user->id, $social_key, $userDetail['fr_count'], $userDetail['fo_count']);
		$this->auth_save($social_key, $userDetail['id'], $cred_token['screen_name'], $cred_token['oauth_token'], $cred_token['oauth_token_secret'], $userDetail['fr_count'], $userDetail['fo_count']);
	}

	public function getUserDetails($screen_name) {
		$response = $this->twitter->get('https://api.twitter.com/1.1/users/show.json?screen_name='.$screen_name);
		if(property_exists($response, 'id'))
			return array("id" => $response->id, "fr_count" => $response->friends_count, "fo_count" => $response->followers_count);
		else
			throw new Exception("API Request Error", 1);			
	}

	public function influencer_save($user_id, $social_key, $followers_count, $friends_count){
		$this->db->query(
			"INSERT INTO nr_influencer_twitter (user_id, social_key, tw_follower_count, tw_friend_count) 
			VALUES ({$user_id}, '{$social_key}', {$followers_count}, {$friends_count})
			ON DUPLICATE KEY UPDATE tw_follower_count = {$followers_count}, tw_friend_count = {$friends_count}");
	}

	public function auth_save($key, $tw_user_id, $tw_screen_name, $tw_access_token, $tw_token_secret){
		$this->db->query(
			"INSERT INTO nr_social_auth_twitter (id, oauth_token, oauth_token_secret, username, tw_user_id) 
			VALUES ('{$key}', '{$tw_access_token}', '{$tw_token_secret}', '{$tw_screen_name}', '{$tw_user_id}')
			ON DUPLICATE KEY UPDATE oauth_token = '{$tw_access_token}', oauth_token_secret = '{$tw_token_secret}'");
	}

	public function update() {
		$sql = "SELECT * FROM nr_influencer_twitter, nr_social_auth_twitter where nr_influencer_twitter.social_key = nr_social_auth_twitter.id";
		
		$query = $this->db->query($sql);
		$results = Model_Influencer_Twitter::from_db_all($query);
		foreach ($results as $result)
		{
			$this->twitter->setAccessToken($result->oauth_token , $result->oauth_token_secret);
			try {
				$userDetail = $this->getUserDetails($result->username);
			} catch(Exception $e) {
				echo 'Error: ' . $e->getMessage();
				exit;
			}
			
			$this->influencer_save($result->user_id, $result->social_key, $userDetail['fr_count'], $userDetail['fo_count']);
			$this->auth_save($result->social_key, $result->tw_user_id, $result->username, $result->oauth_token, $result->oauth_token_secret);
		}
	}
	
	public function index() {	
		$url = $this->login_url();
		$this->redirect($url, false);
	}
	
}