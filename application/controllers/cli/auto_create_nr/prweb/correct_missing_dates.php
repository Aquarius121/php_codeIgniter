<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Correct_Missing_Dates_Controller extends CLI_Base { 
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT c.*, url 
				FROM nr_content c
				INNER JOIN nr_pb_prweb_pr p
				ON p.content_id = c.id
				WHERE (c.date_publish LIKE '1970%'
					OR c.date_publish LIKE '0000%')
				AND c.is_checked_for_date = 0
				ORDER BY c.id
				LIMIT 1";
		
		

		while ($cnt <= 600)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$content = Model_Content::from_db($result);
			if (!$content) break;

			if ($publish_date = $this->get($content->url))
			{
				$content->date_created = $publish_date;
				$content->date_publish = $publish_date;

			}

			$content->is_checked_for_date = 1;
			$content->save();
			$cnt++;
			
		}
	}

	public function get($url)
	{
		lib_autoload('simple_html_dom');

		//$url = "http://prweb.com/releases/powerequipmentdirect/inc5000list/prweb4432474.htm";

		$html = @file_get_html($url);

		if (empty($html))
		{
			//echo "empty hay yar";
			return '2010-08-26 00:00';
		}

		$date_line = @$html->find('p[class=releaseDateline]', 0)->innertext;
		if (!empty($date_line))
		{
			preg_match("/(.*)\(PRWEB\)(.*)/", $date_line, $match);

			if (is_array($match) && count($match) > 1)
			{
				$address = $match[1];
				$p_date = $match[2];
				if (empty($p_date))
					echo "khali ha";
				$publish_date = date(DATE::FORMAT_MYSQL, strtotime($p_date));
			}
			else
			{
				$text = @$html->find('div[class=fullWidth]', 0)->innertext;
				if (!empty($text))
				{
					$reg = '/(\w+)\s*(\d+),\s*(\d{4})/';
					$match = preg_match($reg, $text, $matches);
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($matches[0]));
				}
			}
		}

		return $publish_date;	

	}

	
}

?>
