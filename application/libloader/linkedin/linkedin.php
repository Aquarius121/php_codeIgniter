<?php

// TODO: take this class to git & composer.

class Linkedin {

	public $client_id;
	public $client_secret;
	public $redirect_uri;
	public $scope;
	public $oauth_verifier;
	public $access_token;

	public function __construct($client_id, $client_secret, $oauth_token = NULL, $oauth_token_secret = NULL) 
	{
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}

	public function authorize_url()
	{ 
		return 'https://www.linkedin.com/uas/oauth2/authorization'; 
	}

	public function set_scope($scope)
	{
		$this->scope = $scope;
	}

	public function set_redirect_uri($redirect_uri)
	{
		$this->redirect_uri = $redirect_uri;
	}

	public function set_oauth_verifier($oauth_verifier)
	{
		$this->oauth_verifier = $oauth_verifier;
	}

	public function set_access_token($access_token)
	{
		$this->access_token = $access_token;
	}	

	public function get_authorize_url() 
	{
		$params = array(
			'response_type' => 'code',
			'client_id' => $this->client_id,
			'scope' => $this->scope,
			'state' => $this->oauth_verifier, 
			'redirect_uri' => $this->redirect_uri,
		);
	 
		// Authentication request
		$authorize_url = $this->authorize_url();
		$url = "{$authorize_url}?" . http_build_query($params);
		
		return $url;
	}

	
	public function get_access_token($code)
	{
		if (!$code)
			return false;

		$redirect_uri = urlencode($this->redirect_uri);

		$params = array(
			'grant_type' => 'authorization_code',
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'code' => $code,
			'redirect_uri' => $redirect_uri
		);
		
		// Access Token request
		$url = urldecode('https://www.linkedin.com/uas/oauth2/accessToken?'.http_build_query($params));
		
		$context = stream_context_create(
			array('http' => 
				array('method' => 'POST',
				)
			)
		);

		$response = file_get_contents($url, false, $context);

		$token = json_decode($response);

		if (!$token)
			return false;

		return $token;
	}

	public function is_linkedin_company_admin($linkedin_company_id)
	{
		$endpoint = "/v1/companies/{$linkedin_company_id}/relation-to-viewer/is-company-share-enabled?format=json";
		return $response = $this->fetch('GET', $endpoint);
	}

	public function get_company_updates($linkedin_company_id)
	{
		$endpoint = "/v1/companies/{$linkedin_company_id}/updates?format=json";
		return $response = $this->fetch('GET', $endpoint);
	}

	public function get_user_companies()
	{
		$endpoint = "/v1/companies?format=json&is-company-admin=true";
		return $response = $this->fetch('GET', $endpoint);
	}

	public function share($message, $linkedin_company_id)
	{
		$access_token = $this->access_token;

		$message = "<?xml version='1.0' encoding='UTF-8'?> 
  					<share>
    					<visibility>
      						<code>anyone</code>
     					</visibility>
      					<comment>{$message}</comment>
   					</share>";

		$url = "https://api.linkedin.com/v1/companies/{$linkedin_company_id}/shares";
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $message );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Authorization: Bearer ' . $access_token));
		$response = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $response;
	} 


	public function fetch($method, $resource) 
	{
		$access_token = $this->access_token;

		$opts = array(
			'http'=>array(
				'method' => $method,
				'header' => "Authorization: Bearer " . $access_token . "\r\n" . "x-li-format: json\r\n"
			)
		);

		$url = 'https://api.linkedin.com' . $resource;

		// Append query parameters (if there are any)
		if (!empty($params) && count($params)) { $url .= '?' . http_build_query($params); }

		$context = stream_context_create($opts);

		$response = file_get_contents($url, false, $context);

		return json_decode($response);
	}
}

?>