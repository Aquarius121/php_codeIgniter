<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Contact_Builder extends Model {
	
	protected static $__table = 'nr_contact_builder';
	protected static $__primary = 'id';
	
	public function delete()
	{
		parent::delete();
		$this->db->delete('nr_contact_builder_x_contact', 
			array('contact_builder_id' => $this->id));
	}
	
	public function add_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		
		$this->db->query("INSERT IGNORE INTO nr_contact_builder_x_contact
			(contact_builder_id, contact_id) VALUES (?, ?)", 
			array($this->id, (int) $contact));
	}
	
	public function add_all_contacts($contacts)
	{
		if (!count($contacts)) return;
		foreach ($contacts as $k => $contact)
			if ($contact instanceof Model_Contact)
				$contacts[$k] = $contact->id;
			
		$inserts = array();
		foreach ($contacts as $k => $contact)
		{
			$contact = (int) $contact;
			$inserts[] = "({$this->id}, {$contact})";
		}
		
		$inserts_str = comma_separate($inserts);
		$this->db->query("INSERT IGNORE INTO nr_contact_builder_x_contact
			(contact_builder_id, contact_id) VALUES {$inserts_str}");
	}
	
	public function remove_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		
		$this->db->query("DELETE FROM nr_contact_builder_x_contact
			WHERE contact_builder_id = ? AND contact_id = ?", 
			array($this->id, (int) $contact));
	}
	
	public function count_contacts()
	{
		$dbr = $this->db->query("SELECT COUNT(*) AS count FROM nr_contact_builder_x_contact
			WHERE contact_builder_id = ?", array($this->id));
		return $dbr->row()->count;
	}

	public function contacts_id_list()
	{
		$contacts = array();
		$dbr = $this->db->query("SELECT contact_id FROM nr_contact_builder_x_contact
			WHERE contact_builder_id = ?", array($this->id));
		foreach ($dbr->result() as $result)
			$contacts[] = $result->contact_id;
		return $contacts;
	}
	
}

?>