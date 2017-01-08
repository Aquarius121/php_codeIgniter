<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Scheduled_Notifications extends CLI_Base {
	
	public function index()
	{
		while (true)
		{
			set_time_limit(60);
			$sql = "SELECT * FROM nr_scheduled_notification sn 
				ORDER BY sn.id ASC LIMIT 1";
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$sch_n = Model_Scheduled_Notification::from_db($result);
			if (!$sch_n) break;
			$sch_n->delete();
			
			$m_user = Model_User::find($sch_n->user_id);
			
			if ($sch_n->class == Model_Scheduled_Notification::CLASS_CONTENT_SCHEDULED)
			{
				if (!($m_content = Model_Content::find($sch_n->related_id))) continue;
				$m_newsroom = Model_Newsroom::find($m_content->company_id);
				
				if ($m_user->is_virtual())
				{
					$cvs = Model_Content_Virtual_Source::find($m_content->id);
					$vs = Model_Virtual_Source::find($cvs->virtual_source_id);
					if (!$vs->callback) continue;

					$iella = Virtuals_Callback_Iella_Request::create($vs);
					$iella->data->uuid = $cvs->remote_uuid;
					$iella->send('content_event/scheduled');
					continue;
				}
				else
				{
					$en = new Email_Notification();
					$en->set_content_view('content_scheduled');
					$en->set_data('content', $m_content);
					$en->set_data('timezone', $m_newsroom->timezone);
					$en->send($m_user, 'Content Scheduled', true);
					continue;	
				}
			}
			
			if ($sch_n->class == Model_Scheduled_Notification::CLASS_CONTENT_UNDER_REVIEW)
			{
				if (!($m_content = Model_Content::find($sch_n->related_id))) continue;
				
				if ($m_user->is_virtual())
				{
					$cvs = Model_Content_Virtual_Source::find($m_content->id);
					$vs = Model_Virtual_Source::find($cvs->virtual_source_id);
					if (!$vs->callback) continue;

					$iella = Virtuals_Callback_Iella_Request::create($vs);
					$iella->data->uuid = $cvs->remote_uuid;
					$iella->send('content_event/under_review');
					continue;
				}
				else
				{
					$en = new Email_Notification();
					$en->set_content_view('content_under_review');
					$en->set_data('content', $m_content);
					$en->send($m_user, 'Content Under Review', true);
					continue;	
				}
			}
			
			if ($sch_n->class == Model_Scheduled_Notification::CLASS_CONTENT_APPROVED)
			{
				if (!($m_content = Model_Content::find($sch_n->related_id))) continue;		
				
				if ($m_user->is_virtual())
				{
					$cvs = Model_Content_Virtual_Source::find($m_content->id);
					$vs = Model_Virtual_Source::find($cvs->virtual_source_id);
					if (!$vs->callback) continue;

					$iella = Virtuals_Callback_Iella_Request::create($vs);
					$iella->data->uuid = $cvs->remote_uuid;
					$iella->send('content_event/approved');
					continue;
				}
				else
				{
					$en = new Email_Notification();
					$en->set_content_view('content_approved');
					$en->set_data('content', $m_content);
					$en->send($m_user, 'Content Approved', true);
					continue;
				}
			}
			
			if ($sch_n->class == Model_Scheduled_Notification::CLASS_CONTENT_REJECTED)
			{
				if (!($m_content = Model_Content::find($sch_n->related_id))) continue;

				if ($m_user->is_virtual())
				{	
					$cvs = Model_Content_Virtual_Source::find($m_content->id);
					$vs = Model_Virtual_Source::find($cvs->virtual_source_id);
					if (!$vs->callback) continue;

					$iella = Virtuals_Callback_Iella_Request::create($vs);
					$iella->data->uuid = $cvs->remote_uuid;
					$iella->data->feedback =  unserialize($sch_n->data);
					$iella->send('content_event/rejected');
				}
				else
				{
					$en = new Email_Notification();
					$en->set_content_view('content_rejected');
					$en->set_data('content', $m_content);
					$en->set_data('feedback', unserialize($sch_n->data));
					$en->send($m_user, 'Content Rejected', true);
				}

				// also send to registered admins to they can follow up
				// when the content is premium. some users reported
				// that the email went to spam folder so this will help

				if ($m_content->is_premium)
				{
					$emails_block = Model_Setting::value('staff_email_content_rejected');
					$cc_emails = Model_Setting::parse_block($emails_block);
					$mock_user = Mock_User::from_object($m_user);

					foreach ($cc_emails as $cc_email)
					{
						$mock_user->email = $cc_email;
						$en->send($mock_user, 'Content Rejected', true);
					}
				}

				continue;
			}

			if ($sch_n->class == Model_Scheduled_Notification::CLASS_CONTENT_PUBLISHED)
			{
				if (!($m_content = Model_Content::find($sch_n->related_id))) continue;
				
				if ($m_user->is_virtual())
				{
					// virtual source notification is already handled
					// as an iella event in the content notification queue
					// TODO: normalize this behaviour to be the same for all types
					continue;
				}
				else
				{
					$en = new Email_Notification();
					$en->set_content_view('content_published');
					$en->set_data('content', $m_content);
					$en->send($m_user, 'Content Published', true);
					continue;
				}
			}
		}
	}
	
}

?>