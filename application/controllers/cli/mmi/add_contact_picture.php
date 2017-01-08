<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Add_Contact_Picture_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($email, $url)
	{
		if (!$email) return $this->trace_warn('usage: add_contact_picture <email> <url>');
		if (!$url) return $this->trace_warn('usage: add_contact_picture <email> <url>');

		$buffer_file = File_Util::buffer_file();
		@copy($url, $buffer_file);

		if (!Image::is_valid_file($buffer_file))
			return $this->trace_failure('invalid image');

		$contacts = Model_Contact::find_all(array(
			array('email', $email),
			array('is_media_db_contact', 1),
		));

		foreach ($contacts as $contact)
		{
			if ($contact_picture = Model_Contact_Picture::create($buffer_file))
			{
				if ($existing = Model_Contact_Picture::find($contact->id))
					$existing->delete();
				$contact_picture->contact_id = $contact->id;
				$contact_picture->save();
				$this->trace_success('added', $contact->id);
			}
		}
	}


}

?>