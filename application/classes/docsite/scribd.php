<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DocSite_Scribd extends DocSite_Base {
	
	public function upload($file, $name, $title)
	{
		if (!is_file($file))
			throw new Exception();
				
		$conf = $this->dsconf;
		$response = Unirest\Request::post($conf->url, null, array(
			'api_key' => $conf->api_key,
			'file' => Unirest\File::add($file),
			'method' => 'docs.upload',
		));
		
		if ($response->code != 200)
			return null;
		
		$str = $response->raw_body;
		$xml = simplexml_load_string($str);
		$docs = $xml->xpath('//doc_id');
		if (!$docs[0]) return null;
		$doc_id = $docs[0]->__toString();
		$doc_url = sprintf($conf->url_doc, $doc_id);
		
		Unirest\Request::post($conf->url, null, array(
			'api_key' => $conf->api_key,
			'doc_ids' => $doc_id,
			'method' => 'docs.changeSettings',
			'title' => $title,
		));

		return $doc_url;
	}
	
}

?>