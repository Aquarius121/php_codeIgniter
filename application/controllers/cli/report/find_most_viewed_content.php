<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Find_Most_Viewed_Content_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		// *************************************
		// NOTE! need to show release plus data in this output
		// *************************************

		set_time_limit(86400);
		$dateCut = escape_and_quote((string) Date::months(-12));
		$sql = "SELECT c.* FROM nr_content c INNER JOIN nr_company cm 
			ON c.company_id = cm.id AND cm.user_id > 1 
			WHERE c.date_publish >= {$dateCut}
			AND c.id > ? AND c.type = 'pr' ORDER BY c.id ASC LIMIT 100";
		$sq = new Stats_Query();
		$desiredAmount = 100;
		$topContent = array();
		$lastid = 0;
		$counter = 0;

		while (true)
		{
			usleep(1);
			$content_arr = Model_Content::from_sql_all($sql, array($lastid));
			if (!$content_arr) break;

			foreach ($content_arr as $content)
			{
				$counter++;
				$this->trace_info('scanned', $counter);

				$lastid = $content->id;
				$hash = new Stats_Hash();
				$hash->content = $content->id;
				$ctx = $hash->context();
				$content->hits = $sq->hits_summation($ctx);

				if (count($topContent) < $desiredAmount)
				{
					$this->trace_success('found', $content->uuid);
					$topContent[] = $content;
					usort($topContent, function($b, $a) {
						return spaceship($a->hits, $b->hits);
					});
				}
				else
				{
					if ($content->hits > $topContent[$desiredAmount-1]->hits)
					{
						$this->trace_success('found', $content->uuid);
						$topContent[$desiredAmount-1] = $content;
						usort($topContent, function($b, $a) {
							return spaceship($a->hits, $b->hits);
						});
					}
				}
			}
		}

		$file = 'application/data/report/most_viewed_content.csv';
		$csv = new CSV_Writer($file);

		$csv->write(array(
			'Views',
			'Date Published',
			'Content ID',
			'Content Title',
			'Content URL',
			'Content Distribution Bundle',
			'Company Name',			
			'User Email',
			'Emails',
		));

		foreach ($topContent as $content)
		{
			$newsroom = $content->newsroom();
			$user = $newsroom->owner();

			$csv->write(array(
				$content->hits,
				concat((string) Date::utc($content->date_publish), ' UTC'),
				$content->id,
				$content->title,
				$this->website_url($content->url()),
				$content->distribution_bundle()->name(),
				$newsroom->company_name,
				$user->email,
				implode(PHP_EOL, array_map(function($a) use ($newsroom) {
					return $newsroom->url(sprintf('manage/contact/campaign/edit/%d', $a->id));
				}, Model_Campaign::find_all(array('content_id', $content->id)))),
			));
		}

		$csv->close();
	}

}
