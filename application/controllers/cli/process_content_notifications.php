<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Process_Content_Notifications_Controller extends CLI_Base {

	protected $iteration_counter = 0;

	const MAX_ITERATIONS = 720;

	public function index()
	{
		// allow at most 1 process
		if ($this->process_count() > 1)
			return;

		// restart the process after a while to free resources
		while ($this->iteration_counter < static::MAX_ITERATIONS)
		{
			$this->process();
			sleep(15);
		}
	}
	
	public function process()
	{
		set_time_limit(3600);

		while (true)
		{
			// find content in the notification queue
			$sql = "SELECT c.* FROM nr_content_notification_queue cnq
				INNER JOIN nr_content c ON c.id = cnq.content_id";

			$dbr = $this->db->query($sql);
			$m_content = Model_Content::from_db($dbr);
			if (!$m_content) break;

			// remove from the notification queue
			$sql = "DELETE FROM nr_content_notification_queue
				WHERE content_id = {$m_content->id}";
			$dbr = $this->db->query($sql);

			// content isn't published anymore
			// so we shouldn't do anything
			if (!$m_content->is_published)
				continue;

			$event = new Iella_Event();
			$event->data->content = $m_content;
			$event->emit('content_published');

			// notify the user that the content has been published
			if (!$m_content->owner()->is_mail_blocked(Model_User_Mail_Blocks::PREF_CONTENT_PUBLISHED))
			{
				$sch_n = new Model_Scheduled_Notification();
				$sch_n->related_id = $m_content->id;
				$sch_n->class = Model_Scheduled_Notification::CLASS_CONTENT_PUBLISHED;
				$sch_n->user_id = $m_content->owner()->id;
				$sch_n->save();
			}
		}
	}
	
}