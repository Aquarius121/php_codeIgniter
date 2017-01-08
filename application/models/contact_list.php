<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Contact_List extends Model {
	
	protected static $__table = 'nr_contact_list';
	protected static $__primary = 'id';
	
	public function delete()
	{
		parent::delete();
		$this->db->delete('nr_contact_list_x_contact', 
			array('contact_list_id' => $this->id));
		$this->db->delete('nr_campaign_x_contact_list', 
			array('contact_list_id' => $this->id));
	}
	
	public function add_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		
		$this->db->query("INSERT IGNORE INTO nr_contact_list_x_contact
			(contact_list_id, contact_id) VALUES (?, ?)", 
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
		$this->db->query("INSERT IGNORE INTO nr_contact_list_x_contact
			(contact_list_id, contact_id) VALUES {$inserts_str}");
	}
	
	public function remove_contact($contact)
	{
		if ($contact instanceof Model_Contact)
			$contact = $contact->id;
		
		$this->db->query("DELETE FROM nr_contact_list_x_contact
			WHERE contact_list_id = ? AND contact_id = ?", 
			array($this->id, (int) $contact));
	}

	public function remove_all_contacts()
	{
		$this->db->delete('nr_contact_list_x_contact', 
			array('contact_list_id' => $this->id));
	}
	
	public function count_contacts()
	{
		$dbr = $this->db->query("SELECT COUNT(*) AS count FROM nr_contact_list_x_contact
			WHERE contact_list_id = ?", array($this->id));
		return $dbr->row()->count;
	}
	
}

?>