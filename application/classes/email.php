<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//
//   $em = new Email();
//
//   // software name for X-Mailer
//   $em = new Email('Super Awesome Mailer');
//
//   $em->set_to_email('destination@domain.com');
//   $em->set_subject('example subject');
//   $em->set_message('this is the message body');
//
//   // enable html (and plaintext) versions
//   $em->enable_html();
//
//   // optional name of sender and receiver
//   $em->set_to_name('John Smith');
//   $em->set_from_name('Sender Name');
//
//   // add one (or more) people to cc
//   $em->add_cc('another@domain.com');
//
//   // add attachment (file to read, filename in email)
//   $em->add_attachment('dir/a/b/file.dat', 'file.dat');
//
//   // add a raw email header
//   $em->set_header('X-Fruit', 'banana');
//
//   // send email (confirm)
//   $em->send(true);
//
//   // returns raw email
//   $em->send(false);
//

class Email
{
	private $to_email;
	private $to_name;
	private $from_email;
	private $from_name;
	private $return_email;
	private $sender_email;
	private $sender_name;
	private $reply_email;
	private $reply_name;
	private $subject;
	private $message;
	private $headers;
	private $attachments;
	private $cc;
	private $bcc;
	private $html; 
	private $mailer;
	
	public function __construct($mailer = false)
	{
		$this->mailer = $mailer;
		$this->attachments = array();
		$this->headers = array();
		$this->cc = array();
		$this->bcc = array();
		$this->html = false;
	}
	
