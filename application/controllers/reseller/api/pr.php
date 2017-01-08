<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');
load_controller('reseller/api/company_trait');

class PR_Controller extends API_Base {
	
	const PER_PAGE = 100;
	
	use Company_Trait;
	
	public function index()
	{
		if (!($chunk = @$this->iella_in->page)) $chunk = 1;
			
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::PER_PAGE);
		$limit_str = $chunkination->limit_str();
				
		$user_id = Auth::user()->id;
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*
			FROM nr_content c 
			INNER JOIN nr_company cm ON c.company_id = cm.id
			INNER JOIN nr_user u ON cm.user_id = u.id
			WHERE c.type = ? AND u.id = ?
			AND c.is_under_writing = 0 
			ORDER BY c.id DESC
			{$limit_str}";
		
		$dbr = $this->db->query($sql, 
			array(Model_Content::TYPE_PR, Auth::user()->id));
		$m_contents = Model_Content::from_db_all($dbr);
		
		$results = array();
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		foreach ($m_contents as $m_content)
		{			
			$result = new stdClass();
			$result->pr_id = $m_content->id;
			$result->pr_title = $m_content->title;
			$result->pr_url = $this->common()->url($m_content->url());
			$result->pr_status = new stdClass();
			$result->pr_status->is_published = $m_content->is_published;
			$result->pr_status->is_under_review = $m_content->is_under_review;
			$result->pr_status->is_draft = $m_content->is_draft;
			$result->pr_status->is_scheduled = (int) $m_content->is_scheduled();
			$result->pr_status->is_approved = $m_content->is_approved;
			$result->pr_status->is_rejected = $m_content->is_rejected;
			if ($m_content->is_published) 
			     $result->pr_date_published = $m_content->date_publish;
			else $result->pr_date_published = null;
			$results[] = $result;
		}
		
