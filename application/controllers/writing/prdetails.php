<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_parent_controller('writing/base');

class PRdetails_Controller extends Writing_Base {
	
	public function __construct()
	{
		parent::__construct();
		$this->sess_data = (array) json_decode($this->session->get('prdetails_data'));
		$m_order_code = Model_Writing_Order_Code::find_code(@$this->sess_data['writing_order_code']);
		$this->vd->m_order_code = $m_order_code;
		
		if ($m_order_code)
		{
			if (!$m_order_code->reseller_id) 
			{
				$m_order = Model_Writing_Order::find('writing_order_code_id', $m_order_code->id);
				$m_wr_session = Model_Writing_Session::find_order($m_order->id);
				$m_newsroom = Model_Newsroom::find($m_wr_session->company_id);
				$url = gstring("manage/writing/process/{$m_wr_session->id}");
				$url = $m_newsroom->url($url);
				$this->redirect($url, false);
			}
			
			if ($customer_name = $this->input->post('customer_name'))
				$m_order_code->customer_name = $customer_name;
			if ($customer_email = $this->input->post('customer_email'))
				$m_order_code->customer_email = $customer_email;
			$m_order_code->save();
		}
	}
	
	public function index($code = null)
	{		
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		
		if (!empty($sess_data['writing_order_code']))
			$this->redirect('writing/prdetails/step1');
		
		$this->vd->direct_link_code = $code;			
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/index');
		$this->load->view('writing/post-content');
		$this->load->view('writing/footer');		
	}
	
	public function step1()
	{
		$this->is_code_entered();

		if ($this->input->post('btn_reply'))
		{
			$this->reply_to_admin();
			return;
		}

		$this->vd->countries = Model_Country::find_all(null, 'name');
			
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		
		if(@$sess_data['emailaddress']=="")
		{
			$fields=array("emailaddress", "companyname", "companycontact", "companyweb","address_street","address_apt_suite",
							"address_city","address_state","address_zip","address_phone","address_country_id",
							"companydetails");
			$sess_data= $this->load_empty_field_values($fields);
		}
		
		$this->vd->fields= $sess_data;	
		$this->vd->details_change_comments="";
		if(@$sess_data['detail_change_comments'])
			$this->vd->details_change_comments= $sess_data['detail_change_comments'];		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/step1');
		$this->load->view('writing/post-content');				
		$this->load->view('writing/footer');
	}
	
	public function step2()
	{
		$this->is_code_entered();		
		
		if ($this->input->post('btn_reply'))
		{
			$this->reply_to_admin();
			return;
		}

		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		$post = $this->input->post();
		$suggested_location = null;
		
		if (is_array($post))
		{
			$sess_data = array_merge($sess_data, $post);
			$this->session->set('prdetails_data', json_encode($sess_data));
		}
		
		if (!empty($sess_data['address_city']) && !empty($sess_data['address_state']))
		{
			$city = $sess_data['address_city'];
			$state = $sess_data['address_state'];
			$suggested_location = "{$city}, {$state}";
		}
		else if (!empty($sess_data['address_city']) && !empty($sess_data['address_country_id']))
		{
			$city = $sess_data['address_city'];
			$country = Model_Country::find($sess_data['address_country_id'])->name;
			$suggested_location = "{$city}, {$country}";
		}
		
		if (empty($sess_data['category']))
		{
			$fields = array("category","pr_angle","angledetails","location");
			$sess_data = $this->load_empty_field_values($fields);
		}
		
		$this->vd->fields = $sess_data;
		$this->vd->beats = Model_Beat::list_all_beats_by_group();
		$this->vd->details_change_comments = @$sess_data['detail_change_comments'];
		
		if (empty($this->vd->fields['location']))
			$this->vd->fields['location'] = $suggested_location;

		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/step2');
		$this->load->view('writing/post-content');				
		$this->load->view('writing/footer');		
	}
	
