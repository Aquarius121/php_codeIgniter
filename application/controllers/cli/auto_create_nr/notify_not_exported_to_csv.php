<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/nr_builder_trait');

class Notify_Not_Exported_To_CSV_Controller extends CLI_Base {
	
	use NR_Builder_Trait;

	public function index()
	{
		$sources = Model_Company::scraping_sources();
		$action_count = 0;
		$results = array();

		foreach ($sources as $source)
		{
			$ab_not_exported_count = $this->auto_built_not_exported_counter($source);
			$claim_count = $this->claim_submissions_counter($source);
			$verified_count = $this->verified_submissions_counter($source);

			if ($ab_not_exported_count || $claim_count || $verified_count)
			{
				$result = new stdClass();
				$result->source = $source;
				$result->ab_not_exported_count = $ab_not_exported_count;
				$result->claim_count = $claim_count;
				$result->verified_count = $verified_count;
				$results[] = $result;
			}	
		}

		if (!count($results))
			return false;

		$email = new Email();
		
		$email->set_to_email('mikesantiago@gmail.com');
		$email->add_cc('doctorconceptual@gmail.com');
		$email->set_from_email($this->conf('email_address'));
		$email->set_subject("Newsroom Builder Not Yet Exported Data Issue");
				
		$this->vd->results = $results;
		$email_message = $this->load->view('email/nr_builder_not_exported_data', null, true);

		$email->set_message($email_message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);		
	}
}

?>