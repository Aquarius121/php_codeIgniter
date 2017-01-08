<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Register_Controller extends Website_Base {

	public $title = 'Journalist Registration';

	public function index()
	{
		$countries = Model_Country::find_all(null, 'name');
		$regions = Model_Region::find_all(null, 'name');

		$index_countries = array();
		foreach ($countries as $country)
			$index_countries[$country->id] = $country;
		foreach ($countries as $k => $country)
			$countries[$k] = $country->id;

		foreach ($regions as $region)
		{
			if (!$region->country_id) continue;
			if (!isset($index_countries[$region->country_id])) continue;
			if (!isset($index_countries[$region->country_id]->regions))
				$index_countries[$region->country_id]->regions = array();
			$index_countries[$region->country_id]->regions[] = $region;
		}

		$this->vd->countries = $countries;
		$this->vd->country_data = $index_countries;
		$this->vd->contact_roles = Model_Contact_Role::find_all(null, 'role');
		$this->vd->contact_media_types = Model_Contact_Media_Type::find_all(null, 'media_type');
		$this->vd->beats = Model_Beat::list_all_beats_by_group();

		$this->render_website('website/journalists/register');
	}

	public function thanks()
	{
		$this->render_website('website/journalists/register_thanks');
	}

	public function save()
	{
		$email = $this->input->post('email');
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if (!$email) $this->redirect('journalists/register');

		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$website_url = $this->input->post('website_url');
		$blog_url = $this->input->post('blog_url');
		$country = $this->input->post('country');
		$region = $this->input->post('region');
		$contact_role = $this->input->post('contact_role');
		$company_name = $this->input->post('company_name');
		$title = $this->input->post('title');
		$contact_media_type = $this->input->post('contact_media_type');
		$beats = json_decode($this->input->post('beats'));

		$m_contact = new Model_Contact();
		$m_contact->company_id = null;
		$m_contact->email = $email;
		$m_contact->first_name = $first_name;
		$m_contact->last_name = $last_name;
		$m_contact->title = $title;
		$m_contact->company_name = $company_name;
		$m_contact->country_id = $country;
		$m_contact->region_id = $region;
		$m_contact->contact_role_id = $contact_role;
		$m_contact->contact_media_type_id = $contact_media_type;
		
		for ($ib = 1; $ib <= 3; $ib++)
		{
			if (!isset($beats[$ib-1])) break;
			$property = "beat_{$ib}_id";
			$m_contact->{$property} = $beats[$ib-1];
		}

		$m_contact->save();
		$secret = $this->vd->secret = 
			substr(md5(microtime(true)), 0, 16);

		$m_sub = new Model_WireUpdate_Subscriber();
		$m_sub->contact_id = $m_contact->id;
		$m_sub_raw = new stdClass();
		$m_sub_raw->first_name = $first_name;
		$m_sub_raw->last_name = $last_name;
		$m_sub_raw->website_url = $website_url;
		$m_sub_raw->blog_url = $blog_url;
		$m_sub_raw->country = $country;
		$m_sub_raw->region = $region;
		$m_sub_raw->contact_role = $contact_role;
		$m_sub_raw->company_name = $company_name;
		$m_sub_raw->title = $title;
		$m_sub_raw->contact_media_type = $contact_media_type;
		$m_sub_raw->beats = $beats;
		$m_sub_raw->secret = $secret;
		$m_sub->raw_data($m_sub_raw);
		$m_sub->save();

		$inserts = array();
		foreach ($beats as $beat)
			$inserts[] = sql_insert_line(array($m_contact->id, $beat));
		$inserts_str = comma_separate($inserts);
		$sql = "INSERT INTO nr_contact_beat_interest
			VALUES {$inserts_str}";
		$this->db->query($sql);
		
		// stores name/value in db
		$nv = new Model_Name_Value();
		$nv->date_expires = Date::days(30);
		$nv->name = $secret;
		$nv->value = $m_sub->id;
		$nv->save();

		// welcome email message to be sent to the user 
		$this->vd->contact = $m_contact;
		$this->vd->subscriber = $m_sub;
		$message = $this->load->view('email/journalists/register', 
			null, true);
		
		$email = new Email();
		$email->set_to_email($m_contact->email);
		$email->set_from_email($this->conf('email_address'));
		$email->set_to_name($m_contact->name());
		$email->set_from_name($this->conf('email_name'));
		$email->set_subject('Newswire: confirm your email address.');
		$email->set_message($message);
		$email->enable_html();
		Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);

		// redirect to thanks page
		$this->redirect('journalists/register/thanks');
	}
	
}

?>