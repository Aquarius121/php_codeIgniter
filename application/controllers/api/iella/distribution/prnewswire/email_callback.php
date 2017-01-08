<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use ZBateson\MailMimeParser\MailMimeParser;

load_controller('api/iella/base');

class Email_Callback_Controller extends Iella_Base {
	
	public function index()
	{
		$callback = Model_Email_Callback::from_object($this->iella_in->callback);
		if (!UUID::validate($callback->id)) return;
		if (!($content_id = $callback->raw_data_object()->content_id))
			return;

		$distribution = Model_PRN_Distribution::find($content_id);
		$distRDO = $distribution->raw_data_object();
		if ($distRDO->report)
			return;

		$parser = new MailMimeParser();
		$message = $parser->parse($this->iella_in->email);
		$body = $message->getHtmlContent();
		$report = false;

		$html = HTML_Util::queryPath($body);
		$html->find('a')->each(function($i, $v) use (&$report) {
			if (trim($v->textContent) === 'View Report Details') {
				$report = $v->getAttribute('href');
				return false;
			}
		});

		if ($report)
		{
			$distRDO->report = $report;
			$distribution->raw_data($distRDO);
			$distribution->save();
		}
	}
	
}