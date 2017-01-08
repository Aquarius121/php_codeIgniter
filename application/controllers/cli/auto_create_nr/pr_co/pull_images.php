<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch the 
// PR.Co images (from media kit)on demand

load_controller('cli/auto_create_nr/base');

class Pull_Images_Controller extends Auto_Create_NR_Base { 

	public function index($company_id)
	{
		set_time_limit(5000);

		if (empty($company_id))
			return false;

		$cnt = 1;

		$sql = "SELECT cd.newsroom_url, cd.pr_co_company_id,
				c.company_id, c.pr_co_category_id
				FROM ac_nr_pr_co_company c
				INNER JOIN ac_nr_pr_co_company_data cd
				ON cd.pr_co_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_images_list_fetched = 0
				AND cd.is_images_fetched = 0
				ORDER BY cd.pr_co_company_id 
				LIMIT 1";


		$result = $this->db->query($sql);

		if ($result->num_rows())
		{
			$c_data = Model_PR_Co_Company_Data::from_db($result);
			
			$this->fetch_images_list($c_data, $cnt);
						
			$c_data->is_images_list_fetched = 1;
			$c_data->save();
		}

		
		// Now fetching individual Images

		$comp = Model_PR_Co_Company::find('company_id', $company_id);
		$c_data = Model_PR_Co_Company_Data::find($comp->id);

		$sql = "SELECT p.* 
				FROM nr_pb_pr_co_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_pb_image pi
				ON pi.content_id = c.id
				WHERE p.pr_co_company_id = '{$comp->id}'
				AND c.type = ?
				AND pi.image_id = 0
				ORDER BY content_id DESC
				LIMIT 1";

		$cnt = 1;
		while (1)
		{
			$result = $this->db->query($sql, Model_Content::TYPE_IMAGE);
			
			if (!$result->num_rows()) break;
		
			$pr_co_pr = Model_PB_PR_Co_Content::from_db($result);
			if (!$pr_co_pr) break;

			$this->fetch_single_image($pr_co_pr, $company_id);
			
			if ($cnt%2 == 0)
				sleep(2);

			$cnt++;			
		}
		
		$c_data->is_images_fetched = 1;
		$c_data->save();
		
	}

	public function fetch_images_list($c_data, $cnt)
	{
		set_time_limit(5000);

		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}presskit";
		}

		$html = @file_get_html($url);

		if (empty($html))
			return 0;

		
		
		$pr_co_cat = Model_PR_Co_Category::find($c_data->pr_co_category_id);
		
		foreach (@$html->find('section[class=thumbnails],ul[class=media_thumbnails]') as $images_section)
		{
		
			foreach(@$images_section->find('li[class=image] a') as $anchor)
			{
				$img_title = $img_url = null;

				$img_title = @$anchor->title;
				$img_url = @$anchor->find('img', 0)->src;

				if (empty($img_url))
					$img_url = @$anchor->href;

				// echo $img_title . "\n";
				// echo $img_url . "\n ----------------------------- \n";

				if ($img_url && substr($img_url, 0, 4) != "http")
					$img_url = "http://www.pr.co{$img_url}";

				$criteria = array();
				$criteria[] = array('url', $img_url);
				$criteria[] = array('pr_co_company_id', $c_data->pr_co_company_id);

				if ($img = Model_PB_PR_Co_Content::find($criteria))
				{}

				elseif (!empty($img_url))
				{
					$m_content = new Model_Content();
					$m_content->company_id = $c_data->company_id;
					$m_content->type = Model_Content::TYPE_IMAGE;
					$m_content->title = null;
					$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
					$m_content->date_publish = Date::$now->format(Date::FORMAT_MYSQL);
					$m_content->is_draft = 1;
					$m_content->title_to_slug();
					$m_content->save();

					$pr_co_pr = new Model_PB_PR_Co_Content();
					$pr_co_pr->pr_co_company_id = $c_data->pr_co_company_id;
					$pr_co_pr->pr_co_category_id = $c_data->pr_co_category_id;
					$pr_co_pr->content_id = $m_content->id;
					$pr_co_pr->url = $img_url;				
					$pr_co_pr->save();

					$m_img = new Model_PB_Image();
					$m_img->content_id = $m_content->id;
					$m_img->save();
				}
			}
		}

	}

	protected function fetch_single_image($pr_co_pr, $company_id)
	{
		set_time_limit(300);
		set_memory_limit('2048M');

		lib_autoload('simple_html_dom');

		if (empty($pr_co_pr->url))
			return false;
		
		$html = @file_get_html($pr_co_pr->url);
		
		if (empty($html))
		{
			$m_pb_image = Model_PB_Image::find($pr_co_pr->content_id);
			$m_pb_image->image_id = -1; 
			$m_pb_image->save();
			return;
		}

		$m_content = Model_Content::find($pr_co_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($pr_co_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $pr_co_pr->content_id;
			$m_c_data->save();
		}


		if (!$m_pb_image = Model_PB_Image::find($m_content->id))
		{
			$m_pb_image = new Model_PB_Image();
			$m_pb_image->content_id = $m_content->id;
		}

		$image_url = $img_src = $pr_co_pr->url;

		$image_url = str_replace("-medium-", "-original-", $image_url);

		$image_size = $this->curl_get_file_size($image_url);

		// if the original image size is too big.
		// we will try fetching the image that is 
		// being displayed on PR.Co
		if ($image_size == -1 || $image_size > Image::MAX_VALID_FILE_SIZE)
			$image_url = $img_src;

		$image_size = $this->curl_get_file_size($image_url);


		// IF image size is still greater
		// we remove this content.
		if ($image_size == -1 || $image_size > Image::MAX_VALID_FILE_SIZE)
		{
			$m_content->delete();
			$pr_co_pr->delete();
			return;
		}

		
		if (!empty($image_url))
		{
			$img_file = "image";
			@copy($image_url, $img_file);
				
			if (Image::is_valid_file($img_file))
			{
				// import the cover image into the system
				$im = Quick_Image::import("image", $img_file);				 
				$im->company_id = $company_id;
				$im->save();

				$m_pb_image->image_id = $im->id;
				$m_pb_image->license = value_or_null($license);
				$m_pb_image->save();
			}
			else
			{
				$m_content->delete();
				$pr_co_pr->delete();
				return;
			}
		}

		////////////////////////////////////////////////////////////////////////////

		$m_content->title_to_slug();
		$m_content->date_updated = null;
		$m_content->date_publish = null;
		$m_content->is_published = 1;
		$m_content->is_draft = 0;
		
		$m_content->save();
	
	}


	protected function curl_get_file_size( $url ) 
	{
		$result = -1;
		$curl = curl_init( $url );

		// Issue a HEAD request and follow any redirects.
		curl_setopt( $curl, CURLOPT_NOBODY, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		//curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );

		$data = curl_exec( $curl );
		curl_close( $curl );

		if( $data ) 
		{
			$content_length = "unknown";
			$status = "unknown";

			if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) 
			{
				$status = (int)$matches[1];
			}

			if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) 
			{
				$content_length = (int)$matches[1];
			}

			// http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
			if( $status == 200 || ($status > 300 && $status <= 308) ) 
			{
				$result = $content_length;
				
			}

			
		}

		return $result;

	}
}

?>
