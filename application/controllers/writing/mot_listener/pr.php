<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('writing/mot_listener/main');

class PR_Controller extends Main_Controller {
	
	public function save()
	{
		$writing_order_id = $this->iella_in->writing_order_id;
		$pr_title = $this->iella_in->pr_title;
		$pr_summary = $this->iella_in->pr_summary;
		$pr_content = $this->vd->pure($this->iella_in->pr_content);
		$company_details = $this->iella_in->company_details;
		$writer_name = $this->iella_in->writer_name;
		
		$supporting_quote = $this->iella_in->supporting_quote;
		$supporting_quote_name = $this->iella_in->supporting_quote_name;
		$supporting_quote_title = $this->iella_in->supporting_quote_title;
		
		if ( ! $writing_order_id ||  ! $pr_title ||  ! $pr_summary ||   ! $pr_content)
		{
			$this->iella_out->errors[] = 'one or more required input param(s) missing';
			$this->iella_out->success = false;
			return;
		}

		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$m_content = Model_Content::find($m_order->content_id);
		$m_content->title = $pr_title;
		$m_content->title_to_slug();
		$m_content->save();
		
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_content_data->summary = $pr_summary;
		$m_content_data->content = $pr_content;
		$m_content_data->supporting_quote = $supporting_quote;
		$m_content_data->supporting_quote_name = $supporting_quote_name;
		$m_content_data->supporting_quote_title = $supporting_quote_title;
		$m_content_data->save();
		
		if ($m_order_code->reseller_id)
		{
			$m_comp_profile = Model_Company_Profile::find($m_content->company_id);
			$m_comp_profile->summary = $company_details;
			$m_comp_profile->save();
		}
		
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->status = Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER;
		$m_order->save();
		
		$this->save_writing_process($m_order->id,
			Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER,
			Model_Writing_Process::ACTOR_WRITER);
		
		$reseller_id = $m_order_code->reseller_id;
		$m_reseller_details = Model_Reseller_Details::find($reseller_id);
		$m_company = Model_Company::find($m_content->company_id);
		$reseller = Model_User::find($reseller_id);
		$wo_code = $m_order_code->writing_order_code;	
		$preview_link = "writing/draft/review/{$m_order->id}/{$wo_code}";
		$preview_link = $this->website_url($preview_link);
		
		if (!$m_reseller_details || $m_reseller_details->editing_privilege == 'admin_editor')
		{			
			$en = new Email_Notification();
			$writing_admin = Model_User::find($this->conf('writing_admin_user'));
						
			if (Model_Writing_Process::how_many_times_rejected($m_order->id)) 
			     $en->set_content_view('admin/admin_writer_revised_pr');
			else $en->set_content_view('admin/admin_writer_wrote_pr');
				
			$en->set_data('writer_name', $writer_name);
			$en->set_data('pr_title', $pr_title);
			$en->set_data('reseller', $reseller);
			$en->set_data('customer_contact_name', $m_order_code->customer_name);
			$en->set_data('customer_contact_email', $m_order_code->customer_email);
			$en->set_data('customer_company_name', $m_company->name);
			$en->set_data('preview_link', $preview_link);
			$en->send($writing_admin);
		}	
				
		else if ($m_reseller_details->editing_privilege == 'reseller_editor')
		{
			$en = new Email_Notification();
			if (Model_Writing_Process::how_many_times_rejected($m_order->id)) 
			     $en->set_content_view('reseller/reseller_writer_revised_pr');
			else $en->set_content_view('reseller/reseller_writer_wrote_pr');
			
			$en->set_data('writer_name', $writer_name);
			$en->set_data('pr_title', $pr_title);
			$en->set_data('preview_link', $preview_link);
			$en->set_data('customer_contact_name', $m_order_code->customer_name);
			$en->set_data('customer_contact_email', $m_order_code->customer_email);
			$en->set_data('customer_company_name', $m_company->name);
			$en->set_data('preview_link', $preview_link);
			$en->send($reseller);
		}
		else if ($m_reseller_details->editing_privilege == 'directly_queue_draft')
		{
			$m_content->is_draft = 0;
			$m_content->is_under_review = 0;
			$m_content->is_under_writing = 0;
			$m_content->save();
			
			$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
			$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED;
			$m_order->save();
			
			$this->save_writing_process($$m_order->id,
				Model_Writing_Order::STATUS_SENT_TO_CUSTOMER,
				Model_Writing_Process::ACTOR_WRITER);
			
			$this->save_writing_process($$m_order->id,
				Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED,
				Model_Writing_Process::ACTOR_WRITER);
		}
		
		$this->iella_out->success = true;
	}
	
