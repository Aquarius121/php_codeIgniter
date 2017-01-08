<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class View_Data extends stdClass {

	public function __get($name)
	{
		return null;
	}

	// see: View_Data::esc_html
	public function esc($content)
	{
		return $this->esc_html($content);
	}

	// escape content for inclusion in html
	public function esc_html($content)
	{
		$content = htmlspecialchars($content);
		return $content;
	}

	// encode $object as JSON
	// for use in javascript context
	public function json($object)
	{
		$object = json_encode($object);
		return $object;
	}
	
	public function add_all($data)
	{
		foreach ($data as $k => $v)
			$this->$k = $v;
	}

	// see: View_Data::safe_html
	public function pure($content, $options = array())
	{
		return $this->safe_html($content, $options);
	}	

	// strip out any unsafe html 
	public function safe_html($content, $options = array())
	{
		if ($content === null) return null;

		// make the html pure
		lib_autoload('html_purifier');
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.EscapeNonASCIICharacters', true);
		$config->set('CSS.AllowedProperties', array());
		foreach ($options as $k => $v) $config->set($k, $v);
		$purifier = new HTMLPurifier($config);
		$content = $purifier->purify($content);

		// remove-on-save elements
		lib_autoload('php_query');
		$doc = phpQuery::newDocumentHTML($content);
		pq('.remove-on-save', $doc)->remove();
		pq('.nanospell-typo', $doc)->contentsUnwrap();
		pq('.nanospell-typo-disabled', $doc)->contentsUnwrap();		
		$content = $doc->__toString();
		
		return $content;
	}	
	
	// cut a string to the specified length
	// and append ... when string was cut
	public function cut($content, $length)
	{
		if (mb_strlen($content) > $length)
		{
			$content = mb_substr($content, 0, $length - 4);
			$content = "{$content} ...";
		}
		
		return $content;
	}
	
}