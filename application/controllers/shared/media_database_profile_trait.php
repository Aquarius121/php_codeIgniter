<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Media_Database_Profile_Trait {

	public function set_profile_notes()
	{
		$contact_profile_id = $this->input->post('contact_profile_id');
		$notes = $this->input->post('notes');
		$user_id = Auth::user()->id;

		$m_notes = Model_Contact_Profile_Notes::find_id(array($contact_profile_id, $user_id));
		if (!$m_notes) $m_notes = new Model_Contact_Profile_Notes();
		$m_notes->contact_profile_id = $contact_profile_id;
		$m_notes->user_id = $user_id;
		$m_notes->notes = $notes;
		$m_notes->save();

		return $this->json(true);
	}

	public function fetch_full_profile()
	{
		$contact_id = $this->input->get('contact_id');
		$contact = Model_Contact::find($contact_id);
		if (!$contact->is_media_db_contact) die();

		// this will create on the fly if required
		$contact->profile = Model_Contact_Profile::find_for_contact($contact);
		$contact->profile_data = $contact->profile->raw_data();		
		$contact->notes = Model_Contact_Profile_Notes::find_id(array($contact->profile->id, Auth::user()->id));
		if ($contact->notes) $contact->notes = $contact->notes->notes;

		$contact->region = Model_Region::find($contact->region_id);
		$contact->locality = Model_Locality::find($contact->locality_id);
		$contact->country = Model_Country::find($contact->country_id);
		$contact->contact_role = Model_Contact_Role::find($contact->contact_role_id);
		$contact->contact_media_type = Model_Contact_Media_Type::find($contact->contact_media_type_id);
		$contact->beat_1 = Model_Beat::find($contact->beat_1_id);
		$contact->beat_2 = Model_Beat::find($contact->beat_2_id);
		$contact->beat_3 = Model_Beat::find($contact->beat_3_id);
		$contact->picture = Model_Contact_Picture::find($contact->id);
		$obfuscator = Media_Database_Contact_Access::email_obfuscator();
		$contact->email = $obfuscator->obfuscate_parts($contact->email);

		$user_id = Auth::user()->id;
		$newsroom_prefixes = Model_Newsroom::__prefixes('nr', 'newsroom');
		
		$sql = "SELECT ca.*, 
			{$newsroom_prefixes} FROM nr_campaign ca INNER JOIN 
			nr_contact_campaign_history cch ON cch.campaign_id = ca.id
			INNER JOIN nr_newsroom nr ON ca.company_id = nr.company_id
			AND nr.user_id = {$user_id}
			WHERE cch.contact_id IN (SELECT contact_id FROM nr_contact_x_contact_profile 
				WHERE contact_profile_id = {$contact->profile->id})
			AND ca.is_sent = 1
			GROUP BY ca.id
			ORDER BY ca.date_send DESC
			LIMIT 5";

		$contact->campaign_history = Model_Campaign::from_sql_all($sql, array(), array(
			'newsroom' => 'Model_Newsroom',
		));

		$this->load->view('shared/media_database/full_profile', 
			array('result' => $contact));
	}

	protected function add_profile_modal()
	{
		$profile_modal = new Modal();
		$profile_modal->set_title('View Profile');
		$profile_modal->set_id('md-profile-modal');
		$this->add_eob($profile_modal->render(850, 580));

		$this->add_eob($this->load->view(
			'shared/media_database/profile_modal_js',
			null, true));		
	}

}