<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CLI_Base extends CIL_Controller {
	
	protected $trace_enabled = false;
	protected $trace_time = true;
	protected $log_enabled = true;
	protected $log_pid = true;
	protected $background_logger = false;
	protected $compression = false;

	// if the CLI is enabled
	protected $cli_enabled = true;

	public function __construct()
	{
		ini_set('display_errors', 'on');
		$level = error_reporting();
		$level = $level | E_ERROR | E_WARNING;
		error_reporting($level);
		restore_error_handler();
		
		parent::__construct();
		if (!$this->input->is_cli_request()) 
			exit(-1);

		// ensure that we use the cli_php_file
		// instead of the default index.php
		$file = basename($_SERVER['SCRIPT_FILENAME']);
		$cli_file = $this->conf('cli_php_file');
		if ($file !== $this->conf('cli_php_file'))
		{
			global $argv;
			$args = $argv;
			$args = array_slice($args, 1);
			$args = implode(' ', $args);

			$error = "error: do not use index file for cli tasks\r\n";
			$usage = "usage: php {$cli_file} {$args}";

			$color = new Colors\Color();
			$message  = null;
			$message .= $color($error)->red()->bold();
			$message .= $color($usage)->cyan();
			$this->console($message);
			exit;
		}
	}

	public function __on_execution_start()
	{
		parent::__on_execution_start();

		$switches = $this->switches();

		if (isset($switches->background_logger))
		{
			$this->log_enabled = true;
			$this->background_logger = true;
			$this->trace_time = false;
		}

		if (isset($switches->trace_enabled))
			$this->trace_enabled = true;
		if (isset($switches->trace_time))
			$this->trace_time = true;
	}

	public function __background_logger($buffer)
	{
		$this->log($buffer);
	}
	
	public function trace()
	{
		if (!$this->trace_enabled) return;
		$message = comma_separate(func_get_args(), true);
			
		if ($this->trace_time)
		{
			$time = Date::utc()->format(Date::FORMAT_LOG);
			$message = sprintf('[%s] %s', $time, $message);
		}
		
		$this->console($message);
	}
	
	public function console($text)
	{
		$args = func_get_args();
		$args = array_slice($args, 1);
		if (count($args) > 0)
			$text = vsprintf($text, $args);
		
		echo $text;
		echo PHP_EOL;
		$this->__flush();
	}
	
	public function dump($object)
	{
		var_dump($object);
		$this->__flush();
	}
	
	public function inspect($object)
	{
		echo json_encode($object);
		echo PHP_EOL;
		$this->__flush();
	}

	public function trace_success()
	{
		$color = new Colors\Color();
		$success = $color('success')->green()->bold();
		$args = func_get_args();
		array_unshift($args, $success);
		call_user_func_array(
			array($this, 'trace'), 
			$args
		);
	}

	public function trace_failure()
	{
		$color = new Colors\Color();
		$failure = $color('failure')->red()->bold();
		$args = func_get_args();
		array_unshift($args, $failure);
		call_user_func_array(
			array($this, 'trace'), 
			$args
		);
	}

	public function trace_warn()
	{
		$color = new Colors\Color();
		$warn = $color('warn')->yellow()->bold();
		$args = func_get_args();
		array_unshift($args, $warn);
		call_user_func_array(
			array($this, 'trace'), 
			$args
		);
	}

	public function trace_info()
	{
		$color = new Colors\Color();
		$info = $color('info')->cyan()->bold();
		$args = func_get_args();
		array_unshift($args, $info);
		call_user_func_array(
			array($this, 'trace'), 
			$args
		);
	}

	public function flush_success($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->green()->bold();
		$this->flush($message);		
	}

	public function flush_failure($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->red()->bold();
		$this->flush($message);		
	}

	public function flush_warn($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->yellow()->bold();
		$this->flush($message);		
	}

	public function flush_info($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->cyan()->bold();
		$this->flush($message);		
	}

	public function console_success($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->green()->bold();
		$args = func_get_args();
		$args[0] = $message;
		call_user_func_array(
			array($this, 'console'),
			$args
		);
	}

	public function console_failure($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->red()->bold();
		$args = func_get_args();
		$args[0] = $message;
		call_user_func_array(
			array($this, 'console'),
			$args
		);
	}

	public function console_warn($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->yellow()->bold();
		$args = func_get_args();
		$args[0] = $message;
		call_user_func_array(
			array($this, 'console'),
			$args
		);
	}

	public function console_info($message)
	{
		$color = new Colors\Color();
		$message = $color($message)->cyan()->bold();
		$args = func_get_args();
		$args[0] = $message;
		call_user_func_array(
			array($this, 'console'),
			$args
		);
	}

	public function trace_sleep($seconds)
	{
		if (!$this->trace_enabled) 
		{
			sleep($seconds);
			return;
		}
		
		$start = microtime(true);
		$end = $start + $seconds;
		$counter = 0;

		while (microtime(true) < $end)
		{
			if ($counter++ % 60 === 0)
			{
				if ($counter > 1) $this->flush(PHP_EOL);
				$time = Date::utc()->format(Date::FORMAT_LOG);
				$message = sprintf('[%s] ', $time);
				$this->flush($message);
			}

			$this->flush('.');
			usleep(100000);
		}

		$this->flush(PHP_EOL);
	}

	public function flush($message = null)
	{
		echo $message;
		$this->__flush();
	}

	public function error($text)
	{
		if ($this->background_logger)
		{
			$this->flush($text);
		}
		else
		{
			$this->__flush();
			$handle = fopen('php://stderr', 'a');
			fwrite($handle, $text);
			fwrite($handle, PHP_EOL);
			fclose($handle);
			flush();
		}		
	}

	protected function __flush()
	{
		if ($this->background_logger)
		{
			$output = $this->output->get_output();
			$this->output->set_output(null);
			$this->log($output);
		}
		else
		{
			while (ob_get_level())
				ob_end_flush();
			flush();
			ob_start();
		}
	}

	protected function process_count()
	{
		$sections = array_slice($this->controller_uri_parts, 0, -1);
		$sections = array_map(function($section) {
			return preg_replace('#[^a-z0-9_]#i', '.', $section);
		}, $sections);

		$sections = implode('(\\s+)', $sections);
		$command = 'ps -eo command | grep -P \'^php(-cli)?(\\s+)%s((\\s+)-\\S*)*(\\s+)%s\' | wc -l';
		$command = sprintf($command, $this->conf('cli_php_file'), $sections);
		$count = (int) trim(shell_exec($command));
		return $count;
	}

	public function switches()
	{
		$switches = new stdClass();
		$__switches = $this->uri->_get_cli_switches();

		foreach ($__switches as $switch)
		{
			preg_match('#^\-+([a-z0-9\-_]+)(=(.*))?$#i', $switch, $match);
			$match[1] = str_replace('-', '_', $match[1]);
			$switches->{$match[1]} = isset($match[2]) 
				? $match[3] 
				: true;
		}

		return $switches;
	}

	protected function __on_execution_end()
	{
		parent::__on_execution_end();

		if ($this->background_logger)
		{
			$this->log($this->output->get_output());
			$this->output->set_output(null);
		}
	}
	
}