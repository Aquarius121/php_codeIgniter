<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mailer {

	const POOL_OUTREACH = 'outreach';
	const POOL_MARKETING = 'marketing';
	const POOL_TRANSACTIONAL = 'transactional';

	const QUEUE_RETRY_PERIOD = 7200;
	
	public static function send($email, $pool = 'default')
	{
		if (!static::__check($email, $pool)) return false;
		return static::__send($email);
	}

	public static function queue($email, $low_priority = false, $pool = 'default')
	{
		if (!static::__check($email, $pool)) return false;
		return static::__write_queue($email, $low_priority);
	}

	protected static function __check($email, $pool)
	{
		// someone passed invalid email
		if (!($email instanceof Email))
			return false;

		$ci =& get_instance();
		$recipient = $email->get('to_email');

		// some users don't have email address
		// so we will protect from failure here
		if (!$recipient) return false;

		// some users have a virtual address
		// so we will protect from failure here
		if (Virtual_User::is_virtual_email($recipient))
			return false;

		// prevent the sending of email to forced block contacts
		if (Model_Mailer_Blocked::find_email($recipient))
			return false;
		
		// in development environment
		// => set developer email
		if ($ci->is_development())
		{
			$email->set_to_email($ci->conf('dev_email'));
			$email->set_cc(array());
			$email->set_bcc(array());
		}

		// indicate to sendgrid
		// that we want to use the named
		// ip pool for email delivery
		$email->set_header('X-SMTPAPI',
			json_encode(array('ip_pool' => $pool)));

		return true;
	}

	public static function __send($email)
	{
		$ci =& get_instance();

		// someone passed invalid email
		if (!($email instanceof Email))
			return false;

		// add a default unsubscribe to our support address
		if (!$email->has_header('List-Unsubscribe'))
			$email->set_header('List-Unsubscribe', 
				sprintf('<mailto:%s>', $ci->conf('list_unsubscribe_email')));

		// ensure correct permissions
		$conf_file = $ci->conf('mailer_conf');
		chmod($conf_file, 0600);

		// write out to buffer file
		$file = File_Util::buffer_file();
		rename($file, sprintf('%s_%s.mail', $file, UUID::create()));
		file_put_contents($file, $email->send(false));
		
		// send the buffer 
		$output = array();
		$return_value = 0;
		$exec = $ci->conf('mailer_exec');
		$exec = sprintf($exec, escapeshellarg($email->envelope_sender()));
		$command = sprintf('cat %s | %s 2>&1', $file, $exec);
		exec($command, $output, $return_value);
		unlink($file);
		if ($return_value != 0)
			return false;
		return true;
	}

	public static function __read_queue($file)
	{
		$qdata = unserialize(file_get_contents($file));
		if (!($qdata->email instanceof Email)) return false;
		return $qdata;
	}

	public static function __write_queue($email, $low_priority = false)
	{
		$ci =& get_instance();

		if ($ci->is_development())
		{
			static::__send($email);
			return;
		}

		// construct queue object
		$qdata = new stdClass();
		$qdata->date_created = Date::$now;
		$qdata->email = $email;
		$qdata = serialize($qdata);
		
		$uuid = UUID::create();
		$qdir = $ci->conf('mailer_queue_dir');

		// low priority doesn't care what order to send
		// normal priority does, so use time() of sending
		$pnum = $low_priority ? PHP_INT_MAX : time();

		// write out the email to queue
		$path = sprintf('%s/%020d_%s.mail', $qdir, $pnum, $uuid);
		file_put_contents($path, $qdata);

		// set the modified time into the past so that there is no send delay
		// * see controllers/cli/process_mailer_queue for more info
		touch($path, Date::seconds(-static::QUEUE_RETRY_PERIOD)->getTimestamp());
	}

}

?>
