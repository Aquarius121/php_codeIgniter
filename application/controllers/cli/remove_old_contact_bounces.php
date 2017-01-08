<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Remove_Old_Contact_Bounces_Controller extends CLI_Base {
	
	public function index()
	{	
		// Deleting contact bounces
		// more than 3 months old.

		$dt_3m_ago = Date::months(-3)->format(Date::FORMAT_MYSQL);		
		$sql = "DELETE FROM nr_contact_bounce 
			WHERE date_bounced < ?";

		$this->db->query($sql, array($dt_3m_ago));

		// Updating a contact to unsubscribe
		// if it has 2 or more bounces 
		// spread out over a period of 
		// at least 7 days

		$sql = "UPDATE nr_contact 
			SET is_unsubscribed = 1
			WHERE id IN (
				SELECT cb.contact_id 
				FROM nr_contact_bounce cb
				INNER JOIN (
					SELECT contact_id, 
					MIN(date_bounced) as min_date_bounced
					FROM nr_contact_bounce
					GROUP BY contact_id
					HAVING COUNT(contact_id) >= 2
					ORDER BY contact_id 
				) AS cb_counter
				ON cb.contact_id = cb_counter.contact_id
				AND DATEDIFF(cb.date_bounced, cb_counter.min_date_bounced) > 7
				GROUP BY cb.contact_id
			)";
	
		$this->db->query($sql);
	}
	
}