		$this->iella_out->page = $chunk;
		$this->iella_out->results = $results;
		$this->iella_out->total_results = $total_results;
		$this->iella_out->results_per_page = static::PER_PAGE;
	}
	
	public function add_pr_validation()
	{
		if (Auth::user()->pr_credits_premium() <= 0)
		{
			$this->iella_out->errors[] = 'no PR credits are available';
			$this->iella_out->success = false;
		}
		
		$required_fields = array(
			'pr_title',
			'pr_summary',
			'pr_content',
			'pr_tags',
		);
		
		foreach ($required_fields as $field)
		{
			if (!@$this->iella_in->{$field})
			{
				$this->iella_out->success = false;
				$this->iella_out->errors[] = "<{$field}> field is required";
			}
		}
		
		if (str_word_count(@$this->iella_in->pr_title) < 4)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = '<pr_title> must have at least 4 words';
		}
		
		if (str_word_count(@$this->iella_in->pr_summary) < 10)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = '<pr_summary> must have at least 10 words';
		}
		
		$content_min_words = $this->conf('press_release_min_words');		
		if (str_word_count(strip_tags(@$this->iella_in->pr_content)) < $content_min_words)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = sprintf(
				'<pr_content> must have at least %d words',
				$content_min_words);
		}
		
		$content_max_length = $this->conf('press_release_max_length');
		if (strlen(strip_tags(@$this->iella_in->pr_content)) > $content_max_length)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = sprintf(
				'<pr_content> must not exceed %d characters',
				$content_max_length);
		}
		
		$content_max_links = $this->conf('press_release_links_premium');	
		if (preg_match_all('/(<a[^>]*>)/i', @$this->iella_in->pr_content) > $content_max_links)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = sprintf(
				'<pr_content> must not exceed %d embedded links',
				$content_max_links);
		}
		
		if (!is_array(@$this->iella_in->pr_tags)
			|| count($this->iella_in->pr_tags) < 3
			|| count($this->iella_in->pr_tags) > 12)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = '<pr_tags> must have between 3 and 12 values';
		}
		
		return $this->iella_out->success;
	}
	
	public function add()
	{
		$this->add_pr_validation();
		
		if (isset($this->iella_in->company_id))
		{
			$company_id = @$this->iella_in->company_id;
			$newsroom = Model_Newsroom::find_company_id($company_id);
			if (!$newsroom || $newsroom->user_id != Auth::user()->id)
			{
				$this->iella_out->errors[] = 'no such record found for <company_id>';
				$this->iella_out->success = false;
				return;
			}
		}
		else
		{
			if (!$this->add_company_validation())
			{
				$error = 'must either provide <company_id> or all required fields related to company';
				$this->iella_out->warnings[] = $warnings;
				return;
			}
			
			$newsroom = $this->add_company();
		}
		
		if (!$this->iella_out->success) return;
	
		$title = $this->iella_in->pr_title;
		$summary = $this->iella_in->pr_summary;
		$content = $this->vd->pure($this->iella_in->pr_content);
		$tags = $this->iella_in->pr_tags;
		$beats = (array) $this->iella_in->beats;
		
		$category1 = value_or_null(@$this->iella_in->pr_category1_id);
		$category2 = value_or_null(@$this->iella_in->pr_category2_id);
		$category3 = value_or_null(@$this->iella_in->pr_category3_id);

		$supporting_quote = value_or_null(@$this->iella_in->pr_supporting_quote);
		$supporting_quote_name = value_or_null(@$this->iella_in->pr_supporting_quote_name);
		$supporting_quote_title = value_or_null(@$this->iella_in->pr_supporting_quote_title);		
		$additional_file_1_name = @$this->iella_in->pr_additional_file_1_name;
		$additional_file_1_url = @$this->iella_in->pr_additional_file_1_url;
		$additional_file_2_name = @$this->iella_in->pr_additional_file_2_name;
		$additional_file_2_url = @$this->iella_in->pr_additional_file_2_url;
		$images = (array) @$this->iella_in->pr_images;
		
		$primary_link_title = value_or_null(@$this->iella_in->pr_primary_link_title);
		$primary_link = value_or_null(@$this->iella_in->pr_primary_link);
		$secondary_link_title = value_or_null(@$this->iella_in->pr_secondary_link_title);
		$secondary_link = value_or_null(@$this->iella_in->pr_secondary_link);
		$youtube_video = value_or_null(@$this->iella_in->pr_youtube_video);
		
		$m_images = array();
		
		foreach ($images as $image)
		{
			$im_file = File_Util::buffer_file();
			
			if (@copy($image, $im_file))
			{
				$raw_image = Image::from_file($im_file); 
				
				if ($raw_image->is_valid())
				{
					// save the image as a JPEG in
					// the original buffer file name
					$raw_image->save($im_file);
					 
					// import as before into system
					$m_images[] = $m_image = Legacy_Image::import('related', $im_file);
					$m_image->company_id = $newsroom->company_id;
					$m_image->save();
				}
				else
				{
					$this->iella_out->warnings[] = "not a valid image: {$image}";
				}					
			}
			else
			{
				$this->iella_out->warnings[] = "failed to download the image: {$image}";
			}
		} 
				
		$m_content = new Model_Content();
		$m_content->company_id = $newsroom->company_id;
		$m_content->title = $title;
		$m_content->type = Model_Content::TYPE_PR;
		$m_content->title_to_slug();		
		$m_content->is_published = 0;
		$m_content->is_approved = 0;
		$m_content->is_draft = 0;
		$m_content->is_under_review = 0;
		$m_content->is_premium = 1;
		$m_content->is_legacy = 0;
		$m_content->date_publish = Date::$now->format(Date::FORMAT_MYSQL);
		$m_content->date_created = $m_content->date_publish;
		$m_content->is_credit_locked = 1;
		$m_content->save();
		
		// consume credit now so that the scheduled API 
		// press releases do not allow additional add calls.
		Auth::user()->consume_pr_credit_premium($m_content);
		
		$m_content->set_images($m_images);
		$m_content->set_tags($tags);
		$m_content->set_beats($beats);

		if (!count($beats))
		{
			$beats = array();
			array_merge($beats, Model_Cat_To_Beat::beats($category1));
			array_merge($beats, Model_Cat_To_Beat::beats($category2));
			array_merge($beats, Model_Cat_To_Beat::beats($category3));
			$m_content->set_beats($beats);
		}
		
		$content_data = new Model_Content_Data();
		$content_data->content_id = $m_content->id;
		$content_data->summary = $summary;
		$content_data->content = $content;
		$content_data->supporting_quote = $supporting_quote;
		$content_data->supporting_quote_name = $supporting_quote_name;
		$content_data->supporting_quote_title = $supporting_quote_title;
		$content_data->rel_res_pri_title = $primary_link_title;
		$content_data->rel_res_pri_link = $primary_link;
		$content_data->rel_res_sec_title = $secondary_link_title;
		$content_data->rel_res_sec_link = $secondary_link;
		$content_data->save();
		
		$content_data_PR = new Model_PB_PR();
		$content_data_PR->cat_1_id = $category1;
		$content_data_PR->cat_2_id = $category2;
		$content_data_PR->cat_3_id = $category3;
		$content_data_PR->content_id = $m_content->id;
		
		if ($additional_file_1_name && $additional_file_1_url)
		{
			$add_file1 = File_Util::buffer_file();
			
			if (@copy($additional_file_1_url, $add_file1))
			{
				$extension = Stored_File::parse_extension($additional_file_1_name);
				$file = Stored_File::from_file($add_file1, $extension);
				
				if (!$file->has_supported_extension())
				{
					$this->iella_out->warnings[] = 'file extension forbidden for additional file 1';
					$file->delete();
				}
				else if ($file->size() > $this->conf('max_web_file_size'))
				{
					$this->iella_out->warnings[] = 'file size limit exceeded for additional file 1';
					$file->delete();
				}
				else
				{
					$file->move();
					$content_data_PR->stored_file_name_1 = $additional_file_1_name;
					$content_data_PR->stored_file_id_1 = $file->save_to_db();
				}
			}
			else
			{
				$this->iella_in->warnings[] = 'failed to download <pr_additional_file_1_url>';
			}
		}
		
		if ($additional_file_2_name && $additional_file_2_url)
		{
			$add_file2 = File_Util::buffer_file();
			
			if (@copy($additional_file_2_url, $add_file2))
			{
				$extension = Stored_File::parse_extension($additional_file_2_name);
				$file = Stored_File::from_file($add_file2, $extension);
				
				if (!$file->has_supported_extension())
				{
					$this->iella_out->warnings[] = 'file extension forbidden for additional file 2';
					$file->delete();
				}
				else if ($file->size() > $this->conf('max_web_file_size'))
				{
					$this->iella_out->warnings[] = 'file size limit exceeded for additional file 2';
					$file->delete();
				}
				else
				{
					$file->move();		
					$content_data_PR->stored_file_name_2 = $additional_file_2_name;
					$content_data_PR->stored_file_id_2 = $file->save_to_db();
				}
			}
			else
			{
				$this->iella_in->warnings[] = 'failed to download <pr_additional_file_2_url>';
			}			
		}
		
		$content_data_PR->web_video_provider = Video::PROVIDER_YOUTUBE;
		$content_data_PR->web_video_id = $youtube_video;
		$content_data_PR->clean_files();
		$content_data_PR->clean_video();
		$content_data_PR->save();
		
		if ($pdf_branding_logo = @$this->iella_in->pdf_branding_logo)
		{
			$im_file = File_Util::buffer_file();
				
			if (@copy($pdf_branding_logo, $im_file))
			{
				$raw_image = Image::from_file($im_file); 	
							
				if ($raw_image->is_valid())
				{
					// save the image as a JPEG in
					// the original buffer file name
					$raw_image->save($im_file);
					 
					// import as before into system
					$image = Legacy_Image::import('logo', $im_file);
					
					// insert into rw_reseller_pr_logo such that this 
					// logo image will be used for PDF branding for this content 
					$sql = "INSERT INTO rw_reseller_pr_logo (content_id, image_id) VALUES (?, ?)";
					$this->db->query($sql, array($m_content->id, $image->id));
				}
				else
				{
					$this->iella_out->warnings[] = '<pdf_branding_logo> is not a valid image';	
				}
			}
			else
			{
				$this->iella_out->warnings[] = 'failed to download <pdf_branding_logo>';
			}
		}
		
		$this->iella_out->company_id = $newsroom->company_id;
		$this->iella_out->id = $m_content->id;
	}
	
	public function view()
	{
		if (!isset($this->iella_in->pr_id))
		{
			$this->iella_out->errors[] = '<pr_id> field is required';
			$this->iella_out->success = false;
			return;
		}
		
		if (!($this->iella_out->result = $this->find_pr($this->iella_in->pr_id)))
		{
			$this->not_found_error();
			return;
		}
	}
	
	public function find_pr($content_id)
	{
		$m_content = Model_Content::find($content_id);
		if (!$m_content) return null;
		if ($m_content->owner()->id != Auth::user()->id)
			return null;
		
		$m_content->load_local_data();
		$m_content->load_content_data();
				
		$result = new stdClass();
		
		$result->pr_url = $this->common()->url($m_content->url());
		$result->pr_slug = $m_content->slug;
		$result->pr_title = $m_content->title;
		$result->pr_summary = $m_content->summary;
		$result->pr_content = $m_content->content;
		$result->pr_tags = $m_content->get_tags();
		
		if ($m_content->is_published) 
		     $result->pr_date_published = $m_content->date_publish;
		else $result->pr_date_published = null;
		
		$result->pr_status = new stdClass();
		$result->pr_status->is_published = $m_content->is_published;
		$result->pr_status->is_under_review = $m_content->is_under_review;
		$result->pr_status->is_draft = $m_content->is_draft;
		$result->pr_status->is_scheduled = (int) $m_content->is_scheduled();
		$result->pr_status->is_approved = $m_content->is_approved;
		$result->pr_status->is_rejected = $m_content->is_rejected;
		
		if ($m_content->is_rejected)
		{
			$comments = array();
			
			if ($rejection = Model_Rejection_Data::find($m_content->id))
			{
				$rejection_data = $rejection->raw_data();
				if (!empty($rejection_data->comments))
				{
					$comments_std = new stdClass();
					$comments_std->title = null;
					$comments_std->content = $rejection_data->comments;
					$comments[] = $comments_std;
				}
				
				if (!empty($rejection_data->canned) && is_array($rejection_data->canned))
				{
					foreach ($rejection_data->canned as $canned_id)
					{
						$canned = Model_Canned::find($canned_id);
						$canned_std = new stdClass();
						$canned_std->title = $canned->title;
						$canned_std->content = $canned->content;
						$comments[] = $canned_std;
					}
				}
			}	
			
			$result->pr_status->comments = $comments;	
		}
		
		$beats = $m_content->get_beats();
		$result->beats = array();

		foreach ($beats as $beat)
		{
			$_beat = new stdClass();
			$_beat->id = $beat->id;
			$_beat->name = $beat->name;
			$result->beats[] = $_beat;
		}

		$result->pr_category1_id = $m_content->cat_1_id;
		$result->pr_category2_id = $m_content->cat_2_id;
		$result->pr_category3_id = $m_content->cat_3_id;

		$result->pr_category1_name = @Model_Cat::find(array(array('id', $m_content->cat_1_id)))->name;
		$result->pr_category2_name = @Model_Cat::find(array(array('id', $m_content->cat_2_id)))->name;
		$result->pr_category3_name = @Model_Cat::find(array(array('id', $m_content->cat_3_id)))->name;

		$result->pr_supporting_quote = $m_content->supporting_quote;
		$result->pr_supporting_quote_name = $m_content->supporting_quote_name;
		$result->pr_supporting_quote_title = $m_content->supporting_quote_title;
			
		$m_images = $m_content->get_images();
		$result->pr_images = array();
		
		foreach ($m_images as $m_image)
		{
			$im_file = $m_image->variant('original')->filename;
			$im_url = Stored_Image::url_from_filename($im_file);
			$result->pr_images[] = $this->common()->url($im_url);
		}
		
		$result->pr_primary_link_title = $m_content->rel_res_pri_title;
		$result->pr_primary_link = $m_content->rel_res_pri_link;
		$result->pr_secondary_link_title = $m_content->rel_res_sec_title;
		$result->pr_secondary_link = $m_content->rel_res_sec_link;
		
		if ($m_content->stored_file_id_1)
		{
			$file1 = Stored_File::from_db($m_content->stored_file_id_1);
			$result->pr_additional_file_1 = $this->common()->url($file1->url());
		}
		
		if ($m_content->stored_file_id_2)
		{
			$file2 = Stored_File::from_db($m_content->stored_file_id_2);
			$result->pr_additional_file_2 = $this->common()->url($file2->url());
		}
		
		if ($m_content->web_video_id)
		{
			$result->pr_youtube_video = Video::get_instance(Video::PROVIDER_YOUTUBE,
				$m_content->web_video_id)->url();
		}
		
		$company_data = $this->find_company($m_content->company_id);
		foreach ($company_data as $k => $v)
			$result->{$k} = $v;
		return $result;
	}
	
	protected function not_found_error()
	{
		$this->iella_out->errors[] = 'not found';
		$this->iella_out->success = false;
	}
	
	public function report()
	{
		if (!isset($this->iella_in->pr_id))
		{
			$this->iella_out->errors[] = '<pr_id> field is required';
			$this->iella_out->success = false;
			return;
		}
		
		$content_id = $this->iella_in->pr_id;
		$m_content = Model_Content::find($content_id);
		if (!$m_content) return $this->not_found_error();
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
		if ($m_newsroom->user_id != Auth::user()->id)
			return $this->not_found_error();
		
		$url = "manage/analyze/content/report_index/{$content_id}";
		$url = $m_newsroom->url($url);
		$report = new PDF_Generator($url);
		$report->generate();
		$download = $report->indirect();
		$this->iella_out->url = $download;
	}
	
}

?>
