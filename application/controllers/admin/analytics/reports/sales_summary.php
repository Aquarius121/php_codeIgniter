<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Sales_Summary_Controller extends Admin_Base {

	public function index($offset)
	{
		$dt = Date::months(-$offset);
		$date_start = $dt->format('Y-m-01 00:00:00');
		$date_end = $dt->format('Y-m-t 23:59:59');

		$cli_file = $this->conf('cli_php_file');
		$command = 'php-cli %s cli report sales_receipts %s %s';
		$command = sprintf($command, 
			escapeshellarg($cli_file),
			escapeshellarg($date_start),
			escapeshellarg($date_end));

		ob_clean();
		$this->force_download('sales.csv', MIME::CSV);
		passthru($command);
		die();
	}

}