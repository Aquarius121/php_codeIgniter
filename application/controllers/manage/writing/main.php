<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/upgrade');

class Main_Controller extends Manage_Base { 

	use Upgrade_Trait;

	const LISTING_SIZE = 10;

	protected $steps = array(
		1 => 'step_1', // company details
		2 => 'step_2', // release details
		3 => 'step_3', // attach media
		4 => 'step_4', // preview and confirm
	);

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Distribution';
		$this->vd->title[] = 'Press Release Writing';
	}
	
	public function index()
	{
		$this->redirect('manage/publish');
	}
	
	public function is_editable($status)
	{
		return Model_Writing_Session::is_editable($status) || Auth::is_admin_online();
	}
	
	public function is_preview_available($status)
	{
		return Model_Writing_Session::is_preview_available($status);
	}
	
	public function process_ordered()
	{
		$this->redirect('manage/writing/process');
	}

	protected function approve_sub($wr_session_id = null)
	{
		$m_wr_session = Model_Writing_Session::find($wr_session_id);
		if ($this->newsroom->company_id != $m_wr_session->company_id)
			$this->denied();
		
		$m_wr_session->is_archived = 1;
		$m_wr_session->save();

		$m_content = Model_Content::find($m_wr_session->content_id);
		$m_content->is_draft = 0;
		$m_content->is_under_review = 0;
		$m_content->is_under_writing = 0;
		$dt_publish = Date::in($this->input->post('date_publish'));
		$m_content->date_publish = $dt_publish;
		$m_content->save();
		
		$m_order = Model_Writing_Order::find($m_wr_session->writing_order_id);
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		Model_Writing_Process::create_and_save($m_order->id,
			Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED,
			Model_Writing_Process::ACTOR_CUSTOMER);

		return get_defined_vars();
	}
	
	public function approve($wr_session_id = null)
	{
		extract($this->approve_sub($wr_session_id));

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The content has been approved.');
		$this->add_feedback($feedback);
		$this->redirect($m_content->url());
	}

	public function approve_edit($wr_session_id = null)
	{
		extract($this->approve_sub($wr_session_id));

		// save as draft for now
		$m_content->is_draft = 1;
		$m_content->save();
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The content can now be edited.');
		$this->add_feedback($feedback);

		// redirect to edit page for the specified content type
		$edit_url = "manage/publish/{$m_content->type}/edit/{$m_content->id}";
		$this->redirect($edit_url);
	}
	
	public function reject($wr_session_id = null)
	{
		$m_wr_session = Model_Writing_Session::find($wr_session_id);
		if ($this->newsroom->company_id != $m_wr_session->company_id)
			$this->denied();
		
		$m_order = Model_Writing_Order::find($m_wr_session->writing_order_id);
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_REJECTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		Model_Writing_Process::create_and_save($m_order->id,
			Model_Writing_Order::STATUS_CUSTOMER_REJECTED,
			Model_Writing_Process::ACTOR_CUSTOMER,
			$this->input->post('comments'));
		
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$m_content = Model_Content::find($m_order->content_id);
		$m_company = Model_Company::find($m_content->company_id);
		$user_for_contact = Model_User::find($this->conf('writing_admin_user'));
		
		$wo_code = $m_order_code->writing_order_code;
		$preview_link = "writing/draft/review/{$m_order->id}/{$wo_code}";
		$preview_link = $this->website_url($preview_link);
		
		$en = new Email_Notification();
		$en->set_content_view('writing/draft_rejected_by_customer');
		$en->set_data('reseller', null);
		$en->set_data('pr_title', $m_content->title);
		$en->set_data('customer_contact_name', $m_order_code->customer_name);
		$en->set_data('customer_contact_email', $m_order_code->customer_email);
		$en->set_data('customer_company_name', $m_company->name);
		$en->set_data('preview_link', $preview_link);
		$en->set_data('comments', $this->input->post('comments'));
		$en->send($user_for_contact);	
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The editor has been notified.');
		$this->add_feedback($feedback);
		$this->redirect('manage/writing');
	}
	
	public function process($wr_session_id = null, $step = 1, $is_review = false)
	{
		if (!($m_wr_session = Model_Writing_Session::find($wr_session_id)))
		{
			$user = Auth::user();
			if (!$user->writing_credits())
				return $this->order_credit();
			
			$user->consume_writing_credits(1);
			$m_wr_session = Model_Writing_Session::create();
			$m_wr_session->company_id = $this->newsroom->company_id;
			$m_wr_session->save();
			
			$url = "manage/writing/process/{$m_wr_session->id}/1";
			$this->redirect($url);
		}
		
		if ($m_wr_session->is_archived)
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Archived!');
			$feedback->set_text('The order has been archived and cannot be edited.');
			
			if ($is_review) 
			{
				$this->use_feedback($feedback);
			}
			else
			{
				$this->add_feedback($feedback);
				$this->redirect('manage/writing');
			}
		}
		
		if ($m_wr_session->company_id != $this->newsroom->company_id)
			$this->denied();
		
		$this->vd->is_review = $is_review;
		$this->vd->m_wr_session = $m_wr_session;
		$this->vd->wr_raw_data = $m_wr_session->raw_data();
		if (!$this->vd->wr_raw_data)
			$this->vd->wr_raw_data = new stdClass();
		
		if (!($m_wr_order_code = Model_Writing_Order_Code::find($m_wr_session->writing_order_code_id)))
		{
			$m_wr_order_code = new Model_Writing_Order_Code();
			$m_wr_order_code->writing_order_code = $m_wr_session->id_to_code();
			$m_wr_order_code->customer_name = Auth::user()->name();
			$m_wr_order_code->customer_email = Auth::user()->email;
			$m_wr_order_code->date_ordered = Date::$now->format(Date::FORMAT_MYSQL);
			$m_wr_order_code->reseller_id = null;
			$m_wr_order_code->is_used = 0;
			$m_wr_order_code->save();
			
			$m_wr_session->writing_order_code_id = $m_wr_order_code->id;
			$m_wr_session->save();
		}
		
		if (!($m_profile = Model_Company_Profile::find($this->newsroom->company_id))) 
		{
			$m_profile = new Model_Company_Profile();
			$m_profile->company_id = $this->newsroom->company_id;
		}
		
		if (!($m_custom = Model_Newsroom_Custom::find($this->newsroom->company_id))) 
		{
			$m_custom = new Model_Newsroom_Custom();
			$m_custom->company_id = $this->newsroom->company_id;
		}
		
		if (!($m_content = Model_Content::find($m_wr_session->content_id)))
		{
			$m_content = new Model_Content();
			$m_content->company_id = $this->newsroom->company_id;
			$m_content->type = Model_Content::TYPE_PR;
			$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->date_publish = $m_content->date_created;
			$m_content->is_under_writing = 1;
			$m_content->is_premium = 1;
			$m_content->is_draft = 1;
			$m_content->save();
			
			$m_wr_session->content_id = $m_content->id;
			$m_wr_session->save();
		}
		
		if (!($m_content_data = Model_Content_Data::find($m_content->id)))
		{
			$m_content_data = new Model_Content_Data();
			$m_content_data->content_id = $m_content->id;
			$m_content_data->save();
		}
		
		if (!($m_pb_pr = Model_PB_PR::find($m_content->id)))
		{
			$m_pb_pr = new Model_PB_PR();
			$m_pb_pr->content_id = $m_content->id;
			$m_pb_pr->save();
		}
		
		$this->vd->m_wr_order = null;
		$this->vd->can_submit = 
			!$m_wr_session->is_archived
			&& !$this->vd->is_review 
			&& !empty($this->vd->wr_raw_data->writing_angle)
			&& !empty($this->vd->wr_raw_data->angle_detail)
			&& !empty($this->vd->wr_raw_data->primary_keyword);
		
		if ($m_wr_order = Model_Writing_Order::find($m_wr_session->writing_order_id))
		{
			$this->vd->m_wr_order = $m_wr_order;
			$is_editable = $this->is_editable($m_wr_order->status);
			$this->vd->can_submit = $this->vd->can_submit
				&& $is_editable;
			
			if (!$is_editable && !$m_wr_session->is_archived)
			{
				$feedback = new Feedback('warning');
				$feedback->set_title('Locked!');
				$feedback->set_text('Order details have been sent to the writer.');
				$feedback->add_text('You cannot submit further details at this time.');
				$feedback->add_text('The company information and press release media can still be edited.', true);
				$this->use_feedback($feedback);
			}
		}
		
		$m_content->load_content_data();
		$m_content->load_local_data();
		
		$this->vd->m_content = $m_content;
		$this->vd->m_profile = $m_profile;
		$this->vd->m_custom = $m_custom;
		$this->vd->m_content_data = $m_content_data;
		$this->vd->m_pb_pr = $m_pb_pr;
		$this->vd->m_wr_order_code = $m_wr_order_code;
		
		$step = (int) $step;
		$step = $this->steps[$step];
		$this->$step($m_wr_session);
	}
	
	protected function order_credit()
	{
		$this->redirect('manage/writing/sales');
	}
	
	protected function step_1()
	{		
		if ($this->input->post('is_continue'))
		{
			$m_profile = $this->vd->m_profile;
			$m_custom = $this->vd->m_custom;
			$m_wr_session = $this->vd->m_wr_session;
			
			$m_profile->values($this->input->post());
			$m_profile->company_id = $this->newsroom->company_id;
			$m_profile->save();
			
			$m_custom->logo_image_id = $this->input->post('logo_image_id');
			$m_custom->save();
			
			// continue to step 2
			$url = "manage/writing/process/{$m_wr_session->id}/2";
			$this->redirect($url);
		}
		
		$order = array('name', 'asc');
		$criteria = array('is_common', 1);
		$this->vd->common_countries = Model_Country::find_all($criteria, $order);
		$this->vd->countries = Model_Country::find_all(null, $order);
		
		$this->vd->step = 1;
		$this->load->view('manage/header');
		$this->load->view('manage/writing/step_1');
		$this->load->view('manage/footer');
	}
	
	protected function step_2()
	{
		if ($this->input->post('is_continue'))
		{
			$beats = array();
			if ($beats = $this->input->post('beats'))
				$beats = $beats;
			
			$tags = explode(chr(44), $this->input->post('tags'));
			$m_content = $this->vd->m_content;
			$m_content->set_beats($beats);
			$m_content->set_tags($tags);
			
			$m_wr_session = $this->vd->m_wr_session;
			$wr_raw_data = $this->vd->wr_raw_data;
			$wr_raw_data->writing_angle = $this->input->post('writing_angle');
			$wr_raw_data->angle_detail = $this->input->post('angle_detail');
			$wr_raw_data->additional_comments = $this->input->post('additional_comments');
			$wr_raw_data->primary_keyword = $this->input->post('primary_keyword');
			$m_wr_session->raw_data($wr_raw_data);
			$m_wr_session->save();
			
			// continue to step 2
			$url = "manage/writing/process/{$m_wr_session->id}/3";
			$this->redirect($url);
		}
		
		$this->vd->beats = Model_Beat::list_all_beats_by_group();
		
		$this->vd->step = 2;
		$this->load->view('manage/header');
		$this->load->view('manage/writing/step_2');
		$this->load->view('manage/footer');
	}
	
	protected function step_3()
	{
		if ($this->input->post('is_continue'))
		{
			$images = array();
			$m_content = $this->vd->m_content;
		
			foreach ((array) $this->input->post('image_ids') as $k => $image_id)
			{
				if (!($image = Model_Image::find(value_or_null($image_id)))) continue;
				$image->meta_data = @$post['image_meta_data'][$k];
				$images[] = $image;
				$image->save();
			}
			
			$m_content->cover_image_id = value_or_null($this->input->post('cover_image_id'));
			$m_content->set_images($images);
			$m_content->save();
			
			$m_content_data = $this->vd->m_content_data;
			$m_content_data->rel_res_pri_title = value_or_null($this->input->post('rel_res_pri_title'));
			$m_content_data->rel_res_pri_link = value_or_null($this->input->post('rel_res_pri_link'));
			$m_content_data->rel_res_sec_title = value_or_null($this->input->post('rel_res_sec_title'));
			$m_content_data->rel_res_sec_link = value_or_null($this->input->post('rel_res_sec_link'));
			$m_content_data->save();
			
			$m_pb_pr = $this->vd->m_pb_pr;
			$m_pb_pr->web_video_provider = value_or_null($this->input->post('web_video_provider'));
			$m_pb_pr->web_video_id = value_or_null($this->input->post('web_video_id'));
			$m_pb_pr->stored_file_id_1 = value_or_null($this->input->post('stored_file_id_1'));
			$m_pb_pr->stored_file_id_2 = value_or_null($this->input->post('stored_file_id_2'));
			$m_pb_pr->stored_file_name_1 = value_or_null($this->input->post('stored_file_name_1'));
			$m_pb_pr->stored_file_name_2 = value_or_null($this->input->post('stored_file_name_2'));
			$m_pb_pr->clean_video();
			$m_pb_pr->clean_files();
			$m_pb_pr->save();		
			
			$m_profile = $this->vd->m_profile;
			$m_profile->values($this->input->post());
			$m_profile->company_id = $this->newsroom->company_id;
			$m_profile->clean_soc();
			$m_profile->save();
			
			// continue to step 2
			$m_wr_session = $this->vd->m_wr_session;
			$url = "manage/writing/process/{$m_wr_session->id}/4";
			$this->redirect($url);
		}
		
		$this->vd->step = 3;
		$this->load->view('manage/header');
		$this->load->view('manage/writing/step_3');
		$this->load->view('manage/footer');
	}
	
	protected function step_4()
	{
		if ($this->input->post('is_continue'))
		{
			$m_wr_order = $this->vd->m_wr_order;
			$m_wr_session = $this->vd->m_wr_session;
			$m_wr_order_code = $this->vd->m_wr_order_code;
			$wr_raw_data = $this->vd->wr_raw_data;
			$date_now_str = Date::$now->format(Date::FORMAT_MYSQL);
			
			if ($m_wr_order && !$this->is_editable($m_wr_order->status))
			{
				// load feedback message for the user
				$feedback = new Feedback('error');
				$feedback->set_title('Error!');
				$feedback->set_text('The order cannot be edited at this time.');
				$this->add_feedback($feedback);
				$this->redirect($this->uri->uri_string);
			}
			
			if (!$m_wr_order)
			{
				$m_wr_order = new Model_Writing_Order();
				$m_wr_order->content_id = $m_wr_session->content_id;
				$m_wr_order->writing_order_code_id = $m_wr_order_code->id;
				$m_wr_order->date_ordered = $date_now_str;
				$m_wr_order->latest_status_date = $date_now_str;
				$m_wr_order->status = Model_Writing_Order::STATUS_NOT_ASSIGNED;
				$m_wr_order->save();
				
				$m_wr_order_code->is_used = 1;
				$m_wr_order_code->save();
				
				$m_wr_session->writing_order_id = $m_wr_order->id;
				$m_wr_session->save();
			}
			
			if ($m_wr_order->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE
			 || $m_wr_order->status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS)
			{
				$m_wr_order->status = Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS;
				$m_wr_order->latest_status_date = $date_now_str;
				
				$m_wr_process = new Model_Writing_Process();
				$m_wr_process->writing_order_id = $m_wr_order->id;
				$m_wr_process->process = Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS;
				$m_wr_process->actor = Model_Writing_Process::ACTOR_CUSTOMER;
				$m_wr_process->process_date = $date_now_str;
				$m_wr_process->comments = value_or_null($this->input->post('reply_to_admin'));
				$m_wr_process->save();
			}				
			
			if (isset($wr_raw_data->editor_comments))
				unset($wr_raw_data->editor_comments);
			$m_wr_session->raw_data($wr_raw_data);
			$m_wr_session->save();
			
			$m_wr_order->writing_angle = $wr_raw_data->writing_angle;
			$m_wr_order->angle_detail = $wr_raw_data->angle_detail;
			$m_wr_order->primary_keyword = $wr_raw_data->primary_keyword;
			$m_wr_order->additional_comments = $wr_raw_data->additional_comments;
			$m_wr_order->save();
			
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('The order has been submitted');
			$feedback->add_text('and will be reviewed soon.');
			$this->add_feedback($feedback);
			$this->redirect('manage/writing');
		}
		
		$this->vd->step = 4;
		$this->load->view('manage/header');
		$this->load->view('manage/writing/step_4');
		$this->load->view('manage/footer');
	}
	
	public function sales()
	{
		$items = $this->locate_credits_for_access_level();	
		$this->vd->writing_credit = $items->writing_credit;
		
		$this->load->view('manage/header');
		$this->load->view('manage/writing/sales');
		$this->load->view('manage/footer');
	}
	
	public function sales_order()
	{
		$cart = Cart::instance();
		$cart->reset();
		
		$items = $this->locate_credits_for_access_level();
		$writing_credit = $items->writing_credit;
		$cart_item = Cart_Item::create($writing_credit);
		if ($this->input->post('add_distribution'))
			$cart_item->attach($items->pr_credit);
		$cart_item->callback = 'manage/writing/process';
		$cart->add_cart_item($cart_item);
		$cart->save();
		
		$this->redirect('manage/order/checkout');
	}
	
}

?>