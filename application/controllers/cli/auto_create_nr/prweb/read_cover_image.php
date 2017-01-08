<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Read_Cover_Image_Controller extends CLI_Base { 

//load_controller('browse/base');
//class Read_Cover_Image_Controller extends Browse_Base { 

	public function index()
	{
		lib_autoload('simple_html_dom');

		$cnt = 1;
		
		$sql = "SELECT p.*, c.slug 
				FROM nr_pb_prweb_pr p
				INNER JOIN ac_nr_prweb_category c
				ON p.prweb_category_id = c.id
				WHERE is_read_for_cover_image = 0
				ORDER BY content_id 
				LIMIT 1";

		while ($cnt++ <= 200)
		{
			set_time_limit(300);

			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$pb_prweb = Model_PB_PRWeb_PR::from_db($result);
			if (!$pb_prweb) break;

			$path_to_file = "raw/prweb_data/categories/{$pb_prweb->slug}/content/{$pb_prweb->content_id}.html";
			
			$html = @file_get_html($path_to_file);

			if (empty($html))
			{
				$pb_prweb->is_read_for_cover_image = 1;
				$pb_prweb->save();
			}


			$img_src = @$html->find('div[class=nismall clearfix] img[class=newsImage]', 0)->src;
			if (empty($img_src))
				$img_src = @$html->find('div[class=ninormal clearfix] img[class=newsImage]', 0)->src;

			if (!empty($img_src))
				$pb_prweb->cover_image_url = $img_src;
			
			$pb_prweb->is_read_for_cover_image = 1;
			$pb_prweb->save();

		}
	}
		
}

?>
