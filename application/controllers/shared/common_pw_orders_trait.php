<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Common_PW_Orders_Trait {
	
	protected function add_order_detail_modal()
	{
		$pw_detail_modal = new Modal();
		$pw_detail_modal->set_title('Pitch Wizard Order Detail');
		$this->add_eob($pw_detail_modal->render(450, 430));
		$this->vd->pw_detail_modal_id = $pw_detail_modal->id;
	}
	
	protected function order_detail_modal($pitch_order_id)
	{
		$m_pitch_order = Model_Pitch_Order::find($pitch_order_id);
		$this->vd->m_pitch_order = $m_pitch_order;
		$m_campaign = Model_Campaign::find($m_pitch_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$m_company = Model_Company::find($m_content->company_id);
		$m_user = Model_User::find($m_company->user_id);
		$m_c_profile = Model_Company_Profile::find($m_content->company_id);
		$this->vd->m_state = Model_State::find($m_pitch_order->state_id);
		$this->vd->m_content = $m_content;
		$this->vd->m_beat_1 = Model_Beat::find($m_pitch_order->beat_1_id);
		$this->vd->m_company = $m_company;
		$this->vd->m_user = $m_user;
		$this->vd->m_c_profile = $m_c_profile;
		$this->vd->m_pitch_order = $m_pitch_order;
		if ($m_pitch_order->beat_2_id)
			$this->vd->m_beat_2 = Model_Beat::find($m_pitch_order->beat_2_id);			
		$this->load->view('manage/contact/pitch/partials/modal_pitch_order_detail');
	}
}

?>