<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/base');

class Branding_Controller extends Reseller_Base {
	
	public $title = 'Branding Details';
	
	public function index()
	{
		$user = $this->vd->user = Auth::user();
		$reseller_details = Model_Reseller_Details::find($user->id);

		if (!$reseller_details)
		{
			$reseller_details = new Model_Reseller_Details();
			$reseller_details->user_id = $user->id;
			$reseller_details->save();
		}

		if ($this->input->post('save'))
		{
			$reseller_details = Model_Reseller_Details::find($user->id);
			$reseller_details->company_name = $this->input->post('company_name');
			$reseller_details->website = $this->input->post('website');
			$reseller_details->business_paypal = $this->input->post('business_paypal');
			$reseller_details->logo_image_id = value_or_null($this->input->post('logo_image_id'));
			$reseller_details->save();

			// load feedback message for the user
			$feedback_view = 'reseller/account/partials/save_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
		}
		
		$this->vd->reseller_details = $reseller_details;
		$this->vd->logo_image_id = $reseller_details->logo_image_id;

		$this->load->view('reseller/header');	
		$this->load->view('reseller/account/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('reseller/account/branding');
		$this->load->view('reseller/post-content');
		$this->load->view('reseller/footer');
	}	

}

?>