	public function step3()
	{
		$this->is_code_entered();		
		
		if ($this->input->post('btn_reply'))
		{
			$this->reply_to_admin();
			return;
		}

		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		$post= $this->input->post();
		if(is_array($post))
		{
			$sess_data = array_merge($sess_data, $post);
			$this->session->set('prdetails_data', json_encode($sess_data));		
		}		
		if(@$sess_data['primarykeyword']=="") //this is the first time we are on step3, so load empty values in fields.
		{
			$fields=array(	"primarykeyword","tags","link_1","link_text_1","link_2","link_text_2","additional_link_1",
							"additional_link_text_1","additional_link_2","additional_link_text_2","youtube_video",
							"additional_comments");
			$sess_data= $this->load_empty_field_values($fields);
		}		
		$this->vd->fields= $sess_data;		
		
		if(@$sess_data['logo_image_id'])
		{
		   $im = Model_Image::find($sess_data['logo_image_id']);
		   $im_file = $im->variant('header-finger')->filename;
		   $im_url = Stored_Image::url_from_filename($im_file);		
		   $this->vd->fields['logo']= $im_url;
		}
		else
			$this->vd->fields['logo']="";
		for($c=1; $c<=3; $c++)
		{
		   if(@$sess_data['related_image' . $c . '_id'])
		   {
			   $im = Model_Image::find($sess_data['related_image' . $c . '_id']);
			   $im_file = $im->variant('finger')->filename;
			   $im_url = Stored_Image::url_from_filename($im_file);		
			   $this->vd->fields['image' . $c]= $im_url;
			}
			else	
				$this->vd->fields['image' . $c] = "";	
		}	
		$this->vd->details_change_comments="";
		if(@$sess_data['detail_change_comments'])
			$this->vd->details_change_comments= $sess_data['detail_change_comments'];		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/step3');
		$this->load->view('writing/post-content');				
		$this->load->view('writing/footer');
	}
	
