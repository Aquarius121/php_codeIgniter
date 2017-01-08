<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Email_Callback extends Model {

	use Raw_Data_Trait;

	// email addresses are in this form 
	const ADDRESS_FORMAT = '%s@callback.newswire.com';
	
	protected static $__table = 'nr_email_callback';
	protected static $__primary = 'id';

	public static function create($event = null)
	{
		$instance = new static();
		$instance->id = UUID::create();
		$instance->event = $event;
		return $instance;
	}

	public function address()
	{
		return sprintf(static::ADDRESS_FORMAT, $this->id);
	}

	public static function find_address($address)
	{
		$id = explode('@', $address)[0];
		return static::find($id);
	}

	public function trigger($email)
	{
		if (!$this->event) return;

		$iella_event = new Iella_Event();
		$iella_event->data->callback = $this->values();
		$iella_event->data->email = $email;
		$iella_event->emit($this->event);
	}
	
}