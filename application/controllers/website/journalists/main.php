<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Main_Controller extends Website_Base {

	public $title = 'Journalists';

	public function index()
	{
		$this->render_website('website/pages/journalists');
	}

	protected function verify($sub_id, $secret)
	{
		$m_sub = Model_WireUpdate_Subscriber::find($sub_id);
		if ($m_sub->is_verified) return $m_sub;

		$nv = Model_Name_Value::find($secret);
		if (!$nv || $nv->value != $sub_id) return;

		$this->vd->first_time_activation = 1;
		$m_sub->has_daily_pr_update = 1;
		$m_sub->has_daily_news_update = 1;
		$m_sub->has_daily_event_update = 1;
		$m_sub->has_daily_blog_update = 0;
		$m_sub->has_realtime_pr_update = 0;
		$m_sub->has_realtime_news_update = 0;
		$m_sub->has_realtime_event_update = 0;
		$m_sub->has_realtime_blog_update = 0;
		$m_sub->is_verified = 1;
		$m_sub->save();

		$mdb = new Model_Contact_MDB_Approval();
		$mdb->contact_id = $m_sub->contact_id;
		$mdb->save();

		return $m_sub;
	}

	public function activate($sub_id, $secret)
	{
		$m_sub = $this->verify($sub_id, $secret);
		if (!$m_sub) return;

		if ($this->input->post())
		{
			if ($this->input->post('first_time_activation'))
			{
				$fback = new Feedback('success');
				$fback->set_title('Success!');
				$fback->set_text('Your subscription has been activated.');
				$this->use_feedback($fback);
			}
			else
			{
				$fback = new Feedback('success');
				$fback->set_title('Success!');
				$fback->set_text('Your preferences have been saved.');
				$this->use_feedback($fback);
			}

			$m_sub->has_daily_pr_update = $this->input->post('has_daily_pr_update');
			$m_sub->has_daily_news_update = $this->input->post('has_daily_news_update');
			$m_sub->has_daily_event_update = $this->input->post('has_daily_event_update');
			$m_sub->has_daily_blog_update = $this->input->post('has_daily_blog_update');
			$m_sub->has_realtime_pr_update = $this->input->post('has_realtime_pr_update');
			$m_sub->has_realtime_news_update = $this->input->post('has_realtime_news_update');
			$m_sub->has_realtime_event_update = $this->input->post('has_realtime_event_update');
			$m_sub->has_realtime_blog_update = $this->input->post('has_realtime_blog_update');
			$m_sub->save();
		}

		$this->vd->subscriber = $m_sub;
		$this->render_website('website/journalists/activate');
	}
	
}

?>