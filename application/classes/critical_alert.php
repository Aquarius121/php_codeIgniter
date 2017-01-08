<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Critical_Alert {

	protected $args;
	protected $backtrace;
	protected $content;
	protected $subject;

	public function __construct($args = null)
	{
		ob_start();
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		var_dump($backtrace);
		$this->backtrace = ob_get_contents();
		ob_end_clean();

		// $args are captured in
		// debug_backtrace() 
		$this->args = $args;
		
		// extract exception message for subject
		if (isset($backtrace[0]['args'][0]) &&
		    $backtrace[0]['args'][0] instanceof Exception)
		{
			$subject = $backtrace[0]['args'][0]->getMessage();
			$subject = (new View_Data())->cut($subject, 40);
			$subject = sprintf('Error Notification: %s', $subject);
			$this->subject = $subject;
		}			
	}
	
	public function send()
	{
		$ci =& get_instance();
		$em = new Email();
		$em->set_to_email($ci->conf('dev_email'));
		$em->set_from_email($ci->conf('crit_email'));
		
		$emails_block = Model_Setting::value('staff_email_critical_errors');
		$emails = Model_Setting::parse_block($emails_block);
		foreach ($emails as $email)
			$em->add_bcc($email);

		$env = new stdClass();
		$env->protocol = $ci->env['protocol'];
		$env->requested_uri = $ci->env['requested_uri'];
		$env->user_agent = $ci->env['user_agent'];
		$env->cookies = $ci->env['cookies'];
		$env->remote_addr = $ci->env['remote_addr'];
		$env->host = $ci->env['host'];
		$env->controller = get_class($ci);
		$env->uri = $ci->uri->uri_string;

		$content  = null;
		$content .= Date::utc()->format(Date::FORMAT_MYSQL);
		$content .= PHP_EOL;
		$content .= PHP_EOL;
		$content .= print_r($env, true);
		$content .= PHP_EOL;
		$content .= PHP_EOL;
		$content .= $this->content;

		$em->set_from_name('Newswire Alerts');
		if ($this->subject)
		     $em->set_subject($this->subject);
		else $em->set_subject('Error Notification');
		$em->set_message($content);

		if ($this->args)
		{
			$buffer_args = File_Util::buffer_file();
			$json_args = new stdClass();
			$json_args->title = $this->subject;
			$json_args->data = $this->args;
			$html_args = $ci->load->view('shared/report/json', 
				$json_args, true);		
			file_put_contents($buffer_args, $html_args);
			$em->add_attachment($buffer_args, 'args.html');
		}

		$buffer_backtrace = File_Util::buffer_file();
		file_put_contents($buffer_backtrace, $this->backtrace);
		$em->add_attachment($buffer_backtrace, 'backtrace.txt');
		
		Mailer::send($em, Mailer::POOL_TRANSACTIONAL);
		unlink($buffer_backtrace);
		if ($this->args !== null)
			unlink($buffer_args);

		$this->log();
	}
	
	public function log()
	{		
		$date = Date::micro_utc()->format('Y_m_d_H_i_s_u');
		$file = "application/logs/critical_alert/{$date}.log";
		file_put_contents($file, $this->backtrace);
		$shell_file = escapeshellarg($file);
		shell_exec(sprintf('gzip %s', $shell_file));
	}

	public function set_subject($subject)
	{
		$this->subject = $subject;
	}

	public function set_content($content)
	{
		$this->content = $content;
	}
	
}