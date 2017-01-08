<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Release_Plus_Mailer {
	
	protected $ci;
	protected $disabled_providers = array(
		Model_Content_Release_Plus::PROVIDER_DIGITAL_JOURNAL,
		Model_Content_Release_Plus::PROVIDER_WORLDNOW,
	);
	
	public function __construct()
	{
		$ci =& get_instance();
		$this->ci = $ci;
	}

	// send a notification to editor that content is
	// updated and ready to transfer to RP provider
	public function send_updated($m_content, $m_plus, $comment = null)
	{
		if (!$m_plus->is_confirmed ||
			in_array($m_plus->provider, $this->disabled_providers))
			return;

		$emails_block = Model_Setting::value('staff_email_release_plus');
		$emails = Model_Setting::parse_block($emails_block);

		$last = Model_Content_Change::find_last($m_content->id, 2);
		$modified_fields = array();

		if (isset($last[0]) && isset($last[1]))
		{
			$considered_fields = array(
				'date_publish' => 'Date',
				'title' => 'Title',
				'summary' => 'Subheadline',
				'content' => 'Content',
			);

			$rd0 = $last[0]->raw_data_object();
			$rd1 = $last[1]->raw_data_object();

			foreach ($considered_fields as $field => $name)
				if ($rd0->content->{$field} != $rd1->content->{$field})
					$modified_fields[] = $name;
		}

		if (count($modified_fields))
		     $modified = sprintf(' (inc. %s)', 
		     		comma_separate($modified_fields, true));
		else $modified = null;

		foreach ($emails as $email)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email;
			$subject = 'URGENT: %s Update%s for: %s';
			$subject = sprintf($subject, $m_plus->code(), $modified, $m_content->title);
			$notification = new Email_Notification('release_plus/updated');
			$notification->set_data('plus', $m_plus);
			$notification->set_data('content', $m_content);
			$notification->set_data('newsroom', Model_Newsroom::find_company_id($m_content->company_id));
			$notification->set_data('comment', $comment);
			$notification->send($mock_user, $subject);
		}
	}
	
	// send a notification to editor that content is
	// approved and ready to transfer to RP provider
	public function send_approved($m_content, $m_plus)
	{
		if (!$m_plus->is_confirmed ||
			in_array($m_plus->provider, $this->disabled_providers))
			return;

		$emails_block = Model_Setting::value('staff_email_release_plus');
		$emails = Model_Setting::parse_block($emails_block);

		foreach ($emails as $email)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email;
			$subject = 'Content for %s has been approved: %s';
			$subject = sprintf($subject, $m_plus->name(), $m_content->title);
			$notification = new Email_Notification('release_plus/approved');
			$notification->set_data('plus', $m_plus);
			$notification->set_data('content', $m_content);
			$notification->set_data('newsroom', Model_Newsroom::find_company_id($m_content->company_id));
			$notification->send($mock_user, $subject);
		}
	}
	
	// send a notification to editor that content is
	// under review and might need to transfer to RP provider
	public function send_under_review($m_content, $m_plus)
	{
		if (!$m_plus->is_confirmed ||
			in_array($m_plus->provider, $this->disabled_providers))
			return;

		$emails_block = Model_Setting::value('staff_email_release_plus');
		$emails = Model_Setting::parse_block($emails_block);

		foreach ($emails as $email)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email;
			$subject = 'Content for %s has been submitted: %s';
			$subject = sprintf($subject, $m_plus->name(), $m_content->title);
			$notification = new Email_Notification('release_plus/under_review');
			$notification->set_data('plus', $m_plus);
			$notification->set_data('content', $m_content);
			$notification->set_data('newsroom', Model_Newsroom::find_company_id($m_content->company_id));
			$notification->send($mock_user, $subject);
		}
	}
	
}

?>