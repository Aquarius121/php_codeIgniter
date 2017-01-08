<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_Post extends Social_Twitter_API {
	
	const MAX_LENGTH = 140;
	const TCO_LENGTH = 25;
	
	protected $message;
	
	public function set_message($value)
	{
		$this->message = $value;
	}
	
	public function save()
	{
		$params = array('status' => $this->message);
		try { $status = $this->twitter->post('statuses/update', $params); }
		catch (Exception $e) { return null; }
		if (!isset($status->id_str)) return null;
		return $status->id_str;
	}

	public static function parse($text)
	{
		// converts links, @username, #hash to <a> links for twitter
		$text = preg_replace('!(http|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/]+!', 
			"<a href=\"\\0\" target=\"_blank\">\\0</a>", $text); 
		$text = preg_replace('#@([\\d\\w]+)#', 
			'<a href="http://twitter.com/$1" target=\"_blank\">$0</a>', $text);
		$text = preg_replace('/#([\\d\\w]+)/', 
			'<a href="http://twitter.com/search?q=%23$1&src=hash" target=\"_blank\">$0</a>',
			$text);
		
		return $text;
	}
	
}

?>