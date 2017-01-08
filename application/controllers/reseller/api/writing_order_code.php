<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');

class Writing_Order_Code_Controller extends API_Base {
	
	public function add()
	{
		$reseller_id = Auth::user()->id;
		$m_reseller_detail = Model_Reseller_Details::find($reseller_id);
		
		$customer_name = implode(' ', array(
			@$this->iella_in->customer_first_name, 
			@$this->iella_in->customer_last_name));
		if (!trim($customer_name)) $customer_name = null;
		
		$payer_email = @$this->iella_in->payer_email;
		$remote_ipn_track_id = @$this->iella_in->ipn_track_id;
		$txn_id = @$this->iella_in->txn_id;
		
		if (!($quantity = (int) @$this->iella_in->quantity))
			// fallback to quantity1 for support
			$quantity = (int) @$this->iella_in->quantity1;
		
		if (!($remote_item_name = @$this->iella_in->item_name))
			// fallback to item_name1 for support
			$remote_item_name = @$this->iella_in->item_name1;		
		
		if (isset($this->iella_in->send_email_to_customer))
		     $send_email_to_customer = $this->iella_in->send_email_to_customer;
		else $send_email_to_customer = true;
		
		if ($remote_ipn_track_id)
		{
			$criteria = array('remote_ipn_track_id', $remote_ipn_track_id);
			$ipn_check = Model_Writing_Order_Code::find($criteria);
		}
		
		if (empty($payer_email))
		{
			$this->iella_out->errors[] = '<payer_email> field is required';
			$this->iella_out->success = false;
		}
		
		if (!$remote_ipn_track_id)
		{
			$this->iella_out->errors[] = '<ipn_track_id> field is required';
			$this->iella_out->success = false;
		}	
		
		if ($quantity < 1)
		{
			$this->iella_out->errors[] = 'field <quantity> should have a value greater than zero';
			$this->iella_out->success = false;
		}	
					
		if ($ipn_check)
		{
			$this->iella_out->errors[] = '<ipn_track_id> already exists';
			$this->iella_out->success = false;
		}
		
		if ($quantity > Auth::user()->writing_credits())
		{
			$this->iella_out->errors[] = '<quantity> exceeds available credits';
			$this->iella_out->success = false;
		}
		
		// an error => exit now
		if (!$this->iella_out->success) return;
		
		// consume writing credits before converting to codes
		Auth::user()->consume_writing_credits($quantity);
		
		$wo_codes = array();
		for ($i = 0; $i < $quantity; $i++)
		{		
			$wo_code_str = Model_Writing_Order_Code::generate_code();
			$wo_codes[] = $wo_code_str;
			
			$m_wt_code = new Model_Writing_Order_Code();
			$m_wt_code->writing_order_code = $wo_code_str;
			$m_wt_code->customer_name = $customer_name;
			$m_wt_code->customer_email = $payer_email;
			$m_wt_code->remote_item_name = $remote_item_name;
			$m_wt_code->remote_ipn_track_id = $remote_ipn_track_id;
			$m_wt_code->remote_transaction_id = $txn_id;
			$m_wt_code->reseller_id = Auth::user()->id;
			$m_wt_code->date_ordered = Date::$now->format(DATE::FORMAT_MYSQL);
			$m_wt_code->save();
		}
		
		if ($send_email_to_customer)
		{
			$reseller = Model_User::find($reseller_id);
			$subject = 'Important: Instructions to Send in Your PR Details';
			$this->vd->how_it_works_link = $m_reseller_detail->website_url('learn-how-it-works');
			$this->vd->writing_order_codes = implode('<br>', $wo_codes);
			$this->vd->pr_details_form_link = $m_reseller_detail->website_url('prdetailsform.php');
			$this->vd->reseller_company_name = $m_reseller_detail->company_name;
			$this->vd->helpdesk_link = $m_reseller_detail->website_url('helpdesk/');
			$message = $this->load->view('writing/email/customer_credits_purchase', null, true);
			
			$em = new Email();
			$em->set_to_email($payer_email);
			$em->set_from_email($reseller->email);
			if ($customer_name) $em->set_to_name($customer_name);
			$em->set_from_name($m_reseller_detail->company_name);
			$em->set_subject($subject);
			$em->set_message($message);
			$em->enable_html();
			Mailer::send($em, Mailer::POOL_TRANSACTIONAL);
		}
		
		$this->iella_out->message = 'writing order code(s) added successfully';
		$this->iella_out->codes = $wo_codes;
		$this->iella_out->success = true;
	}
	
}

?>
