<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Industry_Outreach_Mailer_Trait {
	
	// send a notification to editor that media campaign
	// must be sent out based on selected beats
	public function send_industry_outreach_notification($m_campaign, $beats, $contacts = null)
	{
		$emails_block = Model_Setting::value('staff_email_industry_outreach');
		$emails = Model_Setting::parse_block($emails_block);

		foreach ($emails as $email)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email;
			$subject = 'Industry outreach order has been created! [%d]';
			$subject = sprintf($subject, $m_campaign->id);
			$notification = new Email_Notification('industry_outreach/created');
			$notification->set_data('beats', $beats);
			$notification->set_data('contacts', $contacts);
			$notification->set_data('campaign', $m_campaign);
			$notification->set_data('content', Model_Content::find($m_campaign->content_id));
			$notification->set_data('newsroom', Model_Newsroom::find_company_id($m_campaign->company_id));
			$notification->send($mock_user, $subject);
		}
	}
	
}

?>