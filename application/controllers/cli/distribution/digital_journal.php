<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Digital_Journal_Controller extends CLI_Base {

	const BASE_URL = 'http://www.digitaljournal.com';
	const LIST_URL = 'http://www.digitaljournal.com/pr/?provider=inewswire';

	public function index()
	{
		$this->trace('started');

		$request = new HTTP_Request(static::LIST_URL);
		$response = $request->get();

		if (!$response || !$response->data)
		{
			$this->trace_failure('no response');
			return;
		}

		lib_autoload('php_query');
		$_doc = phpQuery::newDocumentHTML($response->data);
		$_pr_items = pq('div.pr-item', $_doc);

		foreach ($_pr_items as $_pr_item)
		{
			$_link = pq('a', $_pr_item);
			$relative_uri = $_link->attr('href');
			$d_url = concat(static::BASE_URL, $relative_uri);
			$title = trim($_link->text());

			// transform title for query in our db
			$title = preg_replace('#[^a-z0-9\-]#', '%', $title);
			$title = sprintf('%s%%', $title);

			$m_content = Model_Content::find(array(
				array('date_publish', '>', Date::hours(-24)),
				array('title', 'like', $title),
				array('is_published', 1),
				array('is_premium', 1),
			));

			if ($m_content)
			{
				// we *should* always find a result for digital journal
				$d_site = Model_Distribution_Site::find_site_from_url($d_url);
				if (!$d_site) $d_site = Model_Distribution_Site::create_site_from_url($d_url);

				$d_index = Model_Distribution_Index::find_index_from_source($d_site, $m_content);
				if (!$d_index) $d_index = Model_Distribution_Index::create_index_from_source($d_site, $m_content);
				$d_index->url = $d_url;
				$d_index->save();
				$this->trace($d_url);
			}
		}

		$this->trace('finished');
	}
	
}

?>