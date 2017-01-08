<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('common/auth_request');
lib_autoload('linkedin');

class Linkedin_Auth_Request_Controller extends Auth_Request_Base {
	
	protected $linkedin_config;
	protected $linkedin_company;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->linkedin_config = $this->conf('linkedin_app');
		$this->linkedin = new Linkedin($this->linkedin_config['clientId'], $this->linkedin_config['secret']);
	}

	protected function login_url()
	{
		$newsroom_name = $this->newsroom->name;
		$company_id = $this->newsroom->company_id;
		$oauth_verifier = md5($company_id);

		$base_url = $this->linkedin_config['base_url'];
		$redirect_uri = "{$base_url}/callback?newsroom={$newsroom_name}";
		$scope = "r_basicprofile rw_company_admin";

		$this->linkedin->set_scope($scope);
		$this->linkedin->set_redirect_uri($redirect_uri);
		$this->linkedin->set_oauth_verifier($oauth_verifier);
		$this->session->set('linkedin_oauth_verifier', $oauth_verifier);

		return $this->linkedin->get_authorize_url();
	}

	public function callback()
	{		
		$code = $this->input->get('code');
		$nr_name = $this->input->get('newsroom');
		$state = $this->input->get('state');

		if (empty($code) || empty($nr_name))
			return false;

		if (!$newsroom = Model_Newsroom::find('name', $nr_name))
			return false;

		$company_id = $newsroom->company_id;
		$oauth_verifier = md5($company_id);

		// CSRF attack
		if ($this->session->get('linkedin_oauth_verifier') !==  $state)
			return false;

		$base_url = $this->linkedin_config['base_url'];
		$redirect_uri = "{$base_url}/callback?newsroom={$nr_name}";
		$this->linkedin->set_redirect_uri($redirect_uri);

		$token = $this->linkedin->get_access_token($code, $state, $nr_name);

		if (!$token || !$token->access_token)
			return false;

		$this->linkedin->set_access_token($token->access_token);

		$l_company_id = $this->session->get('linkedin_company_id');

		$companies_list = $this->linkedin->get_user_companies();

		if (!@$companies_list->_total)
		{
			$relative_url = 'manage/newsroom/social/linkedin_not_company_admin';
			$url = $this->newsroom->url($relative_url);
			$this->redirect($url, false);
		}

		$find = array('company/', 'companies/');
		$replace = array(null, null);
		$l_company_id = str_replace($find, $replace, $l_company_id);
		$l_company_id = String_Util::normalize($l_company_id);
		
		$linkedin_companies = $companies_list->values;
		$linkedin_company_id = null;

		foreach ($linkedin_companies as $linkedin_company)
		{
			$l_company = String_Util::normalize($linkedin_company->name);
			if (String_Util::contains($l_company, $l_company_id))
				$linkedin_company_id = $linkedin_company->id;
		}

		$this->db->query(
			"INSERT INTO nr_social_auth_linkedin (company_id, 
			 access_token,  linkedin_company_id, date_renewed, expires_in) VALUES (?, ?, ?, UTC_TIMESTAMP(), ?)
			 ON DUPLICATE KEY UPDATE access_token = ?, 
			 linkedin_company_id = NULL, date_renewed = UTC_TIMESTAMP()",
			array($newsroom->company_id, 
				$token->access_token,
				$linkedin_company_id,
				$token->expires_in,
				$linkedin_company_id));
		
		$relative_url = 'manage/newsroom/social/auth_complete';
		$url = $this->newsroom->url($relative_url);
		$this->redirect($url, false);
	}
	
	
	public function index()
	{
		$url = $this->login_url();
		$this->redirect($url, false);
	}	
	
}

?>