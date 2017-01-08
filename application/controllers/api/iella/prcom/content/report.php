<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Report_Controller extends PRCom_API_Base {

	public function index()
	{
		$rdata = new Request_Data();
		$uuid = $this->iella_in->uuid;
		if ($content = Model_Content::find_uuid($uuid))
			$newsroom = $content->newsroom();
		else return;

		if ($this->iella_in->options)
			foreach ($this->iella_in->options as $k => $v)
				$rdata->{$k} = $v;

		$url = "manage/analyze/content/report_index/{$content->id}/1/0";
		$url = $rdata->insert_into_query_string($url);
		$url = $newsroom->url($url);

		$rdata->save();

		$report = new PDF_Generator($url);
		$report->set_zoom_level(0.75);
		$report->generate();

		$download = $report->indirect();
		$this->iella_out->download = 
			$this->website_url($download);
	}

}

?>