	public function step4()
	{
		$this->is_code_entered();
		
		if ($this->input->post('btn_reply'))
		{
			$this->reply_to_admin();
			return;
		}

		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		$post= $this->input->post();
		if(is_array($post))
		{
			$sess_data = array_merge($sess_data, $post);			
			$logo_file = $_FILES['company_logo']['tmp_name'];
			if (Image::is_valid_file($logo_file))
			{
				$logo_image_id = Legacy_Image::import('logo', $_FILES['company_logo']['tmp_name'])->id;
				$sess_data['logo_image_id']= $logo_image_id;				
			}		
			else if($this->input->post('remove_logo')==1)
				$sess_data['logo_image_id']=0;			
			
			for($c=1; $c<=3; $c++)
			{
				$related_image_file = $_FILES['image' . $c]['tmp_name'];
				if (Image::is_valid_file($related_image_file))
				{
					$related_image_id = Legacy_Image::import('related', $_FILES['image' . $c]['tmp_name'])->id;
					$sess_data['related_image' . $c . '_id']= $related_image_id;				
				}		
				else if($this->input->post('remove_image' . $c)==1)
					$sess_data['related_image' . $c . '_id']=0;		
			}				
			$this->session->set('prdetails_data', json_encode($sess_data));
		}			
		$this->vd->fields= $sess_data;	
		$sql="select name from nr_country where id= ?";
		$query = $this->db->query($sql,array($sess_data['address_country_id']));
		$countryRec= $query->result();
		$this->vd->countryName= $countryRec[0]->name;
		$cat = Model_Cat::find(array(array('id', $sess_data['category'])));
		$this->vd->catName= $cat->name;
		$angleTitles=array("problem"=>"Problem / Solution - Introduces a problem and presents the website 
										or product as a solution",
							"discount"=>"Discount Offer or Special Offer Announcement",
							"website"=>"Website or product launch",
							"announcement"=>"Special Company Announcement - i.e. Company Merge, 
											Company Acquisition, Anniversary etc","other"=>"Other");
		$angle= $sess_data['pr_angle'];		
		$this->vd->angleTitle= $angleTitles[$angle]; 
		
		if(@$sess_data['logo_image_id'])
		{
		   $im = Model_Image::find($sess_data['logo_image_id']);
		   $im_file = $im->variant('header-thumb')->filename;
		   $im_url = Stored_Image::url_from_filename($im_file);		
		   $this->vd->fields['logo']= $im_url;
		}
		else
			$this->vd->fields['logo']=0;
		for($c=1; $c<=3; $c++)
		{
		   if(@$sess_data['related_image' . $c . '_id'])
		   {
			   $im = Model_Image::find($sess_data['related_image' . $c . '_id']);
			   $im_file = $im->variant('web')->filename;
			   $im_url = Stored_Image::url_from_filename($im_file);		
			   $this->vd->fields['image' . $c]= $im_url;
			}
			else	
				$this->vd->fields['image' . $c] = "";	
		}	
		
		
		$video = Video::get_instance(Video::PROVIDER_YOUTUBE);
		$video->parse_video_id($sess_data['youtube_video']);		
		$this->vd->videoIframe= $video->render(533,300);
		$this->vd->details_change_comments="";
		if(@$sess_data['detail_change_comments'])
			$this->vd->details_change_comments= $sess_data['detail_change_comments'];		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/step4');
		$this->load->view('writing/post-content');				
		$this->load->view('writing/footer');			
	}	
	
	public function save()
	{	
		$this->is_code_entered();

		if ($this->input->post('btn_reply'))
		{
			$this->reply_to_admin();
			return;
		}

		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
			
		if (!@$sess_data['writing_order_id'] && 
			!$this->is_writing_order_code_available(@$sess_data['writing_order_code']))
		{
			$sess_data = array();
			$this->session->set('prdetails_data', json_encode($sess_data));
			$feedback_view = 'writing/prdetails/partials/message_code_error';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->set_redirect('writing/prdetails');
		}
		
		$m_order_code = Model_Writing_Order_Code::find_code($sess_data['writing_order_code']);		
		$reseller_id = $m_order_code->reseller_id;
		
		if (!empty($sess_data['company_id'])) // this means its update.
		{
			$newsroom = Model_Newsroom::find($sess_data['company_id']); 
			$newsroom->company_name = $sess_data['companyname'];
			$newsroom->save();
		}
		else  // create a newsroom
		{
			$newsroom = Model_Newsroom::create($reseller_id, $sess_data['companyname']);
			$newsroom->is_archived = 1;
			$newsroom->is_reseller_controlled = 1;
			$newsroom->save();
		}
		
		if (!empty($sess_data['logo_image_id']))
		{
			$im = Model_Image::find($sess_data['logo_image_id']);
			$im->company_id = $newsroom->company_id;
			$im->save();
		}
		
		if (empty($sess_data['company_id']) || 
		  !($nr_custom = Model_Newsroom_Custom::find($sess_data['company_id'])))
			$nr_custom = new Model_Newsroom_Custom();
		
		if (empty($sess_data['company_id']) ||
		  !($company_profile = Model_Company_Profile::find($sess_data['company_id'])))
			$company_profile = new Model_Company_Profile();
		
		$nr_custom->company_id = $newsroom->company_id;
		if (!empty($sess_data['logo_image_id']))
			$nr_custom->logo_image_id = $sess_data['logo_image_id'];
		$nr_custom->save();
			
		$company_profile->company_id = $newsroom->company_id;
		$company_profile->address_street = value_or_null($sess_data['address_street']);
		$company_profile->address_apt_suite = value_or_null($sess_data['address_apt_suite']);
		$company_profile->address_city = value_or_null($sess_data['address_city']);
		$company_profile->address_state = value_or_null($sess_data['address_state']);
		$company_profile->address_zip = value_or_null($sess_data['address_zip']);
		$company_profile->website = value_or_null($sess_data['companyweb']);
		$company_profile->phone = value_or_null($sess_data['address_phone']);
		$company_profile->summary = value_or_null($sess_data['companydetails']);
		$company_profile->address_country_id = value_or_null($sess_data['address_country_id']);
		$company_profile->save();
		
		if (empty($sess_data['company_contact_id']) ||
		  !($company_contact = Model_Company_Contact::find($sess_data['company_contact_id'])))
			$company_contact = new Model_Company_Contact();
			
		$company_contact->company_id = $newsroom->company_id;
		$company_contact->name = $sess_data['companycontact'];
		$company_contact->title = 'Press Contact';
		$company_contact->email = $sess_data['emailaddress'];
		$company_contact->save();
		
		$newsroom->company_contact_id = $company_contact->id;
		$newsroom->save();
		
		if (empty($sess_data['content_id']))
		{
			$m_content = new Model_Content();
			$m_content->company_id = $newsroom->company_id;
			$m_content->type = Model_Content::TYPE_PR;		 
			$m_content->is_published = 0;
			$m_content->is_approved = 0;
			$m_content->is_draft= 1;		 
			$m_content->is_under_review = 0;
			$m_content->is_under_writing = 1;
			$m_content->is_premium = 1;		 
			$m_content->is_legacy = 0;	
			$m_content->is_credit_locked = 1;	 
			$m_content->date_publish = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->date_created = $m_content->date_publish;
			$m_content->save();
		}
		else
		{
			$m_content = Model_Content::find($sess_data['content_id']);
		}
		
		// now setting the images for the content
		$images=array();
		for($c=1; $c<=3; $c++)
			if(@$sess_data['related_image' . $c . '_id'])
				$images[]= $sess_data['related_image' . $c . '_id'];
		   
		$m_content->set_images($images);

		if ($sess_data['beat'])
		{
			$m_content->set_beats(array($sess_data['beat']));
		}
			
		// now setting the content data
		if (empty($sess_data['content_id']))
		     $content_data = new Model_Content_Data();
		else $content_data = Model_Content_Data::find($sess_data['content_id']);
			
		$content_data->content_id = $m_content->id;
		$content_data->rel_res_pri_title = value_or_null($sess_data['link_text_1']);
		$content_data->rel_res_pri_link = value_or_null($sess_data['link_1']);		 
		$content_data->rel_res_sec_title = value_or_null($sess_data['additional_link_text_1']);		 
		$content_data->rel_res_sec_link = value_or_null($sess_data['additional_link_1']);		
		$content_data->save();
		
		// creating object to store data specific to the
		// Press Release content type and linking it to the content
		if (empty($sess_data['content_id']))
		     $content_data_PR = new Model_PB_PR();
		else $content_data_PR = Model_PB_PR::find($sess_data['content_id']);
		
		$content_data_PR->content_id = $m_content->id;
		$content_data_PR->location = $sess_data['location'];
		$content_data_PR->web_video_provider = Video::PROVIDER_YOUTUBE;
		$video = Video::get_instance(Video::PROVIDER_YOUTUBE);
		$content_data_PR->web_video_id = $video->parse_video_id($sess_data['youtube_video']);
		$content_data_PR->save();
		
		// now adding tags
		$m_content->set_tags(explode(',', $sess_data['tags']));
		
		// now setting writing order
		if (!empty($sess_data['writing_order_id']))
		{
			$m_order = Model_Writing_Order::find($sess_data['writing_order_id']);
		}
		else		
		{
			$m_order = new Model_Writing_Order();
			$m_order->date_ordered = Date::$now->format(DATE::FORMAT_MYSQL);		
		}	
		
		if(@$sess_data['sent_to_customer_for_detail_change'])
			$m_order->status='customer_revise_details';

		$m_order->content_id = $m_content->id;
		
		// $m_order->company_id= $newsroom->company_id;
		
		// $m_order->writing_order_code= trim($sess_data['writing_order_code']);
		
		$m_order->writing_order_code_id = $m_order_code->id;
		
		$m_order->writing_angle = $sess_data['pr_angle'];	
		$m_order->angle_detail = $sess_data['angledetails'];
		$m_order->primary_keyword = $sess_data['primarykeyword'];
		$m_order->additional_comments = $sess_data['additional_comments'];		
		if(!@$sess_data['reseller_editing'] && !@$sess_data['within_24_hours_editing'])
			$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);		
		$m_order->save();		
		$t_code = Model_Writing_Order_Code::find(array(
						array('writing_order_code',$sess_data['writing_order_code'])));
		$t_code->is_used = 1;
		$t_code->save();
		
		if( ! @$sess_data['writing_order_id']) //its a new order.
		{
			if (@$reseller_id) 
				$this->send_new_order_email_notifications($reseller_id, $m_order->id, $m_order_code);				
			$this->load->view('writing/header');
			$this->load->view('writing/menu');
			$this->load->view('writing/pre-content');
			$this->load->view('writing/prdetails/thanks');
			$this->load->view('writing/post-content');				
			$this->load->view('writing/footer');	
		}
		elseif(@$sess_data['reseller_editing'])  //its an existing order, edited by reseller.
		{			
			$this->load->view('writing/header');
			$this->load->view('writing/menu');
			$this->load->view('writing/pre-content');
			$this->load->view('writing/prdetails/thanks_update_reseller');
			$this->load->view('writing/post-content');
			$this->load->view('writing/footer');
		}
		else  //its an existing order, edited by the customer.
		{			
			if(@$sess_data['sent_to_customer_for_detail_change'])
			{
				$w_process=new Model_Writing_Process();
				$w_process->writing_order_id= $sess_data['writing_order_id'];
				$w_process->process='customer_revise_details';
				$w_process->actor='customer';
				$w_process->process_date= Date::$now->format(DATE::FORMAT_MYSQL);
				$w_process->comments = value_or_null($this->input->post('reply_to_comments'));
				$w_process->save();
				if (@$reseller_id) 
					$this->send_order_updated_email_to_editor($sess_data['writing_order_id']);
			}	
			
			$this->load->view('writing/header');
			$this->load->view('writing/menu');
			$this->load->view('writing/pre-content');
			$this->load->view('writing/prdetails/thanks_update');
			$this->load->view('writing/post-content');
			$this->load->view('writing/footer');
		}
		
		
		
		$sess_data = array();
		$this->session->set('prdetails_data', json_encode($sess_data));	
	}

