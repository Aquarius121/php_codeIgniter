<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_Notification {

	protected $vd;
	protected $content_view;
	protected $container_view = 'email/container/index';
	protected $from_email;
	protected $from_name;
	
	public function __construct($view = null)
	{
		$this->content_view = $view;	
		$this->vd = array();
	}
	
	public function set_content_view($view)
	{
		$this->content_view = $view;
	}

	public function set_from_email($email)
	{
		$this->from_email = $email;
	}

	public function set_from_name($name)
	{
		$this->from_name = $name;
	}
	
	public function set_container_view($view)
	{
		$this->container_view = $view;
	}
	
	public function set_data($name, $value)
	{
		$this->vd[$name] = $value;
	}
	
	public function send($user, $subject = null, $subject_prefix = false)
	{
		$email = $this->generate($user, $subject, $subject_prefix);
		Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);
	}

	public function generate($user, $subject = null, $subject_prefix = false)
	{
		$this->vd['user'] = $user;
		$ci =& get_instance();
		
		$default_subject = 'Newswire Notification';
		$content_view = "email/notification/{$this->content_view}";
		$content = $ci->load->view($content_view, $this->vd, true);
		$this->vd['content_view'] = $content;
		
		if ($this->container_view)
		     $email_content = $ci->load->view($this->container_view, $this->vd, true);
		else $email_content = $content;
		
		// prefix will create subjects of the form: 'Newswire Notification: Subject'
		if ($subject_prefix) $subject = sprintf('%s: %s', $default_subject, $subject);
		if ($subject === null) $subject = $default_subject;
		
		// fallback to the default newswire notifcations email
		if (!$this->from_email) $this->from_email = $ci->conf('email_address');
		if (!$this->from_name) $this->from_name = $ci->conf('email_name');

		$email = new Email();
		// separate emails, not conversation (gmail)
		$email->__avoid_conversation();
		$email->set_to_email($user->email);
		$email->set_from_email($this->from_email);
		$email->set_to_name(trim($user->name()));
		$email->set_from_name($this->from_name);
		$email->set_subject($subject);
		$email->set_message($email_content);
		$email->enable_html();
		return $email;
	}

}

?>