	public function find()
	{
		$writing_order_code = $this->iella_in->writing_order_code;
		$ci =& get_instance();
		
		$criteria = array();
		$criteria[] = array('writing_order_code', $writing_order_code);
		$m_order_code = Model_Writing_Order_Code::find($criteria);		
		
		$criteria = array();
		$criteria[] = array('writing_order_code_id', $m_order_code->id);
		$m_order = Model_Writing_Order::find($criteria);
				
		if ( ! $m_order)
		{
			$this->iella_out->errors[] = 'Task does not exist';
			$this->iella_out->success = false;
			$this->send();
			return;
		}
		
		$wp = new Model_Writing_Process();
		$writing_order_id = $m_order->id;

		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_order->writing_angle_desc = Model_Writing_Order::full_angle_name($m_order->writing_angle);
		$m_content = Model_Content::find($m_order->content_id);

		$images = $m_content->get_images();
		foreach ($images as $image)
		{
			$im_variant = $image->variant('view-web');
			$im_variant_file = $im_variant->filename;
			$im_variant_url = Stored_Image::url_from_filename($im_variant_file);
			$image->thumb_url = $ci->website_url($im_variant_url);

			$a_variant = $image->variant('original');
			$a_variant_file = $a_variant->filename;
			$a_variant_url = Stored_Image::url_from_filename($a_variant_file);
			$image->url = $ci->website_url($a_variant_url);

			$meta = json_decode($im_variant->meta_data);
		}

		$m_content->images = $images;

		$tags_array = $m_content->get_tags();
		$m_content->tags = implode(", ", $tags_array);
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_comp_profile = Model_Company_Profile::find(array('company_id', $m_content->company_id));
		$m_pb_pr = Model_PB_PR::find(array("content_id", $m_content->id));

		if (!empty($m_pb_pr->stored_file_id_1))
		{
			$file_1 = Stored_file::load_data_from_db($m_pb_pr->stored_file_id_1);
			$file_url_1 = Stored_file::url_from_filename($file_1->filename);
			$m_pb_pr->stored_file_url_1 = $ci->website_url($file_url_1);
		}

		if (!empty($m_pb_pr->stored_file_id_2))
		{
			$file_2 = Stored_file::load_data_from_db($m_pb_pr->stored_file_id_2);
			$file_url_2 = Stored_file::url_from_filename($file_2->filename);
			$m_pb_pr->stored_file_url_2 = $ci->website_url($file_url_2);
		}

		$m_content->pb_pr = $m_pb_pr;

		$m_content->category_name = "BOILERPLATE";

		$beat_names = array();
		$beats = $m_content->get_beats();
		foreach ($beats as $i => $beat)
			$beat_names[] = $beat->name;

		$cat_name = implode(", ", $beat_names);
		$m_content->category_name = $cat_name;

		$this->iella_out->company = Model_Company::find(array('id', $m_content->company_id));
		$this->iella_out->pre_writing_conversation = $wp->get_pre_writing_conversation_with_writer($m_order->id);
		$this->iella_out->reseller_rejection_messages = $wp->get_reseller_rejection_messages($m_order->id);		
		$this->iella_out->order = $m_order;
		$this->iella_out->order->writing_order_code = $writing_order_code;
		$this->iella_out->content = $m_content;
		$this->iella_out->content_data = $m_content_data;
		$this->iella_out->company_profile = $m_comp_profile;
		$this->iella_out->is_company_locked = !$m_order_code->reseller_id;
		
		$conf=new stdClass();
		$conf->title_max_length = $this->conf('title_max_length');
		$conf->summary_max_length = $this->conf('summary_max_length');
		$conf->press_release_min_words = $this->conf('press_release_min_words');
		$conf->press_release_max_length = $this->conf('press_release_max_length');
		$conf->press_release_links_premium = $this->conf('press_release_links_premium');
		$this->iella_out->conf = $conf;
		$this->iella_out->success = true;
		
		$this->send();	
	}
	
