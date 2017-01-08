<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Iella_Base extends CIL_Controller {
	
	protected $iella_in;
	protected $iella_out;

	protected $catch_with_oee = true;
	protected $secret = null;	
	protected $ssl_optional = true;
	protected $ssl_required = false;
	
	public function __construct()
	{
		parent::__construct();

		$secret_file = $this->conf('iella_secret_file');
		$this->secret = file_get_contents($secret_file);
		if ($this->authorize() !== true) 
			die('access denied');
			
		// send errors as a critical alert
		$handler = array($this, 'error_handler');
		$this->env['ci_error_handler'] = $handler;
		set_error_handler($handler, E_WARNING | E_ERROR);
		
		// check for fatal errors on shutdown
		$handler = array($this, 'error_check_for_fatal');
		register_shutdown_function($handler);
		
		$this->iella_files = array();
		$this->iella_out = new stdClass();
		
		$iella_in_str = $this->input->get_post('iella-in');
		$this->iella_in = json_decode($iella_in_str);
		if (is_object($this->iella_in))
			$this->iella_in = Raw_Data::from_object($this->iella_in);
		
		$iella_files_str = $this->input->get_post('iella-files');
		$iella_files = (array) json_decode($iella_files_str);

		if ($iella_files)
		{
			foreach ($iella_files as $iella_file)
				if (is_file($_FILES[$iella_file->name]['tmp_name']))
					$this->iella_files[$iella_file->name] = 
						$_FILES[$iella_file->name]['tmp_name'];
		}
	}

	protected function __on_execution_start()
	{
		// -------------------------------
	}
	
	public function error_handler($elevel, $estr, $efile, $eline)
	{
		$error = new stdClass();
		$error->elevel = $elevel;
		$error->estr = $estr;
		$error->efile = $efile;
		$error->eline = $eline;
		$alert = new Critical_Alert($error);
		$alert->send();
	}
	
	public function error_check_for_fatal()
	{
		$error = error_get_last();
		if ($error['type'] != E_ERROR &&
			 $error['type'] != E_PARSE) return;
		chdir($this->env['cwd']);
			
		// invoke iella error handler	
		$this->error_handler($error['type'],
			$error['message'], $error['file'],
			$error['line']);
	}
	
	protected function send($exit = false)
	{
		ob_clean();
		$iella_out_str = json_encode($this->iella_out);

		if (!$iella_out_str && json_last_error() !== JSON_ERROR_NONE)
		{
			$iella_out_str = json_encode(null);
			$debug = new stdClass();
			$debug->uri = $this->env['requested_uri'];
			$debug->json_error = json_last_error_msg();
			$alert = new Critical_Alert($debug);
			$alert->send();
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output($iella_out_str);

		if ($exit === true)
		{
			$this->output->_display();
			exit();
		}
	}
	
	protected function authorize()
	{
		if ($_in_secret = $this->input->post('iella-secret'))
		{
			if ($_in_secret === $this->secret)
				return true;
		}
		else
		{
			$_in_secret_file = Stored_File::from_uploaded_file('iella-secret');
			
			if ($_in_secret_file->exists())
			{
				$_in_secret = $_in_secret_file->read();
				if ($_in_secret === $this->secret)
					return true;
			}
		}
	}

	// reschedule the iella event
	// [*] does not support files
	// [*] use only for trusted requests
	protected function reschedule($datetime)
	{
		if (!($datetime instanceof DateTime))
			throw new Exception();

		$uri = $this->full_request_uri();
		$m_sir = new Model_Scheduled_Iella_Request();
		$m_sir->url = $this->full_request_uri();
		$m_sir->data = $this->input->get_post('iella-in');
		$m_sir->date_execute = $datetime->format(Date::FORMAT_MYSQL);
		$m_sir->save();

		$this->send(true);
	}
	
	protected function __on_execution_end($exception)
	{
		parent::__on_execution_end($exception);
		
		if ($exception !== null)
		{
			$alert = new Critical_Alert($exception);
			$alert->send();

			$this->iella_out = new stdClass();
			$this->iella_out->exception = 
				$exception->__toString();
		}
		
		$this->send();
	}
	
}

?>
