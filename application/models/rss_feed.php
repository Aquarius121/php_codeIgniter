<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_RSS_Feed extends Model {
	
	const MAX_SLUG_LENGTH = 64;

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_custom_rss_feed';
	
	protected static $markers = array(
		'content-url' => 'Content URL',
		'content-title'=> 'Content Title',
		'company-name' => 'Company Name',
		'company-newsroom-url' => 'Newsroom URL',
		'company-website' => 'Company Website'
	);
	
	public static function markers()
	{
		return static::$markers;
	}
	
	public static function find_slug($slug)
	{
		return static::find('slug', $slug);
	}
	
	public function title_to_slug()
	{
		$this->slug = static::generate_slug($this->title, (int) $this->id);
	}
	
	public static function generate_slug($title, $existing_id = 0)
	{
		$ci =& get_instance();
		$slug = Slugger::create($title, static::MAX_SLUG_LENGTH);
		
		// Checking to make sure the slug does not already exist 		
		$sql = "SELECT 1 FROM nr_custom_rss_feed 
				WHERE slug = ? AND id != ?";

		while (true)
		{			
			$params = array($slug, $slug, (int) $existing_id);
			$result = $ci->db->query($sql, $params);
			if (!$result->num_rows()) return $slug;
			$slug = Slugger::create_with_random($title, static::MAX_SLUG_LENGTH);
		}
	}
	
	public static function generate_content($params, $content)
	{
		return Marker::replace_all($content, array(
			'content-url' => $params['url'],
			'content-title'=> $params['title'],
			'company-name' => $params['company_name'],
			'company-newsroom-url' => $params['newsroom_url'],
			'company-website' => $params['company_website'],
		));
	}
}

?>