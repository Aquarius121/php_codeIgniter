<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Publish_Content_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	protected $review_period;
	protected $iteration_counter = 0;

	// number of iterations before we 
	// exit process and go again
	const MAX_ITERATIONS = 1000;

	// how often is the user notified 
	// about the publish failure. this
	// is multiplied by attempt interval
	const NOTIFICATION_FREQUENCY = 8;

	public function index()
	{
		// allow at most 1 process
		if ($this->process_count() > 1) sleep(30);
		if ($this->process_count() > 1) return;

		// restart the process after a while to free resources
		while ($this->iteration_counter < static::MAX_ITERATIONS)
		{
			$this->process();
			sleep(5);
		}
	}
	
	public function process()
	{
		set_time_limit(3600);
		
		// publish content that is approved
		// or does not require approval
		$sql = "SELECT * FROM nr_content c 
			WHERE c.is_published = 0
			AND c.is_draft = 0
			AND c.is_approved = 1
			AND c.date_publish < UTC_TIMESTAMP()";
			
		$db_result = $this->db->query($sql);
		$results = Model_Content::from_db_all($db_result);
		
		foreach ($results as $result)
		{			
			$this->iteration_counter++;

			if (!$result->is_identity_locked)
			{
				$result->is_identity_locked = 1;

				// only set current date/time
				// for publish when not backdated
				if (!$result->is_backdated)
				{
					$result->date_publish = Date::utc();
					$result->date_publish->setSeconds(0);
					$result->date_updated = Date::utc();
				}
				
				// schedule identity locked event for next run
				$event = new Scheduled_Iella_Event();
				$event->data->content = $result->values();
				$event->schedule('content_identity_locked');
			}
			
			$result->is_credit_locked = 1;
			$result->is_published = 1;
			$result->is_under_review = 0;
			$result->save();

			// add to the content notification queue
			$sql = "INSERT IGNORE INTO 
				nr_content_notification_queue
				VALUES ({$result->id})";
			$this->db->query($sql);
			
			// record the events within KM
			$kmec = new KissMetrics_Event_Library($result->owner());
			$kmec->event_published($result);
		}

		// review is normally 24 hours before desired publish
		// * as of march 2015 this is now set super high 
		$this->review_period = Model_Setting::value('review_period');
		$review_dt_cut = Date::hours($this->review_period)->format(Date::FORMAT_MYSQL);
		
		while (true)
		{
			$this->iteration_counter++;

			// require credit lock or non-credit type
			$internal_types = Model_Content::internal_types();
			$credit_types = Model_Content::__requires_credit();
			$non_credit_types = array_diff($internal_types, $credit_types);
			$non_credit_types = sql_in_list($non_credit_types);
			
			$sql = "SELECT * FROM nr_content c WHERE
				c.is_published = 0 AND
				c.is_draft = 0 AND
				c.is_under_review = 0 AND
				c.is_approved = 0 AND 
				c.date_publish < ? AND
				    (c.is_credit_locked = 1 
				  OR c.type IN ({$non_credit_types}))
				LIMIT 1";
				
			$params = array($review_dt_cut);
			$dbr = $this->db->query($sql, $params);
			$m_content = Model_Content::from_db($dbr);
			if (!$m_content) break;
			
			$m_newsroom = Model_Newsroom::find($m_content->company_id);
			$m_user = Model_User::find_company_id($m_content->company_id);

			// sanity check for bad data
			if (!$m_newsroom || !$m_user)
			{
				$m_content->is_draft = 1;
				$m_content->save();
				continue;
			}
			
			// archived newsroom => do not publish (reseller controlled is exception)
			if ($m_newsroom->is_archived && !$m_newsroom->is_reseller_controlled)
			{
				$m_content->is_draft = 1;
				$m_content->save();
				continue;
			}
			
			$this->scheduled_transfer($m_content, $m_user);
		}
	}
	
	protected function scheduled_transfer($m_content, $m_user)
	{
		if ($m_content->requires_approval())
		{
			if (!$m_user->is_mail_blocked(Model_User_Mail_Blocks::PREF_CONTENT_UNDER_REVIEW))
			{
				$sch_n = new Model_Scheduled_Notification();
				$sch_n->related_id = $m_content->id;
				$sch_n->class = Model_Scheduled_Notification::CLASS_CONTENT_UNDER_REVIEW;
				$sch_n->user_id = $m_user->id;
				$sch_n->save();
			}

			$iella_event = new Scheduled_Iella_Event();
			$iella_event->data->content = $m_content;
			$iella_event->schedule('content_under_review');
		
			if ($m_user->raw_data()->auto_hold_under_review)
			{
				// automatically hold this PR for admin review 
				// because it was submitted under a flagged user account
				$hold_rd = array('comments' => 'Automatic hold based on user account.');
				$hold = Model_Hold_Data::find_or_create($m_content);
				$hold->raw_data($hold_rd);
				$hold->save();
			}

			$m_content->is_approved = 0;
			$m_content->is_rejected = 0;
			$m_content->is_under_review = 1;
			$m_content->save();
		}
		else
		{
			$m_content->is_approved = 1;
			$m_content->save();
		}
	}
	
}