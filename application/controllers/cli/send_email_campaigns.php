<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Email_Campaigns_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index()
	{
		// allow at most 1 process
		if ($this->process_count() > 1)
			return;

		while (true)
		{
			set_time_limit(86400);
			set_memory_limit('2048M');

			$spam_score_threshold = $this->conf('spam_score_threshold');

			$sql = "SELECT ca.* FROM nr_campaign ca 
				LEFT JOIN nr_content co ON ca.content_id = co.id
				WHERE ca.is_sent = 0 AND ca.is_draft = 0 
				AND ca.date_send <= UTC_TIMESTAMP() 
				AND ca.is_send_active = 0
				AND (co.is_published IS NULL OR co.is_published = 1 
					OR ca.allow_non_published_content = 1)
				ORDER BY ca.id ASC LIMIT 1";
			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;

			$campaign = Model_Campaign::from_db($result);
			if (!$campaign) break;

			if ($campaign->spam_score == -1)
			{
				$campaign->spam_score = $campaign->spam_vulnerability_score();
				$campaign->save();
			}

			if (!$campaign->bypass_spam_check)
			{
				if ($campaign->spam_score >= $spam_score_threshold)
				{
					$campaign->is_draft = 1;
					$campaign->save();
					$this->email_admin_with_spam_score($campaign);
					continue;
				}
			}
			
			$sql = "UPDATE nr_campaign 
				SET is_send_active = 1 
				WHERE id = ?";
				
			$this->db->query($sql, array($campaign->id));
			$credits_required = $campaign->credits_required();
			$user = Model_User::find_company_id($campaign->company_id);
			$credits_available = $user->email_credits();	

			if ($credits_available < $credits_required)
			{
				// is this an auto campaign? don't send email notification 
				$mcbc = Model_Content_Bundled_Campaign::find('campaign_id', $campaign->id);

				if (!$mcbc && !$user->is_mail_blocked(Model_User_Mail_Blocks::PREF_NO_CREDITS))
				{
					$en = new Email_Notification();
					$en->set_content_view('not_enough_email_credits');
					$en->set_data('m_campaign', $campaign);
					$en->set_data('credits_required', $credits_required);
					$en->set_data('credits_available', $credits_available);
					$en->send($user);
				}
				
				$this->remove_from_scheduled($campaign);
			}
			else if ($campaign->content_id && 
				($content = Model_Content::find($campaign->content_id)) && 
				($newsroom = Model_Newsroom::find($campaign->company_id)) && 
			   !$newsroom->is_active && $content->type != Model_Content::TYPE_PR)
			{
				$en = new Email_Notification();
				$en->set_content_view('email_content_is_inactive');
				$en->set_data('m_campaign', $campaign);
				$en->send($user);
				
				$this->remove_from_scheduled($campaign);
			}
			else
			{
				$user->consume_email_credits($credits_required);
				// record credit usage for the company
				Model_Company_Email_Count::create($campaign->company_id)
					->record($credits_required);

				$campaign->send_all();
				$sql = "UPDATE nr_campaign 
					SET is_sent = 1, is_send_active = 0
					WHERE id = ?";
							
				$this->db->query($sql, array($campaign->id));
			}

			// wait between each campaign
			// because they are heavy on cpu
			// and heavy on memory
			gc_collect_cycles();
			sleep(5);
		}
	}
	
	protected function remove_from_scheduled($campaign)
	{
		$sql = "UPDATE nr_campaign 
			SET is_sent = 0, is_draft = 1,
			is_send_active = 0 
			WHERE id = ?";
		
		$this->db->query($sql, array($campaign->id));
	}

	protected function email_admin_with_spam_score($campaign)
	{
		$emails_block = Model_Setting::value('staff_email_outreach_marked_spam');
		$emails = Model_Setting::parse_block($emails_block);

		foreach ($emails as $email)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email;
			$subject = 'URGENT: Email Campaign Not Sent for High Spam Score';
			$notification = new Email_Notification('admin/email_campaign_failure');
			$notification->set_data('campaign', $campaign);
			$notification->set_data('newsroom', Model_Newsroom::find_company_id($campaign->company_id));
			$notification->send($mock_user, $subject);
		}
	}
	
}