<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Contact_Controller extends Browse_Base {

	public $title = 'Contacts';

	const LISTING_LIMIT = 10;
	
	public function index()
	{
		$this->render_list();
	}

	protected function render_list()
	{
		$limit = static::LISTING_LIMIT;
		$offset = (int) $this->input->get('offset');
		$company_id = $this->newsroom->company_id;		
		
		$sql = "SELECT cc.* FROM nr_company_contact cc
			LEFT JOIN nr_company c ON c.id = ?
			AND c.company_contact_id = cc.id
			WHERE cc.company_id = ? 
			ORDER BY (c.company_contact_id IS NULL) ASC,	cc.first_name ASC, cc.last_name ASC
			LIMIT {$offset}, {$limit}";
		
		$query = $this->db->query($sql, array($company_id, $company_id));
		$results = Model_Company_Contact::from_db_all($query);
		$this->vd->results = $results;	
		
		if ($this->input->get('partial')) 
		{
			if (!count($results)) return $this->json(false);
			$content = $this->load->view('browse/partial-listing-contact', null, true);
			return $this->json(array('data' => $content));
		}
		
		$this->load->view('browse/header');
		$this->load->view('browse/listing-contact');
		$this->load->view('browse/footer');
	}

}

?>