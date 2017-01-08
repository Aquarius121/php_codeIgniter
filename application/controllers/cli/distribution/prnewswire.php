<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class PRNewswire_Controller extends CLI_Base {
	
	protected $trace_enabled = false;
	protected $trace_time = false;

	public function auto()
	{
		set_time_limit(3600);

		// allow at most 1 process
		if ($this->process_count() > 1)
			return;

		$provider = Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE;
		$provider = escape_and_quote($provider);

		$sql = "SELECT c.*, 
			{{ pd.* AS prn USING Model_PRN_Distribution }}
			FROM nr_content c 
			INNER JOIN nr_content_release_plus crp
			  ON crp.content_id = c.id 
			  AND crp.provider = {$provider}
			  AND crp.is_confirmed = 1
			  AND c.is_approved = 1
			  AND c.is_draft = 0
			  AND c.date_publish < DATE_ADD(UTC_TIMESTAMP(), INTERVAL 24 HOUR)
			LEFT JOIN nr_prn_distribution pd 
			  ON pd.content_id = c.id
			WHERE  (pd.is_submitted IS NULL OR pd.is_submitted = 0)
				AND (pd.is_blocked   IS NULL OR pd.is_blocked = 0)
			ORDER BY c.date_publish ASC
			LIMIT 1";

		while (true)
		{
			$m_content = Model_Content::from_sql($sql);
			if (!$m_content) return;
			
			if (!($prn = $m_content->prn))
			{
				$prn = new Model_PRN_Distribution();
				$prn->content_id = $m_content->id;
			}

			$this->submit($m_content, $prn);
			sleep(1);
		}
	}

	public function manual($id)
	{
		if (!$id) return;
		$m_content = Model_Content::find($id);
		$prn = Model_PRN_Distribution::find($m_content->id);
		
		if (!$prn) 
		{
			$prn = new Model_PRN_Distribution();
			$prn->content_id = $m_content->id;
		}

		$this->console_info('Title: %s', $m_content->title);
		if ($prn->is_submitted)
			$this->flush_warn('Status: Submitted');
		$this->flush('  Confirm submit (Y/N): ');

		$handle = fopen('php://stdin', 'r');
		$confirm = fgetc($handle);
		fclose($handle);

		if (strtoupper($confirm) === 'Y')
			$this->submit($m_content, $prn);
	}

	protected function submit(Model_Content $m_content, Model_PRN_Distribution $prn)
	{
		$api = PRNewswire_API_Client_Factory::create();
		$release = PRNewswire_Release::from_content($m_content);
		$prn->date_submit = Date::utc();
		
		try 
		{ 
			$result = $api->submit($release);
			if (!$result) throw new PRNewswire_API_Exception();
			$this->trace_success($m_content->id, $result);
			$rd = $prn->raw_data_object();
			$rd->api = $this->conf('prnewswire')['api_label'];
			$rd->release_number = $result;
			$prn->raw_data($rd);
			$prn->is_submitted = 1;
			$prn->save();
		}
		catch (Exception $ex)
		{
			$this->trace_failure($m_content->id, $ex->getMessage());
			$rd = $prn->raw_data_object();
			$rd->api = $this->conf('prnewswire')['api_label'];
			if (!$rd->errors) $rd->errors = array();
			$error = new Raw_Data();
			$error->date = (string) Date::utc();
			$error->message = $ex->getMessage();
			$error->request = $api->_request;
			$rd->errors[] = $error;
			$prn->raw_data($rd);
			$prn->is_blocked = 1;
			$prn->save();
		}
	}

}
