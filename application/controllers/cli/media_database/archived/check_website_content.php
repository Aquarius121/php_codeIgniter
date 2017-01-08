<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Check_Website_Content_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index($worker_id)
	{
		$mutex = new Flock_Mutex(__FILE__);
		set_time_limit(86400);
		
		while (true)
		{
			$mutex->lock();
			
			$sql = "SELECT * FROM nr_contact_keyword_builder
				WHERE 
				    is_website_checked = 0
				AND is_company_found = 0
				AND is_excluded = 0
				AND is_locked = 0
				AND domain IS NOT NULL
				LIMIT 1";
				
			$dbr = $this->db->query($sql);
			$model = Model_Contact_Keyword_Builder::from_db($dbr);
			
			// lock all for that domain while we check
			$sql = "UPDATE nr_contact_keyword_builder
				SET is_locked = 1 WHERE domain = ?";
			$this->db->query($sql, array($model->domain));
			
			$mutex->unlock();
			
			$domain_url = "http://{$model->domain}/";
			$request = new HTTP_Request($domain_url);
			$website_url = $request->follow_redirects();
			
			if (!$website_url || !($response = $request->get()))
			{
				// update/unlock all for that domain
				$sql = "UPDATE nr_contact_keyword_builder
					SET is_website_checked = 1, is_company_found = 0,
					is_locked = 0 WHERE domain = ?";
				$this->db->query($sql, array($model->domain));
				continue;
			}
			
			// update/unlock all for that domain
			$sql = "UPDATE nr_contact_keyword_builder
				SET website = ? WHERE domain = ?";
			$this->db->query($sql, array($website_url, $model->domain));
			
			$sql = "SELECT c.id, c.first_name, c.last_name, c.company_name 
				FROM nr_contact_keyword_builder ckb
				INNER JOIN nr_contact c 
				ON c.id = ckb.contact_id
				WHERE ckb.domain = ?";
			$dbr = $this->db->query($sql, array($model->domain));
			$contacts = Model_Contact::from_db_all($dbr);
			
			$response_html = strtolower($response->data);
			
			foreach ($contacts as $contact)
			{
				$contact->is_company_found = 0;
				
				$values_to_check = array();	
				$company_name_parts = explode(' ', str_replace('-', ' ', $contact->company_name));
				$company_name = preg_replace('#[^a-z0-9]#i', '.{0,4}', $contact->company_name);
				$company_name = strtolower($company_name);
				$values_to_check[] = $company_name;
				
				foreach ($company_name_parts as $k => $part)
				{
					if (!isset($company_name_parts[$k+1]))	break;
					$combined = implode(' ', array(
						$company_name_parts[$k],
						$company_name_parts[$k+1],
					));
					
					$combined = preg_replace('#[^a-z0-9]#i', '.{0,4}', $combined);
					$combined = strtolower($combined);
					$values_to_check[] = $combined;
				}
				
				if ($contact->first_name && $contact->last_name)
				{
					$first_name = preg_replace('#[^a-z0-9]#i', '.{0,4}', $contact->first_name);
					$last_name = preg_replace('#[^a-z0-9]#i', '.{0,4}', $contact->last_name);
					$first_name = strtolower($first_name);
					$last_name = strtolower($last_name);
					
					$values_to_check[] = sprintf('%s.{0,4}%s', 
						preg_quote(substr($first_name, 0, 1)),  
						preg_quote($last_name));
					$values_to_check[] = sprintf('%s.{0,4}%s', 
						preg_quote($first_name), 
						preg_quote($last_name));
				}
				
				foreach ($values_to_check as $value)
				{
					$pattern = sprintf('#%s#s', $value);
					
					if (preg_match($pattern, $response_html))
					{
						// update as found for that contact
						$sql = "UPDATE nr_contact_keyword_builder
							SET is_website_checked = 1, is_company_found = 1,
							is_locked = 0 WHERE contact_id = ?";
						$this->db->query($sql, array($contact->id));
						
						$this->trace(sprintf('%02d', $worker_id), 'found', 
							$model->domain, $contact->first_name, 
							$contact->last_name, $contact->company_name);
						
						// breaks out of values_to_check
						$contact->is_company_found = 1;
						break;
					}
				}
				
				if (!$contact->is_company_found)
				{
					$this->trace(sprintf('%02d', $worker_id), 'not found',
						$model->domain, $contact->first_name, 
						$contact->last_name, $contact->company_name);
					
					// update as not found for that contact
					$sql = "UPDATE nr_contact_keyword_builder
						SET is_website_checked = 1, is_company_found = 0,
						is_locked = 0 WHERE contact_id = ?";
					$this->db->query($sql, array($contact->id));
				}
			}
		}
	}

}

?>