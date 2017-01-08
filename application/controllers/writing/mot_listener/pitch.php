<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('writing/mot_listener/main');

class Pitch_Controller extends Main_Controller {
	
	public function find()
	{
		$pitch_order_id = $this->iella_in->pitch_order_id;		
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
				
		if ( ! $m_pw_order)
		{
			$this->iella_out->errors[] = 'Task does not exist';
			$this->iella_out->success = false;
			$this->send();
			return;
		}
		
		$beat_1 = Model_Beat::find($m_pw_order->beat_1_id);
		$m_pw_order->beat_1_name = $beat_1->name;
		if ( ! empty($m_pw_order->beat_2_id))
		{
			$beat_2 = Model_Beat::find($m_pw_order->beat_2_id);
			$m_pw_order->beat_2_name = $beat_2->name;
		}
		
		if ( ! empty($m_pw_order->state_id))
		{
			$state = Model_State::find($m_pw_order->state_id);
			$m_pw_order->state_name = $state->name;
		}
		
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$m_content->url = $this->common()->url($m_content->url());
		$tags_array = $m_content->get_tags();
		$m_content->tags = implode(", ", $tags_array);
		
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_comp_profile = Model_Company_Profile::find(array('company_id', $m_content->company_id));
		$m_pb_pr = Model_PB_PR::find(array("content_id", $m_content->id));
		$m_content->category_name = @$m_content->get_beats()[0]->name;
		
		$this->iella_out->m_company = Model_Company::find(array('id', $m_content->company_id));
		$this->iella_out->pre_writing_conversation = Model_Pitch_Writing_Process::
														get_pre_writing_conversation($pitch_order_id);

		$this->iella_out->rejection_messages = Model_Pitch_Writing_Process::
														get_rejection_conversation($pitch_order_id);		
		$this->iella_out->m_pw_order = $m_pw_order;
		$this->iella_out->m_pw_content = $m_pw_content;
		$this->iella_out->m_campaign = $m_campaign;
		$this->iella_out->m_content = $m_content;
		$this->iella_out->m_content_data = $m_content_data;
		$this->iella_out->m_company_profile = $m_comp_profile;
		
		$conf = new stdClass();
		$conf->pitch_subject_max_length = $this->conf('pitch_subject_max_length');
		$conf->pitch_min_words = $this->conf('pitch_min_words');
		$conf->pitch_max_length = $this->conf('pitch_max_length');
		$this->iella_out->conf = $conf;	
			
		$this->iella_out->success = true;		
		$this->send();	
	}	
	
	public function save()
	{		
		$pitch_order_id = $this->iella_in->pitch_order_id;
		$subject = $this->iella_in->subject;
		$pitch_text = $this->vd->pure($this->iella_in->pitch_text);
		$is_draft = $this->iella_in->is_draft;
		$writer_name = $this->iella_in->writer_name;
		
		if ( ! $pitch_order_id)
		{
			$this->iella_out->errors[] = 'one or more required input param(s) missing';
			$this->iella_out->success = false;	
			return;
		}	
				
		if ( ! $m_pw_content = Model_Pitch_Content::find($pitch_order_id))
		{
			$m_pw_content = new Model_Pitch_Content();
			$m_pw_content->pitch_order_id = $pitch_order_id;
		}
		
		$m_pw_content->subject = $subject;
		$m_pw_content->pitch_text = $pitch_text;
		$m_pw_content->date_written = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_pw_content->save();
		
		if ( ! $is_draft)
		{
			$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
			$m_pw_order->status = Model_Pitch_Order::STATUS_WRITTEN_SENT_TO_ADMIN;
			$m_pw_order->save();
			
			$process = Model_Pitch_Writing_Process::PROCESS_WRITTEN_SENT_TO_ADMIN;
			Model_Pitch_Writing_Process::create_and_save($pitch_order_id, $process);
			
			// Send email to admin
			$pw_mailer = new Pitch_Wizard_Mailer();
			$pw_mailer->pitch_written($pitch_order_id, $writer_name);
		}
		
		$this->iella_out->success = true;	
	}
	
	public function writer_request_details_revision()
	{
		$pitch_order_id = $this->iella_in->pitch_order_id;
		$comments = $this->iella_in->comments;
		$writer_name = $this->iella_in->writer_name;
		
		if ( ! $pitch_order_id)
		{
			$this->iella_out->errors[] = 'one or more required input param(s) missing';
			$this->iella_out->success = false;
			return;
		}
		
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION;
		$m_pw_order->save();
		
		$process = Model_Pitch_Writing_Process::PROCESS_WRITER_REQUEST_DETAILS_REVISION;
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, $process, $comments);
		
		$this->iella_out->success = true;
		
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->writer_requests_to_revise_detail($pitch_order_id, $comments, $writer_name);
	}
	
	public function get_pre_writing_conversation()
	{
		$pitch_order_id = $this->iella_in->pitch_order_id;
		$this->iella_out->conversation = Model_Pitch_Writing_Process::get_pre_writing_conversation($pitch_order_id);
		$this->iella_out->success = true;
		$this->send();
	}
}

?>

