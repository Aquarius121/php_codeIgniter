<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Slugger {
	
	public static function create($text, $length = PHP_INT_MAX)
	{
		if (trim($text)) 
		{
			// normalize text
			$slug = strtolower($text);
			$slug = str_replace('\'', '', $slug);
			$slug = preg_replace('#[^a-z0-9]#is', '-', $slug);
			$slug = preg_replace('#--*#is', '-', $slug);
			$slug = preg_replace('#(^-|-$)#is', '', $slug);
			
			if (strlen($slug) > $length)
			{
				// trim and then re-normalize (no split word)
				$slug = substr($slug, 0, $length);
				$slug = preg_replace('#\-[^\-]+$#is', '', $slug);
				$slug = preg_replace('#(^\-|\-$)#is', '', $slug);
			}
			
			// return if has value
			if ($slug) return $slug;
		}
		
		// generate a random slug based on time
		return substr(md5(microtime(true)), 0, 32);
	}
	
	public static function create_with_random($text, $length = PHP_INT_MAX)
	{
		$slug = static::create($text, $length);
		
		// random value to append to end
		$rand = mt_rand(1000000, 9999999);
		$extra_len = strlen($rand) + 1;
		$max_len = $length;
		$max_len = $max_len - $extra_len;
		
		// this will reduce length but not split word
		$slug = substr($slug, 0, $max_len);
		$slug = preg_replace('#\-[^\-]+$#is', '', $slug);
		$slug = preg_replace('#(^\-|\-$)#is', '', $slug);
		$slug = sprintf('%s-%s', $slug, $rand);
		return $slug;
	}
	
}

?>