<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

lib_autoload('linkedin');

class Social_Linkedin_Auth {
	
	public $company_id;
	public $access_token;
	public $linkedin_company_id;
	public $date_renewed;
	public $expires_in;
	
	public static function find($company)
	{
		$ci =& get_instance();
		$result = $ci->db->get_where('nr_social_auth_linkedin', 
			array('company_id' => $company));
		
		if (!$result->num_rows())
			return false;
			
		$row = $result->row();		
		$auth = new static();
		foreach ($row as $k => $v)
			$auth->$k = $v;
		
		return $auth;
	}
	
	public function test()
	{
		if (!$this->is_valid()) 
			return $this->delete();
		
		$res = null;
		$ci =& get_instance();
		$linkedin_config = $ci->conf('linkedin_app');

		if (!$this->linkedin_company_id)
			return $this->delete();

		$linkedin = new Linkedin($linkedin_config['clientId'], $linkedin_config['secret']);
		$linkedin->set_access_token($this->access_token);
		
		try {
			$is_admin = $linkedin->is_linkedin_company_admin($this->linkedin_company_id);
		}

		catch (Exception $e ) {}
		
		if (!isset($is_admin) || !$is_admin)
			$this->delete();
	}
	
	public function delete()
	{
		$ci =& get_instance();
		$ci->db->delete('nr_social_auth_linkedin', 
			array('company_id' => $this->company_id));
		$this->access_token = null;
	}
	
	public function is_valid()
	{
		return $this->access_token;
	}

	public function set_company($linkedin_company_id)
	{
		$ci =& get_instance();
		$this->linkedin_company_id = $linkedin_company_id;
		$ci->db->update('nr_social_auth_linkedin', 
			array('linkedin_company_id' => $this->linkedin_company_id),
			array('company_id' => $this->company_id));
	}
	
}

?>