<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/insights/query');
load_controller('shared/process_results_image_variant_trait');
load_controller('shared/process_results_company_profile_trait');
load_controller('shared/process_results_company_name_trait');

class Alert_Controller extends Query_Controller {

	public function create()
	{
		$params = (array) $this->input->post('params');
		$params = Raw_Data::from_array($params);
		$this->transform_params($params);
		
		$m_alert = Model_Insights_Alert::create();
		$m_alert->user_id = Auth::user()->id;
		$m_alert->email = Auth::user()->email;
		$m_alert->raw_data_write('params', $params);
		$m_alert->save();

		$message = 'The alert was created successfully.';
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_html($message);
		$object = $feedback->alert_object();
		$this->json($object);
	}

}