	public function save_as_draft()
	{		
		$writing_order_id = $this->iella_in->writing_order_id;
		$pr_title = $this->iella_in->pr_title;
		$pr_summary = $this->iella_in->pr_summary;
		$pr_content = $this->iella_in->pr_content;
		$company_details = $this->iella_in->company_details;
		$writer_name = $this->iella_in->writer_name;
		
		$supporting_quote = $this->iella_in->supporting_quote;
		$supporting_quote_name = $this->iella_in->supporting_quote_name;
		$supporting_quote_title = $this->iella_in->supporting_quote_title;
		
		if ( ! $writing_order_id)
		{
			$this->iella_out->errors[] = 'one or more required input param(s) missing';
			$this->iella_out->success = false;
			return;
		}	
				
		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$m_content = Model_Content::find($m_order->content_id);
		$m_content->title = $pr_title;
		$m_content->title_to_slug();
		$m_content->save();
		
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_content_data->summary = $pr_summary;
		$m_content_data->content = $pr_content;
		$m_content_data->supporting_quote = $supporting_quote;
		$m_content_data->supporting_quote_name = $supporting_quote_name;
		$m_content_data->supporting_quote_title = $supporting_quote_title;
		
		$m_content_data->save();
		
		if ($m_order_code->reseller_id)
		{
			$m_comp_profile = Model_Company_Profile::find($m_content->company_id);
			$m_comp_profile->summary = $company_details;
			$m_comp_profile->save();
		}		
		$this->iella_out->success = true;
	
	}
	
	public function get_pre_writing_conversation()
	{
		$writing_order_id = $this->iella_in->writing_order_id;
		$this->iella_out->conversation = Model_Writing_Process::get_pre_writing_conversation($writing_order_id);
		$this->iella_out->success = true;
		$this->send();
	}
	
	public function writer_request_details_revision()
	{
		$writing_order_id = $this->iella_in->writing_order_id;
		$comments = $this->iella_in->comments;
		$writer_name = $this->iella_in->writer_name;
		
		if (!$writing_order_id)
		{
			$this->iella_out->errors[] = 'one or more required input param(s) missing';
			$this->iella_out->success = false;
			return;
		}
		
		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->status = 'writer_request_details_revision';
		$m_order->save();
		
		$m_writing_process = new Model_Writing_Process();
		$m_writing_process->writing_order_id = $writing_order_id;
		$m_writing_process->process = 'writer_request_details_revision';
		$m_writing_process->actor = 'writer';
		$m_writing_process->comments = $comments;
		$m_writing_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_writing_process->save();
		
		$this->iella_out->success = true;
		
		$m_content = Model_Content::find($m_order->content_id);
		$m_company = Model_Company::find($m_content->company_id);
		
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$reseller_id = $m_order_code->reseller_id;
		
		
		$writing_angle_desc = Model_Writing_Order::full_angle_name($m_order->writing_angle);
		$m_reseller_details = Model_Reseller_Details::find($reseller_id);
		$reseller = Model_User::find($reseller_id);
			
		$en = new Email_Notification();
		$en->set_data('writer_name', $writer_name);
		$en->set_data('pr_angle', $writing_angle_desc);
		$en->set_data('customer_company_name', $m_company->name);
		$en->set_data('customer_contact_name', $m_order_code->customer_name);
		$en->set_data('customer_contact_email', $m_order_code->customer_email);
		$en->set_data('comments', $comments);
		$en->set_data('reseller', $reseller);
			
		if ( ! $m_reseller_details || $m_reseller_details->editing_privilege == 'admin_editor')
		{	
			$writing_admin = Model_User::find($this->conf('writing_admin_user'));
			$en->set_content_view('admin/admin_writer_requests_details_revision');
			$this->iella_out->writing_admin = $writing_admin;
			// $en->send($writing_admin);
		}
		else if ($m_reseller_details->editing_privilege == 'reseller_editor')
		{
			$en->set_content_view('reseller/reseller_writer_requests_details_revision');
			$en->send($reseller);
		}
		
	}
}

?>

