<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class WireUpdate_Controller extends Iella_Base {
	
	public function index()
	{
		$supported_types = array(
			Model_Content::TYPE_PR,
			Model_Content::TYPE_NEWS,
			Model_Content::TYPE_EVENT,
			Model_Content::TYPE_BLOG,
		);

		$content = Model_Content::from_object($this->iella_in->content);
		if (!in_array($content->type, $supported_types))
			return;

		$impressions_uri = new Stats_URI_Builder();
		$impressions_uri->add_content_impression($content);
		$impressions_uri_str = $impressions_uri->build();
		$this->vd->impressions_uri = $impressions_uri_str;

		$beats = $content->get_beats();
		$beats_id = array();
		foreach ($beats as $beat)
			$beats_id[] = (int) $beat->id;

		if (!count($beats_id)) return;
		$beats_in_list = sql_in_list($beats_id);
		$sql = "SELECT c.id, c.email, c.first_name, c.last_name
			FROM nr_contact_beat_interest cbi 
			INNER JOIN nr_contact c
			ON c.id = cbi.contact_id
			AND cbi.beat_id IN ({$beats_in_list})
			INNER JOIN nr_wireupdate_subscriber ws
			ON c.id = ws.contact_id
			AND ws.has_realtime_{$content->type}_update = 1
			GROUP BY c.id";

		$dbr = $this->db->query($sql);
		$contacts = Model_Contact::from_db_all($dbr);
		$from_email = $this->conf('journalists_email_address');
		$from_name = $this->conf('journalists_email_name');
		$content->load_content_data();
		$content->load_local_data();

		foreach ($contacts as $contact)
		{
			$subscriber = Model_WireUpdate_Subscriber::find_contact($contact);
			$this->vd->subscriber = $subscriber;
			$this->vd->contact = $contact;
			$this->vd->content = $content;

			$view = 'email/journalists/realtime';
			$message = $this->load->view($view, null, true);
			$subject = sprintf('Newswire Update: %s', $content->title);

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
	
}

?>