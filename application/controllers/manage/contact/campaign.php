<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/common_pw_orders_trait');

class Campaign_Controller extends Manage_Base {	

	use Common_PW_Orders_Trait;
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Email Campaigns';
	}

	public function index()
	{
		$this->redirect('manage/contact/campaign/all');
	}
	
	public function all($chunk = 1)
	{
		$this->listing($chunk, 'all');
	}
	
	public function sent($chunk = 1)
	{
		$filter = 'ca.is_sent = 1';
		$this->listing($chunk, 'sent', $filter);
	}
	
	public function scheduled($chunk = 1)
	{
		$filter = 'ca.is_draft = 0 AND ca.is_sent = 0';
		$this->listing($chunk, 'scheduled', $filter);
	}
	
	public function draft($chunk = 1)
	{
		$filter = 'ca.is_sent = 0 AND ca.is_draft = 1';
		$this->listing($chunk, 'draft', $filter);
	}
	
	protected function listing($chunk, $status, $filter = 1)
	{
		$filter_all = 1;
		$terms_sql = 1;
		$company_id = $this->newsroom->company_id;
		$terms = $this->input->get('terms');
		$terms_sql = sql_search_terms(array('ca.name', 
			'ca.subject', 'co.title'), $terms);
				
		$this->load->view('manage/header');
		
		$chunkination = new Chunkination($chunk);
		$limit_str = $chunkination->limit_str();
		
		// order the campaigns by name if searching 
		// otherwise order in the order they were created
		$order = ($terms ? 'ca.name ASC' : 'ca.id DESC');

		$sql = "SELECT SQL_CALC_FOUND_ROWS ca.*,
			co.type AS content_type,
			po.id as pitch_order_id,
			po.status as pitch_status,
			co.title as content_title,
			ps.id as pw_session_id,
			bc.campaign_id as is_auto_campaign
			FROM nr_campaign ca 
			LEFT JOIN nr_content co 
			ON ca.content_id = co.id
			LEFT JOIN pw_pitch_order po
			ON po.campaign_id = ca.id
			LEFT JOIN pw_pitch_session ps
			ON ps.pitch_order_id = po.id
			LEFT JOIN nr_content_bundled_campaign bc
			ON ca.id = bc.campaign_id
			WHERE ca.company_id = ? AND {$filter} AND {$terms_sql}
			ORDER BY {$order} {$limit_str}";
		
		$query = $this->db->query($sql, array($company_id));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$results = array();
		foreach ($query->result() as $result)
			$results[] = $result;
		
		$url_format = gstring("manage/contact/campaign/{$status}/-chunk-");
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->add_order_detail_modal();
		
		$this->load->view('manage/contact/campaign');
		$this->load->view('manage/footer');
	}
	
	public function edit_from($content_id)
	{
		$company_id = $this->newsroom->company_id;
		$content = Model_Content::find($content_id);

		if ($content && $content->company_id == $company_id)
		{
			$this->vd->from_m_content = $content;	
			$content->load_content_data();
			$content->load_local_data();
			$this->vd->m_content = $content;
			$this->vd->default_content = $this->load->view(
				'manage/contact/partials/campaign_default_content', 
				array('m_content' => $content), true);	
			$this->vd->default_content_summary = $this->load->view(
				'manage/contact/partials/campaign_default_content_summary', 
				array('m_content' => $content), true);
		}
		
		$this->edit();
	}
	
	public function edit($campaign_id = null)
	{
		if ($campaign_id)
			  $this->vd->title[] = 'Edit Campaign';
		else $this->vd->title[] = 'New Campaign';
		
		$campaign = Model_Campaign::find($campaign_id);
		$company_id = $this->newsroom->company_id;
		$this->vd->campaign = $campaign;
		$pw_order = null;
		
		if ($campaign_id)
		{
			if ($pw_order = Model_Pitch_Order::find('campaign_id', $campaign_id))
			{
				$this->vd->pw_order = $pw_order;
				$pw_content = Model_Pitch_Content::find($pw_order->id);
				$this->vd->pw_content = $pw_content;
				if ($pw_order->status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER)
					$this->vd->pitch_requires_review = 1;
				
				$criteria = array();
				$criteria[] = array('pitch_order_id', $pw_order->id);
				$criteria[] = array('status', Model_Pitch_List::STATUS_SENT_TO_CUSTOMER);
				
				if ($pw_list = Model_Pitch_List::find($criteria))
				{
					$this->vd->pw_list = $pw_list;
					$sql = "SELECT COUNT(contact_id) AS contacts_count
						FROM nr_contact_list_x_contact 
						WHERE contact_list_id = ?";
					$query = $this->db->query($sql, array($pw_list->contact_list_id));
					$result = $query->row();
					$this->vd->pl_contacts_count = $result->contacts_count;
				}
			}
		}
		
		if ($campaign && $campaign->company_id != $company_id)
			$this->denied();
		
		if (!$pw_order && !$this->test_has_contact())
		{
			// no contacts added warning message
			$feedback_view = 'manage/contact/partials/no_contacts_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
		}
		
		$vd = array();
		$vd['lists_all'] = true;
		$vd['markers'] = Model_Campaign::markers();
		
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
		
		$vd['lists'] = Model_Contact_List::find_all(
			$criteria, array('name', 'asc'));
		
		$this->vd->related_lists = $campaign ? 
			$campaign->get_lists() : array();
			
		$this->vd->company_profile =
			Model_Company_Profile::find($company_id);

		$this->vd->pitch_templates = $this->get_pitch_templates();

		if ($campaign && $campaign->is_send_active)
		{
			$feedback = new Feedback('info');
			$feedback->set_title('Attention!');
			$feedback->set_text('The campaign is sending.
				You cannot make changes at this time.');
			$this->use_feedback($feedback);
		}
			
		if ($campaign)
		{
			$dt_date_send = Date::out($campaign->date_send, $this->local_tz());
			if ($dt_date_send->format('H:i') === '00:00')
			     $campaign->date_send_str = $dt_date_send->format('Y-m-d');
			else $campaign->date_send_str = $dt_date_send->format('Y-m-d H:i');
			$m_content = $campaign->m_content = Model_Content::find($campaign->content_id);
			$campaign->load_content_data();

			if ($m_content)
			{
				$m_content->load_content_data();
				$m_content->load_local_data();
				$this->vd->m_content = $m_content;
				$this->vd->default_content = $this->load->view(
					'manage/contact/partials/campaign_default_content', 
					array('m_content' => $m_content), true);	
				$this->vd->default_content_summary = $this->load->view(
					'manage/contact/partials/campaign_default_content_summary', 
					array('m_content' => $m_content), true);
			}

			$ssmodal = new Modal();
			$ssmodal->set_title('Spam Report');
			$ssmodal->set_id('spam-report-modal');
			$this->add_eob($ssmodal->render(600, 600));
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/contact/campaign-edit', $vd);
		$this->load->view('manage/footer');
	}

	// check that a customer has created a contact
	// or added one from the media database
	protected function test_has_contact()
	{
		$sql = "SELECT 1 FROM nr_contact WHERE company_id = ?";
		$dbr = $this->db->query($sql, array($this->newsroom->company_id));
		if ($dbr->num_rows()) return true;

		$sql = "SELECT 1 FROM nr_contact c 
			INNER JOIN nr_contact_list_x_contact x ON x.contact_id = c.id
			INNER JOIN nr_contact_list cl ON x.contact_list_id = cl.id
			WHERE c.is_media_db_contact = 1 AND cl.company_id = ?";
		$dbr = $this->db->query($sql, array($this->newsroom->company_id));
		if ($dbr->num_rows()) return true;

		return false;
	}
	
	public function accept_pitch()
	{
		$campaign_id = $this->input->post('campaign_id');
		$m_campaign = Model_Campaign::find($campaign_id);
		$m_campaign->is_under_writing = 0;
		$m_campaign->save();
		
		$m_pw_order = Model_Pitch_Order::find('campaign_id', $campaign_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED;
		$m_pw_order->save();
		
		$process = Model_Pitch_Writing_Process::PROCESS_CUSTOMER_ACCEPTED;
		Model_Pitch_Writing_Process::create_and_save($m_pw_order->id, $process);
		
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->user_accepted_pitch($m_pw_order->id);		
	}
	
	protected function reject_pitch($comments = '')
	{
		$campaign_id = $this->input->post('campaign_id');
		$m_pw_order = Model_Pitch_Order::find('campaign_id', $campaign_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_CUSTOMER_REJECTED;
		$m_pw_order->save();
		
		$process = Model_Pitch_Writing_Process::PROCESS_CUSTOMER_REJECTED;
		Model_Pitch_Writing_Process::create_and_save($m_pw_order->id, $process, $comments);
		
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->user_rejected_pitch($m_pw_order->id, $comments);		
	}
	
	public function edit_save()
	{	
		// failed required.js validation 
		Required_JS_Enforcer::enforce();
		
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		$campaign_id = value_or_null($post['campaign_id']);

		// clean the html to make it safe
		$allowed_properties = array('border','border-color','border-style','border-width','float','height','margin','width');
		$post['content'] = value_or_null($this->vd->pure($post['content'], 
			array('CSS.AllowedProperties' => $allowed_properties)));

		if ($this->input->post('reject_after_editing'))
		{
			$campaign_id = $this->input->post('campaign_id');
			$m_campaign = Model_Campaign::find($campaign_id);
			$m_campaign->subject = $post['subject'];
			$m_campaign->save();
			$m_campaign_data = Model_Campaign_Data::find($campaign_id);
			$m_campaign_data->content = $post['content'];
			$m_campaign_data->save();
			
			$reason = Model_Pitch_Writing_Process::COMMENTS_CUSTOMER_EDITED;
			$this->reject_pitch($reason);
			$feedback = new Feedback('success');
			$feedback->set_title('Rejected!');
			$feedback->set_text('Thank you for your feedback. 
				We will revise the pitch per your 
				suggestions and get back to you.');
			
			$this->add_feedback($feedback);
			$this->redirect('manage/contact/campaign/all');
		}
		
		if ($this->input->post('approve_after_editing'))
		{
			$campaign_id = $this->input->post('campaign_id');
			$m_campaign = Model_Campaign::find($campaign_id);
			$m_campaign->subject = $post['subject'];
			$m_campaign->is_under_writing = 0;
			$m_campaign->save();
			$m_campaign_data = Model_Campaign_Data::find($campaign_id);
			$m_campaign_data->content = $post['content'];
			$m_campaign_data->save();
			
			$this->accept_pitch();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Pitch approved successfully');
			$this->add_feedback($feedback);
			$this->redirect("manage/contact/campaign/edit/{$campaign_id}");
		}
		
		if ($this->input->post('reject_button'))
		{
			$this->reject_pitch(value_or_null($this->input->post('rejection_reason')));
			$feedback = new Feedback('success');
			$feedback->set_title('Rejected!');
			$feedback->set_text('Thank you for your feedback. 
				We will revise the pitch per your 
				suggestions and get back to you.');
			
			$this->add_feedback($feedback);
			$this->redirect('manage/contact/campaign/all');		
		}
		
		if ($this->input->post('is_accept_pitch'))
		{
			$this->accept_pitch();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Pitch accepted successfully');
			$this->add_feedback($feedback);
			$this->redirect('manage/contact/campaign/edit/'.$this->input->post('campaign_id'));
		}
		
		if ($m_pw_order = Model_Pitch_Order::find('campaign_id', $campaign_id))
		{
			if ($this->input->post('publish') || $this->input->post('resend'))
				$this->accept_pitch();
		}
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$content = Model_Content::find(@$post['content_id']);
		if ($content && $content->company_id != $company_id)
			$post['content_id'] = null;
		
		// must check manually because
		// this is a checkbox and no tick
		// will not send a value
		if (!isset($post['all_contacts'])) $post['all_contacts'] = 0;
		if (!isset($post['is_draft'])) $post['is_draft'] = 0;
		
		if (isset($post['resend']))
		{
			$post['contact_count'] = null;
			$post['is_draft'] = 0;
			$post['is_sent'] = 0;
		}
		
		$campaign = Model_Campaign::find($campaign_id);
		if ($campaign && $campaign->company_id != $company_id) $this->denied();
		if (!$campaign) $campaign = new Model_Campaign();

		if ($campaign->is_send_active)
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Attention!');
			$feedback->set_text('The campaign is sending.
				You cannot make changes at this time.');
			$this->add_feedback($feedback);
			$this->redirect('manage/contact/campaign');
			return;
		}

		$campaign->values($post);
		
		if (!$campaign->name)
			$campaign->name = substr(md5(time()), 0, 16);
		
		// set the time to 00:00:00 on the date specified unless time provided	
		$dt_date_send = new DateTime(@$post['date_send'], $this->local_tz());
		if (!preg_match('#\d:\d\d#', @$post['date_send']))
			$dt_date_send->setTime(0, 0, 0);
		$dt_date_send->setTimezone(Date::$utc);
		$campaign->date_send = $dt_date_send->format(Date::FORMAT_MYSQL);

		// delay start while possibly making changes
		if ($dt_date_send < Date::minutes(5))
		{
			$dt_date_send = Date::minutes(5);
			$campaign->date_send = $dt_date_send->format(Date::FORMAT_MYSQL);
		}
		
		// if the send datetime is before the publish datetime
		if ($content && $dt_date_send < new DateTime($content->date_publish) &&
			!$campaign->allow_non_published_content)
		{
			// set them to be at the same time
			$campaign->date_send = $content->date_publish;
			
			// convert to local timezone again and set to 00:00:00
			// so that we can check if its actually the same day
			$new_dt_date_send = Date::out($campaign->date_send, $this->local_tz());
			$new_dt_date_send->setTime(0, 0, 0);
			$new_dt_date_send->setTimezone(Date::$utc);
			
			// if true => not the same day
			if ($new_dt_date_send > $dt_date_send)
			{	
				// load feedback message for the user
				$feedback_view = 'manage/contact/partials/campaign_date_warning_feedback';
				$feedback = $this->load->view($feedback_view, null, true);
				$this->add_feedback($feedback);
			}
		}
		else if ($content && !$content->is_published && !$campaign->allow_non_published_content)
		{
			// load feedback message for the user
			$feedback = new Feedback('warning');
			$feedback->set_title('Warning!');
			$feedback->set_text('The content is not published. We will not send the emails out until the content is published.');
			$this->add_feedback($feedback);
		}
		
		$campaign->company_id = $company_id;
		if ($this->input->post('content-render-variant') !== null)
			$campaign->template_id = $this->input->post('content-render-variant');
		$campaign->save();

		$lists = array();
		foreach ((array) @$post['lists'] as $contact_list_id)
		{
			if (!$contact_list_id) continue;
			if (!($list = Model_Contact_List::find($contact_list_id))) continue;
			if ($list->company_id != $company_id) continue;
			$lists[] = $list;
		}
		
		$campaign->set_lists($lists);
		
		$data = Model_Campaign_Data::find($campaign->id);
		if (!$data) $data = new Model_Campaign_Data();
		$data->content = $post['content'];
		$data->campaign_id = $campaign->id;
		$data->save();

		$campaign->spam_score = $campaign->spam_vulnerability_score();
		$campaign->save();

		if (Auth::is_admin_online())
		{
			$bypass_spam_check = isset($post['bypass_spam_check'])
				&& $post['bypass_spam_check'];
			$sender_use_from = isset($post['sender_use_from'])
				&& $post['sender_use_from'];
			$campaign->bypass_spam_check = $bypass_spam_check;
			$campaign->sender_use_from = $sender_use_from;
			$campaign->save();
		}

		if (isset($post['test']))
		{
			if ($campaign->spam_score >= $this->conf('spam_score_threshold') && 
					!$campaign->bypass_spam_check)
			{
				$feedback = new Feedback('warning');
				$feedback->set_title('Warning!');
				$feedback->set_text("Email content is vulnerable to be marked as spam. ");
				$feedback->add_text("Spam score: {$campaign->spam_score}.");
				$this->add_feedback($feedback);
			}
			else
			{
				$contact = Model_Contact::find_match($campaign->company_id, $post['test_email']);
				if (!$contact) $contact = new Model_Contact();
				$contact->company_id = $campaign->company_id;
				$contact->email      = !isset($post['test_email']) ?: $post['test_email'];
				$contact->first_name = !isset($post['test_first_name']) ?: $post['test_first_name'];
				$contact->last_name  = !isset($post['test_last_name']) ?: $post['test_last_name'];
				$contact->save();

				if ($campaign->send_test($contact))
				{
					// load feedback message for the user
					$feedback = new Feedback('success');
					$feedback->set_title('Success!');
					$feedback->set_text('The test email has been sent. The campaign is now saved as a draft.');
					$this->add_feedback($feedback);
				}
			}

			$campaign->is_draft = 1;
			$campaign->save();
			
			// redirect back to the campaign edit
			$redirect_url = "manage/contact/campaign/edit/{$campaign->id}";
			$this->redirect($redirect_url);
		}
		
		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The campaign has been saved.');
		$this->add_feedback($feedback);
		
		$credits_required = $campaign->credits_required();
		$credits_available = Auth::user()->email_credits();
		
		if (!$campaign->is_draft && !$credits_required)
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Could not send because no valid contacts were added.');
			$this->add_feedback($feedback);

			$feedback = new Feedback('info');
			$feedback->set_title('Note!');
			$feedback->set_text('You cannot contact people who have chosen to unsubscribe.');
			$this->add_feedback($feedback);

			$campaign->is_draft = 1;
			$campaign->save();
			
			// redirect back to the campaign edit screen
			$redirect_url = "manage/contact/campaign/edit/{$campaign->id}";
			$this->redirect($redirect_url);
		}

		if (!$campaign->is_draft && $credits_available < $credits_required)
		{
			// load feedback message for the user
			$feedback_view = 'manage/contact/partials/save_low_credits_warning_feedback';
			$feedback = $this->load->view($feedback_view, 
				array('credits_required' => $credits_required), true);
			$this->add_feedback($feedback);
		}
		else
		{
			$feedback = new Feedback('info');
			$feedback->set_text(sprintf('This campaign will consume %d email credit(s) 
				(%d available) for the selected set of contacts.', 
				$credits_required, $credits_available));
			$this->add_feedback($feedback);
		}
		
		// redirect back to the campaign list
		$redirect_url = sprintf('manage/contact/campaign/edit/%d', $campaign->id);
		$this->redirect($redirect_url);
	}
	
	public function delete($campaign_id)
	{
		if (!$campaign_id) return;
		$campaign = Model_Campaign::find($campaign_id);
		$company_id = $this->newsroom->company_id;
		
		if ($campaign && $campaign->company_id != $company_id)
			$this->denied();
		
		if ($this->input->post('confirm'))
		{
			$campaign->delete();

			// load feedback message 
			$feedback = new Feedback('success');
			$feedback->set_title('Deleted!');
			$feedback->set_text('The campaign has been removed.');
			$this->add_feedback($feedback);

			// redirect back to type specific listing
			$redirect_url = 'manage/contact/campaign/';
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->campaign_id = $campaign_id;
			$feedback_view = 'manage/contact/partials/campaign_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($campaign_id);
		}
	}
	
	public function find_content()
	{
		$company_id = $this->newsroom->company_id;
		$limit = (int) $this->input->post('limit');
		$offset = (int) $this->input->post('offset');
		
		$filter_types = array(Model_Content::TYPE_PR, 
			Model_Content::TYPE_NEWS, Model_Content::TYPE_EVENT);
		if (!$this->newsroom->is_active)
			$filter_types = array(Model_Content::TYPE_PR);
		$types_in_list = sql_in_list($filter_types);
		
		$sql = "SELECT c.*
			FROM nr_content c 
			WHERE c.company_id = ? 
			AND c.type IN ({$types_in_list}) 
			AND c.is_under_writing = 0
			ORDER BY c.id DESC
			LIMIT {$offset}, {$limit}";
				
		$query = $this->db->query($sql, array($company_id));
		$m_results = Model_Content::from_db_all($query);
		$results = array();
		
		foreach ($m_results as $m_content)
		{
			$m_content->load_content_data();
			$m_content->load_local_data();	
			$result = new stdClass();
			$result->id = $m_content->id;
			$result->type = Model_Content::short_type($m_content->type);
			$result->title = $this->vd->esc($this->vd->cut($m_content->title, 60));
			$result->subject = $m_content->title;
			$result->location = $m_content->location;
			$result->content = $this->load->view(
				'manage/contact/partials/campaign_default_content', 
				array('m_content' => $m_content), true);
			$result->content_summary = $this->load->view(
				'manage/contact/partials/campaign_default_content_summary', 
				array('m_content' => $m_content), true);
			$results[] = $result;
		}
		
		$response = new stdClass();
		$response->data = $results;
		$this->json($response);
	}

	public function get_pitch_templates()
	{
		$templates = array(
			(object) array(
				'id' => 'first_look',
				'title' => '[First Look] Title of the Press Release',
				'headline' => '[First Look] {{title}}',
				'hover_text' => 'Select [First Look] Template for product, service or website launch'
			),
			(object) array(
				'id' => 'exclusive',
				'title' => '[EXCLUSIVE] Title of the Press Release',
				'headline' => '[EXCLUSIVE] {{title}}',
				'hover_text' => 'Select [Exclusive] Template to highlight key story or newsworthy angle'
			),
			(object) array(
				'id' => 'media_advisory',
				'title' => '[MEDIA ADVISORY] Location: Title of the Press Release',
				'headline' => '[MEDIA ADVISORY] {{location}}: {{title}}',
				'hover_text' => 'Select [Media Advisory] Template for local or regional outlets regarding your company or event'
			),
		);

		$company = Model_Company::find($this->newsroom->company_id);
		$c_contact = Model_Company_Contact::find($company->company_contact_id);
		$c_profile = Model_Company_Profile::find($this->newsroom->company_id);

		$tpl = new stdClass();
		if ($c_contact && $c_contact->first_name && $c_contact->last_name)
			$tpl->full_name = sprintf('%s %s', $c_contact->first_name, $c_contact->last_name);
		elseif ($c_contact && $c_contact->first_name)
			$tpl->full_name = sprintf('%s', $c_contact->first_name);
		else $tpl->full_name = NULL;

		if ($c_contact && $c_contact->email)
			$tpl->email = $c_contact->email;
		elseif ($c_profile && $c_profile->email) 
			$tpl->email = $c_profile->email;
		else $tpl->email = Auth::user()->email;

		$tpl->cm_state = ($c_profile && $c_profile->address_state) ? 
			$c_profile->address_state : NULL;

		$results = array();

		foreach ($templates as $template) 
		{
			$result = new stdClass;
			$result->id = $template->id;
			$result->title = $template->title;
			$result->headline = $template->headline;
			$result->hover_text = $template->hover_text;
			$result->content = $this->load->view(
				'manage/contact/partials/campaign_pitch_templates', array('template' => $template,
					'company' => $company, 'tpl_data' => $tpl), true);
			$results[$template->id] = $result;
		}

		return $results;
	}
	
	public function load_pw_order_detail_modal($pitch_order_id)
	{
		$this->order_detail_modal($pitch_order_id); 
	}

	public function upload_image()
	{			
		$callback_view = 'manage/contact/campaign_image_upload_callback';
		$si_original = Stored_Image::from_uploaded_file('upload');
		$this->vd->upload_error = null;
		$this->vd->img_url = null;
		
		if (!$si_original->is_valid_image())
		{
			$this->vd->upload_error = 'The image must be in JPG, PNG or GIF format.';
			$this->load->view($callback_view);
			return;
		}
		
		if ($si_original->size() > 2 * 1024 * 1024)
		{
			$this->vd->upload_error = 'The image size must be less than 2MB';
			$this->load->view($callback_view);
			return;
		}

		$si_original->move();
		$img_url = $si_original->url();
		$this->vd->img_url = $this->website_url($img_url);
		$this->load->view($callback_view);
	}

}

?>