<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Subscribe_Controller extends Browse_Base {

	public $title = 'Manage Subscription';

	public function index()
	{
		$this->manage();
	}

	public function create_from_new_modal()
	{
		$email = $this->input->post('email');
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);

		if ($email)
		{
			$sub = Model_Contact::find_subscriber($this->newsroom->company_id, $email);
			if ($sub) return $this->json(false);

			$nr_contact = new Model_Contact();
			$nr_contact->email = $email;
			$nr_contact->company_id = $this->newsroom->company_id;
			$nr_contact->is_nr_subscriber = 1;
			$nr_contact->is_unsubscribed = 1;
			$nr_contact->save();

			$m_sub = new Model_Subscription();
			$m_sub->contact_id = $nr_contact->id;
			
			$m_sub->notify_pr = $this->input->post('pr_update');
			$m_sub->notify_news = $this->input->post('news_update');
			$m_sub->notify_event = $this->input->post('event_update');
			$m_sub->notify_blog = $this->input->post('blog_update');
			$m_sub->notify_facebook = $this->input->post('facebook_update');
			$m_sub->notify_twitter = $this->input->post('twitter_update');

			$m_sub->from_url = $this->input->post('from_url');

			$m_sub->date_subscribed = Date::$now;
			$m_sub->remote_addr = $this->env['remote_addr'];
			$m_sub->save();

			$this->send_verification_email($m_sub->id);

			return $this->json(true);
		}

		$this->json(false);
	}

	// Keeping this function for 
	// compatibility with older 
	// manage links
	public function manage($email, $company_id)
	{
		$criteria = array();
		$criteria[] = array('md5(email)', $email);
		$criteria[] = array('md5(company_id)', $company_id);
		$criteria[] = array('is_nr_subscriber', 1);
		
		if ($contact = Model_Contact::find($criteria))
		{
			if (!$sub = Model_Subscription::find('contact_id', $contact->id))
				show_404();

			if (!$sub_hash = Model_Subscription_Hash::find($sub->id))
				show_404();
			
			$url = $this->newsroom->url("browse/subscribe/edit/{$sub_hash->hash}");
			$this->redirect($url);
		}
		else
			show_404();
	}

	public function edit($sub_hash)
	{
		if (!$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);

		$m_contact = Model_Contact::find($m_sub->contact_id);

		if (!$m_sub || !$m_contact || !$m_contact->is_nr_subscriber)
			show_404();

		$this->vd->sub = $m_sub;
		$this->vd->contact = $m_contact;
		$this->vd->sub_hash = $sub_hash;

		$this->vd->newsroom_name = $this->newsroom->company_name;

		$nr_profile = Model_Company_Profile::find($this->newsroom->company_id);
		$this->vd->nr_profile = $nr_profile;

		$social_profiles = $nr_profile->get_social_wire_media();

		$this->vd->social_profiles = $social_profiles;
		$this->vd->wide_view = 1;
		$this->load->view('browse/header');
		$this->load->view('browse/subscribe/edit-form');
		$this->load->view('browse/footer');
	}

	public function edit_save()
	{
		if (!$this->input->post('update_subscription'))
			show_404();

		$sub_hash = $this->input->post('sub');
		if (!$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);

		if (!$m_sub || !$m_contact || !$m_contact->is_nr_subscriber)
			show_404();

		$m_sub->notify_pr = $this->input->post('pr_update');
		$m_sub->notify_news = $this->input->post('news_update');
		$m_sub->notify_event = $this->input->post('event_update');
		$m_sub->notify_blog = $this->input->post('blog_update');
		$m_sub->notify_facebook = $this->input->post('facebook_update');
		$m_sub->notify_twitter = $this->input->post('twitter_update');
		$m_sub->save();

		// if a user has unsubscribed and 
		// later decides to resume subscription
		// can just edit preferences and save
		$m_contact->is_unsubscribed = 0;
		$m_contact->save();
		$this->update_contact_list($m_sub);

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Subscription preferences updated successfully.');
		
		$this->add_feedback($feedback);
		$url = "browse/subscribe/edit/{$sub_hash}";
		$this->redirect($this->newsroom->url($url), false);
	}

	protected function update_contact_list($m_sub)
	{
		$m_contact = Model_Contact::find($m_sub->contact_id);
		if (!$m_contact || !$m_contact->is_nr_subscriber)
			return false;

		$c_list_criteria = array();
		$c_list_criteria[] = array('company_id', $m_contact->company_id);
		$c_list_criteria[] = array('is_nr_subscriber_list', '1');

		if (!$m_contact->is_unsubscribed)
		{
			// Add or update the contact list			
			if (!$nr_contact_list = Model_Contact_List::find($c_list_criteria))
			{
				$nr_contact_list = new Model_Contact_List();
				$nr_contact_list->name = 'Newsroom Subscribers';
				$nr_contact_list->company_id = $m_contact->company_id;
				$nr_contact_list->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$nr_contact_list->is_nr_subscriber_list = 1;
				$nr_contact_list->save();
			}

			$nr_contact_list->add_contact($m_contact);
		}
		elseif ($nr_contact_list = Model_Contact_List::find($c_list_criteria))
		{
			$nr_contact_list->remove_contact($m_contact);
			if (! $nr_contact_list->count_contacts())
				$nr_contact_list->delete();

		}	
	}

	public function send_verification_email($sub_id, $resend = 0)
	{
		if (!$sub_id)
			show_404();

		$m_sub = Model_Subscription::find($sub_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);
		if (!$m_sub || !$m_contact)
			show_404();

		if (!$sub_hash = Model_Subscription_Hash::find($sub_id))
		{
			$sub_hash = new Model_Subscription_Hash();
			$sub_hash->subscription_id = $sub_id;
			$sub_hash->hash = Data_Hash::__hash_hex($m_sub->id, 'sha256');
			$sub_hash->save();
		}

		$this->vd->newsroom = $this->newsroom;
		$this->vd->newsroom_name = $this->newsroom->company_name;

		$this->vd->url = $this->input->post('from_url');
		
		if(getenv('REMOTE_ADDR'))
			$this->vd->ip = getenv('REMOTE_ADDR');

		$url = "browse/subscribe/verify/{$sub_hash->hash}";
		$this->vd->activation_link = $this->newsroom->url($url);
		
		$message_view = 'browse/subscribe/emails/confirm-subscription';
		$message = $this->load->view($message_view, null, true);

		$subject = "Response Required: Verify Your Subscription To {$this->newsroom->company_name}'s Updates";
		$ci =& get_instance();
		$mail = new Email();
		$mail->set_to_email($m_contact->email);
		$mail->set_from_email($ci->conf('email_address'));
		$mail->set_from_name($ci->conf('email_address'));
		$mail->set_subject($subject);
		$mail->set_message($message);
		$mail->enable_html();
		Mailer::send($mail, Mailer::POOL_TRANSACTIONAL);
		
		if ($resend)
			$this->redirect('browse/subscribe/email_resent');
	}

	public function verify($sub_hash)
	{	
		if (!$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);

		if (!$m_sub || !$m_contact || !$m_contact->is_nr_subscriber)
			show_404();

		$m_contact->is_unsubscribed = 0;
		$m_contact->save();

		$this->update_contact_list($m_sub);

		$this->vd->newsroom_name = $this->newsroom->company_name;
		$this->vd->wide_view = 1;
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$text = "You have successfully subscribed to receive {$this->newsroom->company_name} updates.";
		$feedback->set_text($text);
		$this->add_feedback($feedback);
		$url = "browse/subscribe/edit/{$sub_hash}";
		$this->redirect($this->newsroom->url($url));
	}

	// Keeping this function for 
	// compatibility with older 
	// verification links, this 
	// will obsolete soon and 
	// we can remove this function
	public function confirm($email, $company_id)
	{	
		$criteria = array();
		$criteria[] = array('md5(email)', $email);
		$criteria[] = array('md5(company_id)', $company_id);
		$criteria[] = array('is_nr_subscriber', 1);
		
		if ($contact = Model_Contact::find($criteria))
		{
			if (!$sub = Model_Subscription::find('contact_id', $contact->id))
				show_404();

			if (!$sub_hash = Model_Subscription_Hash::find($sub->id))
				show_404();
			
			$url = $this->newsroom->url("browse/subscribe/verify/{$sub_hash->hash}");
			$this->redirect($url);
		}
		else
			show_404();
	}

	public function unsubscribe_all($sub_hash)
	{
		if (!$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);

		$this->vd->newsroom_name = $this->newsroom->company_name;
		$this->vd->sub_hash = $m_sub_hash->hash;
		$this->load->view('browse/header');
		$this->vd->remove_from_all = 1;
		$this->load->view('browse/subscribe/remove-from-all');
		$this->load->view('browse/footer');
	}
	
	// Keeping this function for 
	// compatibility with older 
	// remove from all links
	public function remove_from_all($email, $company_id)
	{
		if (!$email || !$company_id)
			show_404();

		$criteria = array();
		$criteria[] = array('md5(email)', $email);
		$criteria[] = array('md5(company_id)', $company_id);
		$criteria[] = array('is_nr_subscriber', 1);
		
		if ($contact = Model_Contact::find($criteria))
		{
			if (!$sub = Model_Subscription::find('contact_id', $contact->id))
				show_404();

			if (!$sub_hash = Model_Subscription_Hash::find($sub->id))
				show_404();
			
			$url = $this->newsroom->url("browse/subscribe/unsubscribe_all/{$sub_hash->hash}");
			$this->redirect($url);
		}
		else
			show_404();
	}

	public function unsubscribe()
	{
		$sub_hash = $this->input->post('sub');
		$is_confirmed = $this->input->post('confirm_unsubscribe');
		if (!$is_confirmed || !$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);

		if (!$m_sub || !$m_contact || !$m_contact->is_nr_subscriber)
			show_404();

		$m_sub->notify_pr = Model_Subscription::NOTIFY_NEVER;
		$m_sub->notify_news = Model_Subscription::NOTIFY_NEVER;
		$m_sub->notify_event = Model_Subscription::NOTIFY_NEVER;
		$m_sub->notify_blog = Model_Subscription::NOTIFY_NEVER;
		$m_sub->notify_facebook = Model_Subscription::NOTIFY_NEVER;
		$m_sub->notify_twitter = Model_Subscription::NOTIFY_NEVER;
		$m_sub->save();

		$m_contact->is_unsubscribed = 1;
		$m_contact->save();

		$this->update_contact_list($m_sub);

		$this->remove_success($sub_hash);
	}

	public function remove_success($sub_hash)
	{
		if (!$sub_hash || !$m_sub_hash = Model_Subscription_Hash::find('hash', $sub_hash))
			show_404();

		$m_sub = Model_Subscription::find($m_sub_hash->subscription_id);
		$m_contact = Model_Contact::find($m_sub->contact_id);

		if (!$m_sub || !$m_contact || !$m_contact->is_nr_subscriber)
			show_404();

		$this->vd->email = $m_contact->email;
		$this->vd->newsroom_name = $this->newsroom->company_name;
		$this->vd->wide_view = 1;
		$this->load->view('browse/header');
		$this->load->view('browse/subscribe/remove-all-success');
		$this->load->view('browse/footer');
	}

}

?>