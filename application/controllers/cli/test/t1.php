<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class T1_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index()
	{
		set_time_limit(3600);

		$number = 1;
		$api = PRNewswire_API_Client_Factory::create();
		// var_dump($api->allowed_distro());
		// return;

		// ---------------------------------------------------------------
		// ---------------------------------------------------------------
		// ---------------------------------------------------------------

		######### Web only, no image ##############
		$m_content = Model_Content::find_slug('prnewswire-api-test-1-4425473');
		$release = PRNewswire_Release::from_content($m_content);
		$this->trace_info(sprintf('Test Number: C%d', $number));
		$this->trace_info(sprintf('Release Title: %s', $release->Headline));
		try { $this->trace_success(sprintf('Release Reference Number: %d', $api->submit($release))); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
			file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		catch (Exception $e) { $this->trace_failure(sprintf('Error: %s', $e->getMessage())); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
			file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		$number++;

		// // ######### State distro, no image ##############
		$m_content = Model_Content::find_slug('prnewswire-api-test-1-4425473');
		$release = PRNewswire_Release::from_content($m_content);
		$this->trace_info(sprintf('Test Number: C%d', $number));
		$this->trace_info(sprintf('Release Title: %s', $release->Headline));
		$release->Distributions = array('ALA');
		try { $this->trace_success(sprintf('Release Reference Number: %d', $api->submit($release))); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
				file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		catch (Exception $e) { $this->trace_failure(sprintf('Error: %s', $e->getMessage())); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
				file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		$number++;

		// // ---------------------------------------------------------------
		// // ---------------------------------------------------------------
		// // ---------------------------------------------------------------

		// // ######### National distro, no image ##############
		$m_content = Model_Content::find_slug('prnewswire-api-test-1-4425473');
		$release = PRNewswire_Release::from_content($m_content);
		$this->trace_info(sprintf('Test Number: C%d', $number));
		$this->trace_info(sprintf('Release Title: %s', $release->Headline));
		$release->Distributions = array('US1');
		try { $this->trace_success(sprintf('Release Reference Number: %d', $api->submit($release))); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
				file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		catch (Exception $e) { $this->trace_failure(sprintf('Error: %s', $e->getMessage())); file_put_contents("/home/www/files/test_{$number}_raw_request.txt", $api->_request->data);
				file_put_contents("/home/www/files/test_{$number}_raw_response.txt", $api->_response->body); sleep(1); }
		$number++;
	}

}