<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
load_controller('api/iella/base');
 
class Event_Controller extends Iella_Base {

	public function on_content_approved()
	{
		// event that you subscribed to
		// echo $this->iella_in->event->name;
		// method called in response (this method)
		// echo $this->iella_in->event->method;
		
		// data transmitted with event (example)		
		$content_id = $this->iella_in->id;
		$m_content = Model_Content::find($content_id);		
		$w_order = Model_Writing_Order::find(array('company_id', $m_content->company_id));
		$w_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$w_order->status = 'approved';
		$w_order->save();
				
		$m_w_process = new Model_Writing_Process();
		$m_w_process->writing_order_id = $w_order->id;
		$m_w_process->process = 'approved';
		$m_w_process->actor = 'admin';
		$m_w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_w_process->save();
		
		$m_content->is_published = 1;
		$m_content->save();
	}
	
	public function on_content_rejected()
	{
		// event that you subscribed to
		// echo $this->iella_in->event->name;
		// method called in response (this method)
		// echo $this->iella_in->event->method;
		
		// data transmitted with event (example)		
		$content_id = $this->iella_in->id;
		$m_content = Model_Content::find($content_id);
		$w_order = Model_Writing_Order::find(array('company_id', $m_content->company_id));
		$w_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$w_order->status = 'rejected';
		$w_order->save();
				
		$m_w_process = new Model_Writing_Process();
		$m_w_process->writing_order_id = $w_order->id;
		$m_w_process->process = 'rejected';
		$m_w_process->actor = 'admin';
		$m_w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_w_process->save();
	}
	
}