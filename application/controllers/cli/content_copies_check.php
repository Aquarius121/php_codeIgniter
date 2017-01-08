<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Content_Copies_Check_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;

	public function index()
	{
		$type = escape_and_quote(Model_Content::TYPE_PR);
		$sql = "SELECT c.* FROM nr_content c 
			LEFT JOIN nr_content_copies cc
			ON cc.content_id = c.id
			WHERE cc.copies IS NULL
			AND c.is_scraped_content = 0
			AND c.date_publish > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 3 HOUR)
			AND c.date_publish < DATE_ADD(UTC_TIMESTAMP(), INTERVAL 3 HOUR)
			AND c.is_draft = 0
			AND c.is_premium = 1
			AND c.type = {$type}
			LIMIT 1000";

		$copyscape = Copyscape_API_Factory::create();
		$results = Model_Content::from_sql_all($sql);

		foreach ($results as $result)
		{
			try 
			{
				$result->load_content_data();
				$text = HTML2Text::plain($result->content);
				if (strlen($text) < 50)
					continue;

				$copies = $copyscape->count($text);
				$m_cc = new Model_Content_Copies();
				$m_cc->content_id = $result->id;
				$m_cc->copies = $copies;
				$m_cc->save();
			}
			catch (Exception $e)
			{
				$data = new stdClass();
				$data->message = $e->getMessage();
				$data->result = $result;

				(new Critical_Alert($data))->send();
				sleep(60);
			}
		}
	}

}