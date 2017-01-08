<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_WireUpdate_Dailies_Controller extends CLI_Base {
	
	protected $supported_types = array(
		Model_Content::TYPE_PR,
		Model_Content::TYPE_NEWS,
		Model_Content::TYPE_EVENT,
		Model_Content::TYPE_BLOG,
	);

	public function index()
	{
		if (!($last_dt_conf = Model_Name_Value::find('Send_WireUpdate_Dailies')))
		{
			$last_dt_conf = new Model_Name_Value();
			$last_dt_conf->name = 'Send_WireUpdate_Dailies';
			$last_dt_conf->value = Date::hours(-24);
			$last_dt_conf->save();
		}

		$types_in_list = sql_in_list($this->supported_types);
		$last_dt = Date::utc((string) $last_dt_conf->value);
		$last_dt_conf->value = Date::$now;
		$last_dt_conf->save();

		$sql = "SELECT * FROM nr_content 
			WHERE is_published = 1
			AND type IN ({$types_in_list})
			AND date_publish > ?";
		$dbr = $this->db->query($sql, array($last_dt));
		$subscriber_collection = array();
		$impressions_uri = new Stats_URI_Builder();

		foreach ($dbr->result() as $content)
		{
			$content = Model_Content::from_object($content);
			$subscribers = $this->find_subscribers_for_content($content);
			$impressions_uri->add_content_impression($content);

			foreach ($subscribers as $subscriber)
			{
				if (!isset($subscriber_collection[$subscriber->id]))
				{
					$subscriber_collection[$subscriber->id] = new stdClass();
					$subscriber_collection[$subscriber->id]->subscriber = $subscriber;
					$subscriber_collection[$subscriber->id]->content = array();
				}

				$subscriber_collection[$subscriber->id]->content[] = $content;
			}
		}

		$impressions_uri_str = $impressions_uri->build();
		$this->vd->impressions_uri = $impressions_uri_str;

		$from_email = $this->conf('journalists_email_address');
		$from_name = $this->conf('journalists_email_name');

		foreach ($subscriber_collection as $sub_data)
		{
			$subscriber = $sub_data->subscriber;
			$contact = $subscriber->contact;
			// limited to 100 results so email doesnt explode
			$content_results = array_slice($sub_data->content, 0, 100);
			$this->vd->subscriber = $subscriber;
			$this->vd->contact = $contact;
			$this->vd->content_results = $content_results;

			$view = 'email/journalists/daily';
			$subject = 'Newswire Daily Summary';
			$message = $this->load->view($view, null, true);

			$email = new Email();
			$email->set_from_email($from_email);
			$email->set_from_name($from_name);
			$email->set_to_name($contact->name());
			$email->set_to_email($contact->email);
			$email->set_subject($subject);
			$email->set_message($message);
			$email->enable_html();

			Mailer::queue($email, true, Mailer::POOL_MARKETING);
		}
	}

	protected function find_subscribers_for_content($content)
	{
		$beats = $content->get_beats();
		$beats_id = array();
		foreach ($beats as $beat)
			$beats_id[] = (int) $beat->id;

		if (!count($beats_id)) return array();
		$beats_in_list = sql_in_list($beats_id);
		$sql = "SELECT c.id, c.email, c.first_name, c.last_name
			FROM nr_contact_beat_interest cbi 
			INNER JOIN nr_contact c
			ON c.id = cbi.contact_id
			AND cbi.beat_id IN ({$beats_in_list})
			INNER JOIN nr_wireupdate_subscriber ws
			ON c.id = ws.contact_id
			AND ws.has_daily_{$content->type}_update = 1
			GROUP BY c.id";

		$dbr = $this->db->query($sql);
		$contacts = Model_Contact::from_db_all($dbr);
		$content->load_local_data();
		$content->load_content_data();
		$subscribers = array();

		foreach ($contacts as $contact)
		{
			$subscriber = Model_WireUpdate_Subscriber::find_contact($contact);
			if (!$subscriber) continue;
			$subscriber->contact = $contact;
			$subscribers[] = $subscriber;
		}

		return $subscribers;
	}
	
}

?>