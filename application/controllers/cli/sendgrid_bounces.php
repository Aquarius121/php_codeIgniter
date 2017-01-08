<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class SendGrid_Bounces_Controller extends CLI_Base {
	
	public function index()
	{
		$sendgrid_api_key = $this->conf('sendgrid')['api_key'];
		$sendgrid_api = new SendGrid_API($sendgrid_api_key);
		$offset = 0;

		while ($offset !== false)
		{
			$bounces = $sendgrid_api->bounces_list(Date::days(-90), Date::$now, $offset);
			if (!$bounces) break;
			$offset = $bounces->next;

			foreach ($bounces->data as $bounce)
			{
				$criteria = array();
				$criteria[] = array('is_media_db_contact', '1');
				$criteria[] = array('is_unsubscribed', '0');
				$criteria[] = array('email', $bounce->email);
				$m_contact = Model_Contact::find($criteria);

				if ($m_contact)
				{
					$m_bounce = Model_Contact_Bounce::create($m_contact, $bounce->created);
					$m_bounce->reason = $bounce->reason;
					$m_bounce->save();
				}
			}
		}

		$sendgrid_api->bounces_delete_all();
	}

}