	public function set_to_email($val)
	{
		$this->to_email = filter_var($val, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_to_name($val)
	{
		$this->to_name = $val;
	}
	
	public function set_from_email($val)
	{
		$this->from_email = filter_var($val, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_from_name($val)
	{
		$this->from_name = $val;
	}

	public function set_return_email($val)
	{
		$this->return_email = filter_var($val, FILTER_VALIDATE_EMAIL);
	}

	public function set_sender_email($val)
	{
		$this->sender_email = filter_var($val, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_sender_name($val)
	{
		$this->sender_name = $val;
	}

	public function set_reply_email($val)
	{
		$this->reply_email = filter_var($val, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_reply_name($val)
	{
		$this->reply_name = $val;
	}
	
	public function set_subject($val)
	{
		$this->subject = $val;
	}
	
	public function set_message($val)
	{
		$this->message = $val;
	}
	
	public function set_header($name, $value)
	{
		$this->headers[$name] = $value;
	}

	public function has_header($name)
	{
		return isset($this->headers[$name]);
	}
	
	public function add_attachment($file, $name)
	{
		$this->attachments[] = array($file, $name);
	}
		 
	public function add_cc($val)
	{
		$this->cc[] = $val;
	}

	public function add_bcc($val)
	{
		$this->bcc[] = $val;
	}
	
	public function set_cc($values)
	{
		$this->cc = $values;
	}

	public function set_bcc($values)
	{
		$this->bcc = $values;
	}
	
	public function enable_html()
	{
		$this->html = true;
	}

	public function __avoid_conversation()
	{
		$this->set_header(static::AVOID_CONVERSATION_HEADER, 
			UUID::create());
	}

	public function envelope_sender()
	{
		if ($this->sender_email)
		{
			$sender = $this->sender_email;
			if (isset($this->sender_name))
				$sender = sprintf('%s <%s>', 
					double_quote($this->sender_name),
					$this->sender_email);
			return $sender;
		}
		else if ($this->from_email)
		{
			$from = $this->from_email;
			if (isset($this->from_name))
				$from = sprintf('%s <%s>', 
					double_quote($this->from_name),
					$this->from_email);
			return $from;
		}
		else
		{
			return null;
		}
	}
	
	public function get($name)
	{
		if (isset($this->{$name}))
			return $this->{$name};
		return null;
	}
	
	// fetch a raw header
	private static function raw_header($name, $value)
	{
		return sprintf('%s: %s', $name, $value);
	}
	
	// fetch all raw headers line seperated
	private static function raw_header_lines($headers)
	{
		// no header lines
		if (count($headers) == 0)
		  return null;
		  
		$raw_headers = array();         
		foreach($headers as $name => $value)
		  $raw_headers[] = static::raw_header($name, $value);		
		$header_lines = implode(static::NL, $raw_headers);		
		return $header_lines;
	}
	
	// fetch a raw boundary   
	private static function raw_bound($bound, $close = false)
	{
		return sprintf(($close ? '--%s--' : '--%s'), $bound);
	}
	
	// handle html content
	// return headers
	private function compose_alt($headers = array())
	{
		if ($this->html)   
		{
			$bound            = md5(microtime());
			$html_message     = $this->message;
			$plain_message    = HTML2Text::email($this->message);
			$plain_message    = wordwrap($plain_message, static::PLAIN_TEXT_WRAP);
			$header_lines     = static::raw_header_lines($headers);
			$html_b64c        = chunk_split(base64_encode($html_message), 
			                       static::B64_CHUNK_LENGTH, static::NL);
						
			$this->message = implode(static::NL, array(
				static::raw_bound($bound), 
				static::raw_header(static::CONTENT_TYPE_HEADER, 
					static::PLAIN_HEADER_VALUE), 
				$header_lines,
				null, // end headers
				$plain_message,
				static::raw_bound($bound),
				static::raw_header(static::CONTENT_TYPE_HEADER,
					static::HTML_HEADER_VALUE),
				static::raw_header(static::ENCODING_HEADER, 
					static::B64_ENCODING_HEADER_VALUE),
				$header_lines,
				$html_b64c,
				static::raw_bound($bound, true),
			));
			
			return array(
				// alternative header
				static::CONTENT_TYPE_HEADER =>
					sprintf(static::ALT_HEADER_VALUE, $bound),
			);
		}
		else
		{
			$this->message = wordwrap($this->message, static::PLAIN_TEXT_WRAP);
		
			return array_merge($headers, array(
				// plain text header
				static::CONTENT_TYPE_HEADER =>
					static::PLAIN_HEADER_VALUE,
			));
		}
	}
	
	// handle attachments
	// return any headers
	private function compose_mixed($headers = array())
	{
		if (count($this->attachments) > 0)
		{
			$bound         = md5(microtime());
			$message       = $this->message;
			$header_lines  = static::raw_header_lines($headers);
			
			// actual message content
			$this->message = implode(static::NL, array(
				static::raw_bound($bound), 
				$header_lines,				
				null, // end headers
				$message,
			));
			
			foreach ($this->attachments as $attach)
			{
				$file = $attach[0];
				$name = $attach[1];
				
				if (!is_file($file)) 
				  continue;
				  
				$mime = mime_content_type($file);
				$data = file_get_contents($file);
				$b64c = chunk_split(base64_encode($data), 
					static::B64_CHUNK_LENGTH, static::NL); 
				
				// each file one a time
				$this->message = implode(static::NL, array(
					$this->message,
					static::raw_bound($bound), 
					// attachment
					static::raw_header(static::CONTENT_TYPE_HEADER,
						sprintf(static::ATTACH_HEADER_VALUE, $mime, $name)),
					// content encoding
					static::raw_header(static::ENCODING_HEADER, 
						static::B64_ENCODING_HEADER_VALUE),
					// content disposition
					static::raw_header(static::DISPOSITION_HEADER, 
						static::DISPOSITION_HEADER_VALUE),
					null,
					$b64c,
				));
			}
			
			// closing bound
			$this->message = implode(static::NL, array(
				$this->message,
				static::raw_bound($bound, true),
			));
			
			return array(
				// alternative header
				static::CONTENT_TYPE_HEADER =>
					sprintf(static::MIXED_HEADER_VALUE, $bound),
			);
		}
		
		return $headers;
	}

	private function add_name_to_email($name, $email)
	{
		// folding white space as per rfc2822
		$name = preg_replace('#\s+#', ' ', $name);

		// "David Smith" <example@domain.com>
		return sprintf('%s <%s>', double_quote($name), $email);
	}
	
	public function send($real_send = false)
	{
		$headers = array();
		$message = $this->message;
		$headers = $this->compose_alt($headers);
		$headers = $this->compose_mixed($headers);
		$raw_headers = array();

		// other headers defined by user
		foreach($this->headers as $name => $value)
		{
			// ensure user defined headers
			// do not break multipart 
			if (!isset($headers[$name]))
				$headers[$name] = $value;
		}
		
		$to = $this->to_email;
		$from = $this->from_email;
		$to_name = $this->to_name;
		$from_name = $this->from_name;
		$sender = $this->sender_email;
		$reply = $this->reply_email;
		$cc = comma_separate($this->cc);
		$bcc = comma_separate($this->bcc);

		// folding white space as per rfc2822
		$to_name = preg_replace('#\s+#', ' ', $to_name);
		$from_name = preg_replace('#\s+#', ' ', $from_name);
		
		// combine to name and email
		if (isset($this->to_name) && $this->to_name)
			$to = $this->add_name_to_email($this->to_name, $to);
		
		// header: mailer name
		$raw_headers[] = static::raw_header(static::MAILER_HEADER,
				($this->mailer ? $this->mailer : static::MAILER_HEADER_VALUE));

		// header: mime version
		$raw_headers[] = static::raw_header(static::MIME_HEADER,
			static::MIME_HEADER_VALUE);

		if (strlen($from) > 0)
		{
			// combine from name and email
			if (isset($this->from_name) && $this->from_name)
				$from = $this->add_name_to_email($this->from_name, $from);

			// header: from address
			$raw_headers[] = static::raw_header(static::FROM_HEADER, $from);
		}

		if ($sender)
		{
			// combine sender name and email
			if (isset($this->sender_name) && $this->sender_name)
				$sender = $this->add_name_to_email($this->sender_name, $sender);

			// header: sender address
			$raw_headers[] = static::raw_header(static::SENDER_HEADER, $sender);
		}

		if ($reply)
		{
			// combine reply name and email
			if (isset($this->reply_name) && $this->reply_name)
				$reply = $this->add_name_to_email($this->reply_name, $reply);

			// header: reply address
			$raw_headers[] = static::raw_header(static::REPLY_TO_HEADER, $reply);
		}

		if ($cc)
		{
			// header: cc addresses
			$raw_headers[] = static::raw_header(static::CC_HEADER, $cc);
		}		

		if ($bcc)
		{
			// header: bcc addresses
			$raw_headers[] = static::raw_header(static::BCC_HEADER, $bcc);
		}
		
		// headers from email construction
		foreach ($headers as $name => $value)
			$raw_headers[] = static::raw_header($name, $value);
		
		if ($real_send)
		{
			// actually send the email and return result
			$result = mail($to, $this->subject, $this->message, 
				implode(static::NL, $raw_headers));
			$this->message = $message;
			return $result;
		}
		else
		{
			// return the email in plain text
			$result = implode(static::NL, array(
				sprintf('To: %s', $to),
				sprintf('Subject: %s', $this->subject),
				implode(static::NL, $raw_headers),
				null, // end headers 
				$this->message,
			));
			
			$this->message = $message;
			return $result;
		}
	}

	const MAILER_HEADER              = 'X-Mailer';
	const MAILER_HEADER_VALUE        = 'Achilles';

	const MIME_HEADER                = 'MIME-Version';
	const MIME_HEADER_VALUE          = '1.0';
	const BCC_HEADER                 = 'BCC';   
	const CC_HEADER                  = 'CC';   
	const FROM_HEADER                = 'From';   
	const SENDER_HEADER              = 'Sender';   
	const REPLY_TO_HEADER            = 'Reply-To';   
	const CONTENT_TYPE_HEADER        = 'Content-Type';
	const ATTACH_HEADER_VALUE        = '%s; name="%s"';
	const ENCODING_HEADER            = 'Content-Transfer-Encoding';
	const B64_ENCODING_HEADER_VALUE  = 'base64';
	const DISPOSITION_HEADER         = 'Content-Disposition';
	const DISPOSITION_HEADER_VALUE   = 'attachment';
	const MIXED_HEADER_VALUE         = 'multipart/mixed; boundary="%s"';
	const ALT_HEADER_VALUE           = 'multipart/alternative; boundary="%s"';
	const PLAIN_HEADER_VALUE         = 'text/plain; charset="utf-8"';
	const HTML_HEADER_VALUE          = 'text/html; charset="utf-8"';
	const AVOID_CONVERSATION_HEADER  = 'X-Entity-Ref-ID';
	const NL                         = "\r\n"; // new line

	const PLAIN_TEXT_WRAP            = 78;
	const B64_CHUNK_LENGTH           = 76;

}

?>
