<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Campaign extends Model {
	
	protected static $__table = 'nr_campaign';
	protected static $__primary = 'id';
	protected static $markers = array(
		'first-name' => 'First Name',
		'last-name' => 'Last Name',
		'email-address' => 'Email Address',
		'state' => 'State',
		'industry' => 'Industry',
		'tracking-link' => 'Tracking Link',
	);
	
	public static function markers()
	{
		return static::$markers;
	}
	
	public function set_lists($lists)
	{
		$this->db->query("DELETE FROM nr_campaign_x_contact_list
			WHERE campaign_id = ?", array($this->id));
		
		foreach ($lists as $list)
		{
			if ($list instanceof Model_Contact_List) $list = $list->id;
			$this->db->query("INSERT IGNORE INTO nr_campaign_x_contact_list
				(campaign_id, contact_list_id) VALUES (?, ?)", 
				array($this->id, (int) $list));
		}
	}
	
	public function get_lists()
	{
		$sql = "SELECT l.* FROM nr_contact_list l INNER JOIN 
			nr_campaign_x_contact_list x ON l.id = x.contact_list_id
			WHERE x.campaign_id = ?";
			
		$result = $this->db->query($sql, array($this->id));
		return Model_Contact_List::from_db_all($result);
	}

	public function load_content_data()
	{
		if (!($cd = Model_Campaign_Data::find($this->id))) return;
		foreach ($cd->values() as $k => $v)
			$this->$k = $v;
	}

	public function spam_vulnerability_score()
	{
		$message = $this->__raw_email_for_spam_check();
		$sa_client = new Spam_Assassin_Client();		
		return $sa_client->spam_score($message);
	}

	public function spam_report()
	{
		$message = $this->__raw_email_for_spam_check();
		$sa_client = new Spam_Assassin_Client();		
		return $sa_client->spam_report($message);
	}

	protected function __raw_email_for_spam_check()
	{
		$ci =& get_instance();
		$m_campaign_data = Model_Campaign_Data::find($this->id);			

		$contact = new Model_Contact();
		$contact->email = 'jamessmith@mail.com';
		$contact->first_name = 'James';
		$contact->last_name = 'Smith';

		$vd = array();
		$vd['contact'] = $contact;
		$vd['content'] = $this->generate_content($contact, null, false);
		$view = 'manage/partials/email-template';
		$content = $ci->load->view($view, $vd, true);
		$css_inliner = new CSS_Inliner();
		$css_inliner->set_html($content);
		$content = $css_inliner->convert();
		$to_name = implode(' ', array($contact->first_name, 
			$contact->last_name));

		$moe = $ci->conf('media_outreach_email');
		$em = new Email($moe['mailer']);
		$em->set_header('Message-ID', '<message@newswire.com>');
		$em->set_header('Date', Date::utc()->format(Date::FORMAT_RFC2822));
		$em->set_from_email($moe['sender_email']);
		$em->set_from_name($moe['sender_name']);
		$em->set_sender_email($moe['sender_email']);
		$em->set_sender_name($moe['sender_name']);
		$em->set_to_email($contact->email);
		$em->set_to_name($to_name);
		$em->set_header('List-Unsubscribe', 
			sprintf('<%s>', $contact->unsubscribe_link_instant($this->id)));
		$em->set_subject($this->subject);
		$em->set_message($content);
		$em->enable_html();

		$message = $em->send(false);
		return $message;
	}
	
	public function send($m_contact, $content = null)
	{
		if (!($m_contact instanceof Model_Contact))
			throw new Exception();

		// ensure that we have all the information required
		$contact = Model_Contact::find($m_contact->id);
		
		$ci =& get_instance();
		$nr = Model_Newsroom::find_company_id($this->company_id);
		
		$stats_hash = new Stats_Hash();
		$stats_hash->action = 'view';
		$stats_hash->contact = $contact->id;
		$stats_hash->campaign = $this->id;
		$context = $stats_hash->context_encoded();

		$stats_hash = new Stats_Hash();
		$stats_hash->action = 'view';
		$stats_hash->campaign = $this->id;
		$set_context = $stats_hash->context_encoded();

		$rec_enc = Stats_Engine::data_encode(array($context));
		$set_enc = Stats_Engine::data_encode($set_context);

		$stats_host = $ci->conf('stats_host');
		$view_pixel = "http://{$stats_host}/activate/im?rec={$rec_enc}&set={$set_enc}";
		
		$vd = array();
		$vd['pixel'] = $view_pixel;
		$vd['contact'] = $contact;
		$vd['content'] = $this->generate_content($contact, $content);
		$vd['unsubscribe'] = $contact->unsubscribe_link($this->id);
		$view = 'manage/partials/email-template';
		$content = $ci->load->view($view, $vd, true);
		$css_inliner = new CSS_Inliner();
		$css_inliner->set_html($content);
		$content = $css_inliner->convert();
		$to_name = implode(' ', array($contact->first_name, 
			$contact->last_name));
		
		$moe = $ci->conf('media_outreach_email');
		$em = new Email($moe['mailer']);
		$em->set_to_email($contact->email);
		$em->set_reply_email($this->sender_email);
		$em->set_reply_name($this->sender_name);

		if ($this->sender_use_from)
		{
			$em->set_from_email($this->sender_email);
			$em->set_from_name($this->sender_name);
		}
		else
		{
			$em->set_from_email($moe['sender_email']);
			$em->set_from_name($moe['sender_name']);
		}
		
		$em->set_sender_email($moe['sender_email']);
		$em->set_sender_name($moe['sender_name']);
		$em->set_header('List-Unsubscribe', 
			sprintf('<%s>', $contact->unsubscribe_link_instant($this->id)));
		if (trim($to_name)) $em->set_to_name($to_name);
		$em->set_subject($this->subject);
		$em->set_message($content);
		$em->enable_html();
		
		// queue with low priority
		return Mailer::queue($em, true, Mailer::POOL_OUTREACH);
	}
	
	public function send_test($contact)
	{
		if (!$contact->email)
			return false;
		
		$ci =& get_instance();
		$nr = Model_Newsroom::find_company_id($this->company_id);
		$contact->is_test = true;

		$vd = array();
		$vd['contact'] = $contact;
		$vd['content'] = $this->generate_content($contact, null, false);
		$view = 'manage/partials/email-template';
		$content = $ci->load->view($view, $vd, true);
		$css_inliner = new CSS_Inliner();
		$css_inliner->set_html($content);
		$content = $css_inliner->convert();
		$to_name = implode(' ', array($contact->first_name, 
			$contact->last_name));

		$moe = $ci->conf('media_outreach_email');
		$em = new Email($moe['mailer']);
		$em->set_to_email($contact->email);
		$em->set_reply_email($this->sender_email);
		$em->set_reply_name($this->sender_name);

		if ($this->sender_use_from)
		{
			$em->set_from_email($this->sender_email);
			$em->set_from_name($this->sender_name);
		}
		else
		{
			$em->set_from_email($moe['sender_email']);
			$em->set_from_name($moe['sender_name']);
		}

		$em->set_sender_email($moe['sender_email']);
		$em->set_sender_name($moe['sender_name']);
		$em->set_header('List-Unsubscribe', 
			sprintf('<%s>', $contact->unsubscribe_link_instant($this->id)));
		if (trim($to_name)) $em->set_to_name($to_name);
		$em->set_subject($this->subject);
		$em->set_message($content);
		$em->enable_html();
		
		return Mailer::send($em, Mailer::POOL_TRANSACTIONAL);
	}
	
	public function generate_content($contact, $content = null, $tracking = true)
	{
		if ($content === null)
		{
			if (!isset($this->content)) 
				$this->load_content_data();
			$content = $this->content;	
		}
		
		$ci =& get_instance();
		$m_content = Model_Content::find(value_or_null($this->content_id));
		$content_url = $m_content ? $m_content->url() : null;
		$tracking_url = $ci->website_url($content_url);
		$content_view_pixel_url = null;
		
		if ($tracking)
		{
			$stats_hash = new Stats_Hash();
			$stats_hash->contact = $contact->id;
			$stats_hash->campaign = $this->id;
			$stats_hash->action = 'click';
			$context = $stats_hash->context_encoded();

			$stats_hash = new Stats_Hash();
			$stats_hash->campaign = $this->id;
			$stats_hash->action = 'click';
			$set_context = $stats_hash->context_encoded();

			$rec_enc = Stats_Engine::data_encode(array($context));
			$set_enc = Stats_Engine::data_encode($set_context);

			$tracking_url = $ci->website_url($content_url);
			$tracking_url = "{$tracking_url}?rec={$rec_enc}&set={$set_enc}";

			if ($m_content && $ci->conf('stats_enabled'))
			{
				$builder = new Stats_URI_Builder();
				$builder->add_content_view($m_content->newsroom(), $m_content);
				$builder->add_remote_content_view($m_content, 'outreach/email');
				$builder->add_network_content_view($m_content);
				$content_view_pixel_url = $builder->build(null, true);
			}
		}

		$c_industry = ($contact->beat_1_id)?
				Model_Beat::find($contact->beat_1_id) : null;
		$c_state = ($contact->region_id)? 
				Model_Region::find($contact->region_id) : null;
		
		$content = Marker::replace_all($content, array(
			'first-name' => $contact->first_name,
			'last-name' => $contact->last_name,
			'email-address' => $contact->email,
			'industry' => ($c_industry)? $c_industry->name : 'industry',
			'state' => ($c_state)? $c_state->name : 'state',
			'tracking-link' => $tracking_url,
			'content-view-pixel' => $content_view_pixel_url,
		));
		
		return $content;
	}
	
	public function send_all()
	{	
		$recipient_contacts = array_values($this->recipient_contacts());
		$m_campaign_data = Model_Campaign_Data::find($this->id);
		if (($data_contacts = @unserialize($m_campaign_data->contacts)) === false)
			$data_contacts = array();
		
		$data_contacts_hash = array();
		foreach ($data_contacts as $contact_id)
			$data_contacts_hash[$contact_id] = true;

		Model_Content::enable_cache();
		Model_Newsroom::enable_cache();
		
		foreach ($recipient_contacts as $k => $contact)
		{
			$this->send($contact, $m_campaign_data->content);

			if ($k % 50  === 0)  sleep(1);
			if ($k % 500 === 0)  sleep(10);

			if (!isset($data_contacts_hash[$contact->id]))
			{
				$data_contacts_hash[$contact->id] = true;
				$data_contacts[] = $contact->id;

				// record the usage for contact history records
				$sql = "INSERT IGNORE INTO nr_contact_campaign_history VALUES (?, ?)";
				$this->db->query($sql, array($contact->id, $this->id));
			}
		}

		Model_Content::disable_cache();
		Model_Newsroom::disable_cache();
		
		$m_campaign_data->contacts = serialize($data_contacts);
		$m_campaign_data->save();
		
		$this->contact_count = count($data_contacts);
		$this->save();
	}
	
	public function credits_required()
	{
		$recipient_contacts = $this->recipient_contacts();
		$required_credits = count($recipient_contacts);
		unset($recipient_contacts);
		gc_collect_cycles();

		return $required_credits;
	}
	
	// should only be called on actual send
	// as this also updates lists with 
	// the last campaign as this
	protected function recipient_contacts()
	{
		// has to hold all contacts in 
		// memory (id, email)
		set_memory_limit('2048M');
		$m_contacts = array();
			
		if ($this->all_contacts)
		{
			$sql = "SELECT co.id, co.email
				FROM nr_contact co WHERE co.company_id = ?
				AND co.id NOT IN (
					SELECT contact_id 
					FROM nr_contact_company_unsubscribed cu
					WHERE cu.company_id = co.company_id 
				)
				AND co.is_unsubscribed = 0";
			$dbr = $this->db->query($sql, array($this->company_id));
			foreach ($dbr->result() as $contact)
				$m_contacts[$contact->email] = Model_Contact::from_db_object($contact);
				
			// Checking if this is a pitch wizard campaign
			$sql = "SELECT pl.contact_list_id 
				FROM nr_campaign c
				INNER JOIN pw_pitch_order po
				ON po.campaign_id = c.id
				INNER JOIN pw_pitch_list pl
				ON pl.pitch_order_id = po.id
				WHERE c.id = ?";

			if ($row = $this->db->query($sql, array($this->id))->row())
			{
				$sql = "SELECT co.id, co.email
					FROM nr_contact_list_x_contact x 
					INNER JOIN nr_contact co 
					ON x.contact_id = co.id
					WHERE x.contact_list_id = ?
					AND co.id NOT IN (
						SELECT contact_id 
						FROM nr_contact_company_unsubscribed cu
						WHERE cu.company_id = ?
					) 
					AND co.is_unsubscribed = 0";
			
				$dbr = $this->db->query($sql, array($row->contact_list_id, $this->company_id));
				foreach ($dbr->result() as $contact)
					$m_contacts[$contact->email] = Model_Contact::from_db_object($contact);
			}
		}
		else
		{
			$lists = $this->get_lists();

			foreach ($lists as $list)
			{
				$list->last_campaign_id = $this->id;
				$list->save();

				$sql = "SELECT co.id, co.email
					FROM nr_contact_list_x_contact x 
					INNER JOIN nr_contact co ON x.contact_id = co.id
					WHERE x.contact_list_id = ?
					AND co.id NOT IN (
						SELECT contact_id 
						FROM nr_contact_company_unsubscribed cu
						WHERE cu.company_id = ?
					) 
					AND co.is_unsubscribed = 0";
				
				$dbr = $this->db->query($sql, array($list->id, $this->company_id));
				foreach ($dbr->result() as $contact)
					$m_contacts[$contact->email] = Model_Contact::from_db_object($contact);
			}
		}

		gc_collect_cycles();
		return $m_contacts;
	}

	public function delete()
	{
		parent::delete();
		$this->db->delete('nr_campaign_x_contact_list', 
			array('campaign_id' => $this->id));
		$this->db->delete('nr_campaign_data', 
			array('campaign_id' => $this->id));
	}
	
}