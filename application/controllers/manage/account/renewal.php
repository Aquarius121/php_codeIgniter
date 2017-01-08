<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Renewal_Controller extends Manage_Base {

	const LISTING_CHUNK_SIZE = 10;
	public $title = 'Automatic Renewals';

	public function index($chunk = 1)
	{
		$user = Auth::user();
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('manage/account/renewal/-chunk-');
		$chunkination->set_url_format($url_format);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ci.*, 
			cs.is_legacy,
			i.name AS item_name,
			i.type AS item_type,
			o.id AS order_id
			FROM co_component_item ci
			INNER JOIN co_component_set cs 
			ON cs.user_id = {$user->id}
			AND ci.component_set_id = cs.id 
			AND ci.is_auto_renew_enabled = 1
			INNER JOIN co_item i ON 
			ci.item_id = i.id
			LEFT JOIN co_order o 
			ON o.component_set_id = cs.id
			ORDER BY i.type = ? DESC, 
			date_created DESC
			{$limit_str}";
			
		$db_result = $this->db->query($sql, array(Model_Item::TYPE_PLAN));
		$results = Model_Component_Item::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$this->vd->results = $results;
		$this->vd->chunkination = $chunkination;
		$chunkination->set_total($total_results);
				
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds())
		{
			$url = 'manage/account/renewal';
			$this->redirect(gstring($url));
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/renewal');
		$this->load->view('manage/footer');
	}
	
	public function cancel($ci_id)
	{
		$list_url = 'manage/account/renewal';
		$component_item = Model_Component_Item::find($ci_id);
		if (!$component_item) $this->redirect($list_url);
		if (!$component_item->is_auto_renew_enabled) $this->redirect($list_url);
		$component_set = Model_Component_Set::find($component_item->component_set_id);
		if ($component_set->user_id != Auth::user()->id) 
			$this->denied();
		
		if ($this->input->post('confirm'))
		{
			$component_item->cancel();
			$no_record = (bool) $this->input->post('no_record');
			$no_record = $no_record && Auth::is_admin_online();
			
			if (!$no_record)
			{
				$raw_data = new stdClass();
				$raw_data->reason = $this->input->post('reason');
				$m_cancel = Model_Cancellation::create();
				$m_cancel->component_item_id = $component_item->id;
				$m_cancel->raw_data($raw_data);
				$m_cancel->save();
			}
			
			$item = Model_Item::find($component_item->item_id);
			if ($item->type == Model_Item::TYPE_PLAN)
			{
				// record the events within KM
				$kmec = new KissMetrics_Event_Library(Auth::user());
				$kmec->event_cancelled();
				
				if (!$no_record)
				{
					// schedule cancel task event for next run
					$event = new Scheduled_Iella_Event();
					$event->data->cancellation = $m_cancel->values();
					$event->data->user = Auth::user()->values();
					$event->data->item = $item->values();
					$event->schedule('cancellation_task');
				}
			}
			
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('The automatic renewal has been cancelled.');
			$this->add_feedback($feedback);
			$this->redirect($list_url);
		}
		
		$this->vd->component_item = $component_item;
		$this->vd->component_set = $component_set;
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/renewal-cancel');
		$this->load->view('manage/footer');
	}
	
	public function suspend($ci_id)
	{
		if (!Auth::is_admin_online()) return;
		$component_item = Model_Component_Item::find($ci_id);
		if (!$component_item) $this->redirect($list_url);
		if (!$component_item->is_auto_renew_enabled) $this->redirect($list_url);
		$component_set = Model_Component_Set::find($component_item->component_set_id);
		if ($component_set->user_id != Auth::user()->id) 
			$this->denied();

		if ($component_item->is_suspended)
		{
			$component_item->is_suspended = 0;
			$component_item->save();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('The renewal has been activated.');
			$this->add_feedback($feedback);
			$this->redirect('manage/account/renewal');
		}
		else
		{
			$component_item->is_suspended = 1;
			$component_item->save();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('The renewal has been suspended.');
			$this->add_feedback($feedback);
			$feedback = new Feedback('info');
			$feedback->set_title('Attention!');
			$feedback->set_text('The renewal will be cancelled if not resumed');
			$feedback->add_text(sprintf(' within %d days of expiration.', 
				Renewal::MAX_SUSPENSION_PERIOD));
			$this->add_feedback($feedback);
			$this->redirect('manage/account/renewal');
		}		
	}

}

?>
