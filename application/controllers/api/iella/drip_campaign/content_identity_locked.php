<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/drip_campaign/base');

class Content_Identity_Locked_Controller extends Drip_Campaign_Base {

	public function index()
	{
		$m_content = Model_Content::from_object($this->iella_in->content);
		if ($m_content->type !== Model_Content::TYPE_PR) return;
		if ($m_content->is_premium) return;
		$this->vd->content = $m_content;
		$this->vd->user = $user = $m_content->owner();

		// permit the user to opt out of these mailings
		if ($user->is_mail_blocked(Model_User_Mail_Blocks::PREF_CONTENT_UPGRADE))
			return;
		
		$email_subject = 'Gain More Visibility Online with Newswire Premium Distribution Services';
		$email_view = 'api/iella/drip_campaign/content_identity_locked';
		$email_message = $this->load->view($email_view, null, true);

		$email = new Email();
		$email->__avoid_conversation();
		$email->set_to_email($user->email);
		$email->set_from_email(static::SENDER_EMAIL);
		$email->set_to_name(trim($user->name()));
		$email->set_from_name(static::SENDER_NAME);
		$email->set_subject($email_subject);
		$email->set_message($email_message);
		$email->enable_html();
		Mailer::queue($email, false, Mailer::POOL_MARKETING);
	}
	
}

?>