<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quick_Image {

	protected static $variants = array(
		// company logo stored for nr_newsroom_custom
		'logo' => array('header', 'header-finger', 'header-thumb', 'header-sidebar'),
		// related image for nr_content_image
		'related' => array('finger', 'view-web', 'web'),
		// cover image for content (includes related)
		'cover' => array('cover', 'view-cover', 'finger', 'view-web', 'web', 'cover-feed', 'cover-website'),
		// contact cover image for content
		'contact' => array('contact', 'contact-cover', 'contact-2x'),
		// importing image from somewhere (muhammad added for image content type)
		'image' => array('view-cover', 'thumb', 'finger', 'cover-website', 'cover', 'view-full'),
	);

	// import an existing image
	public static function import($type, $file)
	{
		if (!isset(static::$variants[$type]))
			throw new Exception();
		
		$ci =& get_instance();
		$v_sizes = $ci->conf('v_sizes');
		$image = new Model_Image();
		$image->save();
		
		if (!$image->id)
			throw new Exception();
		
		$sim_original = Stored_Image::from_file($file, 'jpg');
		$sim_original->move();
		
		foreach (static::$variants[$type] as $variant)
		{
			if (!isset($v_sizes[$variant]))
			{
				$image->remove();
				throw new Exception();
			}
			
			$sim_variant = $sim_original->from_this_resized($v_sizes[$variant]);
			$image->add_variant($sim_variant->save_to_db(), $variant);
		}
		
		$image->add_variant($sim_original->save_to_db(), 'original');
		return $image;
	}

}