	public function reply_to_admin()
	{		
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));

		$m_order = Model_Writing_Order::find($sess_data['writing_order_id']);
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS;		
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();

		$w_process = new Model_Writing_Process();
		$w_process->writing_order_id = $sess_data['writing_order_id'];
		$w_process->process = Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS;
		$w_process->actor = Model_Writing_Process::ACTOR_CUSTOMER;
		$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$w_process->comments = $this->input->post('reply_to_comments');
		$w_process->save();

		$m_order_code = Model_Writing_Order_Code::find_code($sess_data['writing_order_code']);		
		if ($reseller_id = $m_order_code->reseller_id)		
			$this->send_order_updated_email_to_editor($sess_data['writing_order_id']);
		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/thanks_reply');
		$this->load->view('writing/post-content');
		$this->load->view('writing/footer');
	}
	
	public function send_order_updated_email_to_editor($order_detail_id)
	{
		$ci =& get_instance();	
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));			
		$reseller_id = $this->get_reseller_id_from_order_code($sess_data['writing_order_code']);
		$reseller = Model_User::find($reseller_id);
		$reseller_company = Model_Reseller_Details::find(array("user_id",$reseller_id));		
		
		
		$preview_link = $this->website_url("writing/prdetails/preview/{$order_detail_id}/" . 
						$sess_data['writing_order_code']);
		
		$ci =& get_instance();	
		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		//first sending email notification to the reseller
		$en = new Email_Notification(); 		
		$en->set_content_view('reseller/reseller_writing_details_revised_by_customer');		 						
		$en->set_data('first_name', $reseller->first_name);		
		$angleTitles=array("problem"=>"Problem / Solution - Introduces a problem and presents the website 
										or product as a solution",
							"discount"=>"Discount Offer or Special Offer Announcement",
							"website"=>"Website or product launch",
							"announcement"=>"Special Company Announcement - i.e. Company Merge, 
											Company Acquisition, Anniversary etc","other"=>"Other");
		$angle= $sess_data['pr_angle'];		
		$en->set_data('pr_angle', $angleTitles[$angle]); 		
		$en->set_data('cust_company_name', $sess_data['companyname']);
		$en->set_data('preview_link', $preview_link);
		
		if($reseller_company->editing_privilege == 'reseller_editor')
			$en->send($reseller);
		else if($reseller_company->editing_privilege == 'admin_editor')
		{			
			$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
			$en->send($writing_admin);
		}	
	}
	
	public function send_new_order_email_notifications($reseller_id, $order_detail_id, $m_order_code)
	{		
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));			
		$reseller = Model_User::find($reseller_id);
		$reseller_company=Model_Reseller_Details::find(array("user_id",$reseller_id));
		
		if($reseller_company->website)
			if(substr($reseller_company->website,strlen($reseller_company->website)-1, 1)!="/")
				$reseller_company->website.="/";
				
		$preview_link= $this->conf('website_url')."writing/prdetails/preview/".$order_detail_id
						 ."/".$sess_data['writing_order_code'];						
		
		//first sending email notification to the reseller
		$en = new Email_Notification();
		$en->set_content_view('reseller/customer_writing_order_details');		 						
		$en->set_data('writing_order_code', $sess_data['writing_order_code']);
		$en->set_data('preview_link', $preview_link);
		$en->send($reseller);
		
		//now sending email to the reseller's customer
		$em = new Email();
		$em->set_to_email($m_order_code->customer_email);	 
		$em->set_to_name($m_order_code->customer_name);
		$em->set_from_email($reseller->email);
		
		//$em->set_from_name($reseller->first_name . " " . $reseller->last_name);
		$em->set_subject('Thank You for Your Press Release Writing Order');
		$this->vd->companycontact = $sess_data['companycontact'];	
		$this->vd->writing_order_edit_link= $reseller_company->website."edit_form1.php?id=" . 
				$order_detail_id."&prcode=".$sess_data['writing_order_code'];
			
		$this->vd->reseller_company_name= $reseller_company->company_name;
		$message = $this->load->view('writing/email/customer_writing_order_thanks', null, true);
		
		$em->set_message($message);
		$em->enable_html();
		Mailer::send($em, Mailer::POOL_TRANSACTIONAL);		
	}
	
	public function get_reseller_id_from_order_code($writing_order_code)
	{
		if ($m_order_code = Model_Writing_Order_Code::find(array('writing_order_code', $writing_order_code)))
			return $m_order_code->reseller_id;
		
		return 0;
	}
	
	public function verify_code($tcode)
	{
		if ($this->is_writing_order_code_available($this->input->post('writing_order_code')))
		{
			$sess_data = array();
			$sess_data['writing_order_code'] = $this->input->post('writing_order_code');
			$this->session->set('prdetails_data', json_encode($sess_data));
			$this->redirect('writing/prdetails/step1');
		}
		else
		{
			$feedback_view = 'writing/prdetails/partials/message_code_error';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->redirect('writing/prdetails');
		}
	}
	
	public function is_writing_order_code_available($tcode)
	{
		$params = array(array('writing_order_code', $tcode), array('is_used', 0));
		return Model_Writing_Order_Code::find($params);
	}
	
	public function del_session_code()
	{
		$sess_data = array();
		$this->session->set('prdetails_data', json_encode($sess_data));
		$this->redirect('writing/prdetails/index');
	}
	
	public function is_code_entered()
	{
		$sess_data = (array) json_decode($this->session->get('prdetails_data'));
		if (empty($sess_data['writing_order_code']))
			$this->redirect('writing/prdetails');
	}
	
	public function load_empty_field_values($fields)
	{
		$data=array();
		for($k=0; $k<count($fields); $k++)
		{
			$data[$fields[$k]]="";
		}
		return $data;
	}
	
	public function preview($writing_order_id, $writing_order_code) //
	{		
		
		$m_order_code = Model_Writing_Order_Code::find(array('writing_order_code', $writing_order_code));
		
		$w_order = Model_Writing_Order::find($writing_order_id);
		
		if( ! $m_order_code || ! $w_order)
			show_404();	
		
		$dt_ordered = new DateTime($w_order->date_ordered);
		$m_content = Model_Content::find($w_order->content_id);		
		$m_comp = Model_Company::find($m_content->company_id);
		$dt_24_hours_ago = Date::hours(-24);	
		
		$reseller_id = $this->get_reseller_id_from_order_code($writing_order_code);
		$m_reseller_comp = Model_Reseller_Details::find($reseller_id);		
		
		
		if($m_reseller_comp->editing_privilege == 'reseller_editor' && @Auth::user()->id == $reseller_id){}
		
		elseif ($m_reseller_comp->editing_privilege != 'reseller_editor' && @Auth::user()->is_admin) {}
		
		elseif ($w_order->writing_order_code_id != $m_order_code->id || 
				($dt_ordered< $dt_24_hours_ago && @$w_order->status != 'sent_to_customer_for_detail_change'))
		
		{
			$this->not_editable();
			return;
		}
		
		
		
		
		$m_order_code = Model_Writing_Order_Code::find(array('writing_order_code', $writing_order_code));
		
		$writing_order = Model_Writing_Order::find($writing_order_id);
		
		if($writing_order && $m_order_code)
		{
			$w_order = Model_Writing_Order::find($writing_order_id);
			$m_content = Model_Content::find($w_order->content_id);
			if (!$m_content) show_404();
			if (!$m_content->is_draft) show_404();
							
			$company = Model_Company::find($m_content->company_id);
			$c_profile = Model_Company_Profile::find($m_content->company_id);
			$c_contact = Model_Company_Contact::find($company->company_contact_id);
			$nr_custom = Model_Newsroom_Custom::find($m_content->company_id);
						
			$m_content_data = Model_Content_Data::find($m_content->id);
			$m_pb_pr = Model_PB_PR::find($m_content->id);
			$tags_array = $m_content->get_tags();
			$tags = implode(",",$tags_array);
			$images = $m_content->get_images();
			
			$sess_data = array();	
			$sess_data['content_id'] = $m_content->id;
			$sess_data['company_id'] = $m_content->company_id;
			$sess_data['writing_order_id'] = $writing_order_id;
			$sess_data['company_contact_id'] = $c_contact->id;
			//step1 fields now
			$sess_data['writing_order_code'] = $writing_order_code;
			$sess_data['emailaddress'] = $c_contact->email;
			$sess_data['companyname'] = $company->name;
			$sess_data['companycontact'] = $c_contact->name;
			$sess_data['companyweb'] = $c_profile->website;
			$sess_data['address_street'] = $c_profile->address_street;
			$sess_data['address_apt_suite'] = $c_profile->address_apt_suite;
			$sess_data['address_city'] = $c_profile->address_city;
			$sess_data['address_state'] = $c_profile->address_state;
			$sess_data['address_zip'] = $c_profile->address_zip;
			$sess_data['address_country_id'] = $c_profile->address_country_id;
			$sess_data['address_phone'] = $c_profile->phone;
			$sess_data['companydetails'] = $c_profile->summary;
			//step2 fields now
			$sess_data['beat'] = @$m_content->get_beats()[0]->id;			
			$sess_data['pr_angle'] = $w_order->writing_angle;
			$sess_data['angledetails'] = $w_order->angle_detail;		
			//step3 fields now
			$sess_data['primarykeyword'] = $w_order->primary_keyword;
			$sess_data['tags'] = $tags;
			
			$sess_data['logo_image_id'] = $nr_custom->logo_image_id;
			$c = 1;
			foreach ($images as $image)
			{
				$sess_data['related_image'.$c.'_id'] = $image->id;
				$c++;
			}
					
			$sess_data['link_1'] = $m_content_data->rel_res_pri_link;
			$sess_data['link_text_1'] = $m_content_data->rel_res_pri_title;
			$sess_data['additional_link_1'] = $m_content_data->rel_res_sec_link;
			$sess_data['additional_link_text_1'] = $m_content_data->rel_res_sec_title;
			$sess_data['youtube_video'] = $m_pb_pr->web_video_id;
			$sess_data['additional_comments'] = @$w_order->additional_comments;
			$this->session->set('prdetails_data', json_encode($sess_data));		
			$this->vd->fields = $sess_data;	
			
			$country = Model_Country::find($sess_data['address_country_id']);
			$this->vd->countryName = $country->name;
			$cat = Model_Cat::find($sess_data['category']);
			$this->vd->catName = $cat->name;
			$angleTitles = array("problem"=>"Problem / Solution - Introduces a problem and presents the 
								website or product as a solution","discount" =>"Discount Offer or Special 
								Offer Announcement", "website"=>"Website or product launch",
								"announcement"=>"Special Company Announcement - i.e. Company Merge, 
								Company Acquisition, Anniversary etc","other"=>"Other");
								
			$angle = $sess_data['pr_angle'];
			$this->vd->angleTitle = $angleTitles[$angle]; 
			
			if(@$sess_data['logo_image_id'])
			{
			   $im = Model_Image::find($sess_data['logo_image_id']);
			   $im_file = $im->variant('header-thumb')->filename;
			   $im_url = Stored_Image::url_from_filename($im_file);		
			   $this->vd->fields['logo']= $im_url;
			}
			else
				$this->vd->fields['logo'] = 0;
				
			for ($c = 1; $c <= 3; $c++)
			{
			   if(@$sess_data['related_image'.$c.'_id'])
			   {
				   $im = Model_Image::find($sess_data['related_image'.$c.'_id']);
				   $im_file = $im->variant('web')->filename;
				   $im_url = Stored_Image::url_from_filename($im_file);		
				   $this->vd->fields['image'.$c]= $im_url;
				}
				else	
					$this->vd->fields['image'.$c] = "";	
			}
			$video = Video::get_instance(Video::PROVIDER_YOUTUBE);
			$video->parse_video_id($sess_data['youtube_video']);
			$this->vd->videoIframe= $video->render(533,300);
			
			$this->vd->details_change_comments = "";
			if(@$sess_data['detail_change_comments'])
				$this->vd->details_change_comments = $sess_data['detail_change_comments'];		
			
			$this->load->view('writing/header');
			$this->load->view('writing/menu');
			$this->load->view('writing/pre-content');
			$this->load->view('writing/prdetails/preview');
			$this->load->view('writing/post-content');				
			$this->load->view('writing/footer');	
		}
		else
		{
			show_404();
		}
		
	}
	
	protected function not_editable()
	{
		// its not editable, show an error page
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/prdetails/not_editable');
		$this->load->view('writing/post-content');
		$this->load->view('writing/footer');
	}
	
	public function edit($writing_order_id, $writing_order_code)
	{
		$m_order_code = Model_Writing_Order_Code::find_code($writing_order_code);		
		$w_order = Model_Writing_Order::find($writing_order_id);		
		if (!$m_order_code || !$w_order) show_404();
		if ($w_order->writing_order_code_id != $m_order_code->id)
			show_404();
		
		$dt_ordered = new DateTime($w_order->date_ordered);
		$m_content = Model_Content::find($w_order->content_id);		
		if (!$m_content) show_404();
		
		$m_comp = Model_Company::find($m_content->company_id);
		$dt_24_hours_ago = Date::hours(-24);	
		
		$reseller_id = $this->get_reseller_id_from_order_code($writing_order_code);
		$m_reseller_comp = Model_Reseller_Details::find($reseller_id);		
		
		
		if (($m_reseller_comp->editing_privilege == 'reseller_editor'
		    && @Auth::user()->id == $reseller_id) || Auth::is_admin_online())
		{
			// permission is allowed
		}
		elseif ($w_order->writing_order_code_id != $m_order_code->id || 
				($dt_ordered< $dt_24_hours_ago && @$w_order->status != 'sent_to_customer_for_detail_change'))
		{
			$this->not_editable();
			return;
		}
		
		//show the editing form		
		$company = Model_Company::find($m_content->company_id);
		$c_profile = Model_Company_Profile::find($m_content->company_id);
		$c_contact = Model_Company_Contact::find($company->company_contact_id);
		$nr_custom = Model_Newsroom_Custom::find($m_content->company_id);
		
		if (!$m_content->is_draft || @$w_order->status == 'customer_accepted')
		{
			$this->not_editable();
			return;
		}			
				
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_pb_pr = Model_PB_PR::find($m_content->id);
		$tags_array = $m_content->get_tags();
		$tags = implode(",",$tags_array);
		$images = $m_content->get_images();		
		
		$sess_data=array();
		
		
		if(Auth::user()->id == $m_comp->user_id)		
			$sess_data['reseller_editing']=1;
			
		if($w_order->status=='sent_to_customer_for_detail_change')
		{
			$sess_data['sent_to_customer_for_detail_change']=1;
			$criteria=array();
			$criteria[]=array('writing_order_id',$writing_order_id);
			$criteria[]=array('process','sent_to_customer_for_detail_change');
			$w_process=Model_Writing_Process::find_all($criteria, array("id","desc"), 1);			
			$sess_data['detail_change_comments']= $w_process[0]->comments;
		}	
		if($dt_ordered >= $dt_24_hours_ago)
			$sess_data['within_24_hours_editing'] = 1;
			
		$sess_data['content_id'] = $m_content->id;
		$sess_data['company_id'] = $m_content->company_id;
		$sess_data['writing_order_id'] = $writing_order_id;
		$sess_data['company_contact_id'] = $c_contact->id;
		
		
		//step1 fields now
		$sess_data['writing_order_code']= $writing_order_code;
		$sess_data['emailaddress']= $c_contact->email;
		$sess_data['companyname']= $company->name;
		$sess_data['companycontact']= $c_contact->name;
		$sess_data['companyweb']= $c_profile->website;
		$sess_data['address_street']= $c_profile->address_street;
		$sess_data['address_apt_suite']= $c_profile->address_apt_suite;
		$sess_data['address_city']= $c_profile->address_city;
		$sess_data['address_state']= $c_profile->address_state;
		$sess_data['address_zip']= $c_profile->address_zip;
		$sess_data['address_country_id']= $c_profile->address_country_id;
		$sess_data['address_phone']= $c_profile->phone;
		$sess_data['companydetails']= $c_profile->summary;
		//step2 fields now
		$sess_data['beat'] = @$m_content->get_beats()[0]->id;		
		$sess_data['pr_angle']= $w_order->writing_angle;
		$sess_data['angledetails']= $w_order->angle_detail;
		
		//step3 fields now
		$sess_data['primarykeyword']= $w_order->primary_keyword;
		$sess_data['tags']= $tags;
		
		$sess_data['logo_image_id']= $nr_custom->logo_image_id;
		$c=1;
		foreach($images as $image)
		{
			$sess_data['related_image' . $c . '_id']= $image->id;
			$c++;
		}			
		$sess_data['link_1']= $m_content_data->rel_res_pri_link;
		$sess_data['link_text_1']= $m_content_data->rel_res_pri_title;
		$sess_data['additional_link_1']= $m_content_data->rel_res_sec_link;
		$sess_data['additional_link_text_1']= $m_content_data->rel_res_sec_title;
		
		$sess_data['youtube_video']= $m_pb_pr->web_video_id;
		$sess_data['additional_comments']= $w_order->additional_comments;
		$this->session->set('prdetails_data', json_encode($sess_data));					
		$this->redirect('writing/prdetails/step1');		
	}
	
}

?>
