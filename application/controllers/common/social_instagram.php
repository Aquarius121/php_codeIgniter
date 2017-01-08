<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use MetzWeb\Instagram\Instagram;
class Social_Instagram_Controller extends CIL_Controller {
	
	protected $instagram_config;
	protected $key_prefix = "influencer_";
	protected static $scope = array(
		'basic',
		'likes',
		'follower_list'
	);
	
	public function __construct() {
		parent::__construct();

		$this->instagram_config = $this->conf('social_instagram_app');
		$this->apiKey = $this->instagram_config['api']['apiKey'];
		$this->apiSecret = $this->instagram_config['api']['apiSecret'];
		$base_url = $this->instagram_config['base_url'];		
		$redirect_uri = $base_url."/callback";
		$this->instagram = new Instagram(array(
		    'apiKey'      => $this->apiKey,
			'apiSecret'   => $this->apiSecret,
			'apiCallback' => $redirect_uri
		));
		$this->user = Auth::user();
	}

	protected function login_url($params = array())	{
		// remove any existing session
		$scope = static::$scope;
		return $this->instagram->getLoginUrl($scope);
	}
	
	public function callback() {
		
		$code = $_GET['code'];
		$data = $this->instagram->getOAuthToken($code);
		$user_id = $data->user->id;
		//var_dump($user_id);
		$this->instagram->setAccessToken($data);
		$follower_count = $this->getFollowerCount($user_id);
		$social_key = $this->key_prefix . $this->user->id;
		$this->influencer_save($user_id, $social_key, $follower_count);
		$this->auth_save($social_key, $user_id, $data->user->username, $data->access_token);
	}

	public function getFollowerCount($user_id) {
		$follower = $this->instagram->getUserFollower($user_id);
		$follower_count = count($follower->data);
		return $follower_count;
	}

	public function influencer_save($user_id, $social_key, $follower_count){
		$this->db->query(
			"INSERT INTO nr_influencer_instagram (user_id, social_key, in_follower_count) 
			VALUES ({$user_id}, '{$social_key}', {$follower_count})
			ON DUPLICATE KEY UPDATE in_follower_count = {$follower_count}");
	}

	public function auth_save($key, $in_user_id, $in_user_name, $in_access_token){
		$this->db->query(
			"INSERT INTO nr_social_auth_instagram (id, in_user_id, in_user_name, access_token) 
			VALUES ('{$key}', '{$in_user_id}', '{$in_user_name}', '{$in_access_token}')
			ON DUPLICATE KEY UPDATE access_token = '{$in_access_token}', in_user_id = '{$in_user_id}'");
	}

	public function update() {
		$sql = "SELECT * FROM nr_influencer_instagram, nr_social_auth_instagram where nr_influencer_instagram.social_key = nr_social_auth_instagram.id";
			
		$query = $this->db->query($sql);
		$results = Model_Influencer_Instagram::from_db_all($query);
		foreach ($results as $result)
		{			
			$this->instagram->setAccessToken($result->access_token);
			$friend_count = $this->getFollowerCount($result->in_user_id);
			$this->influencer_save($result->user_id, $result->social_key, $friend_count);
			//$this->auth_save($result->social_key, $result->in_user_id, $result->in_user_name, $result->in_access_token, $friend_count);
		}
	}
	
	public function index()	{
		$url = $this->login_url();
		$this->redirect($url, false);
	}
	
}