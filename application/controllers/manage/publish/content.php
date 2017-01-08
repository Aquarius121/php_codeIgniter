<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/listing');

class Content_Base extends Listing_Base {
	
	protected $listing_section = 'publish';
	protected $edit_full_details = false;
	protected $content_type = null;

	public function __construct()
	{
		parent::__construct();
		$this->vd->content_type = $this->content_type;
	}
	
	public function edit_bundled($content_id)
	{
		$this->edit_full_details = true;
		$this->edit($content_id);
	}

	public function fork($content_id)
	{
		$feedback = new Feedback('info');
		$feedback->set_title('Attention!');
		$feedback->set_text('Content will be forked on next save.');
		$this->use_feedback($feedback);
		$this->vd->duplicate = true;
		$this->edit($content_id);
	}
	
	protected function edit($content_id = 0)
	{
		$company_id = (int) $this->newsroom->company_id;
		$m_content = null;
		
		if ($content_id)
		{
			$this->vd->m_content = $m_content = Model_Content::find($content_id);
			if (!$m_content) $this->denied();
			if ($m_content->is_under_writing &&
			    !Auth::user()->is_reseller &&
			    !Auth::is_admin_online())
				$this->denied();
			
			if ((int) $m_content->company_id !== $company_id) 
			{
				$m_desired_newsroom = Model_Newsroom::find_company_id($m_content->company_id);
				if ($m_desired_newsroom->user_id != Auth::user()->id)
					$this->denied();
				$url = $m_desired_newsroom->url($this->uri->uri_string);
				$this->redirect(gstring($url), false);
			}
			
			$m_content->load_local_data();
			$m_content->load_content_data();

			if ($m_content->is_untitled())
				$m_content->title = null;
		}
		
		if ($m_content)
			  $title_type = Model_Content::full_type($m_content->type);
		else $title_type = Model_Content::full_type($this->uri->segment(3));
		array_pop($this->vd->title);
		
		if ($m_content && $m_content->title)
			  $this->title = $m_content->title;
		else if ($m_content) $this->title = "Edit {$title_type}";
		else $this->title = "Add {$title_type}";
		
		$recent_tags = Model_Content::recent_tags($company_id, 5);
		$this->vd->recent_tags = $recent_tags;
		
		$beats = Model_Beat::list_all_beats_by_group();
		$this->vd->beats = $beats;
		
		$twitter_auth = Social_Twitter_Auth::find($company_id);
		$facebook_auth = Social_Facebook_Auth::find($company_id);
		
		$this->vd->social = new stdClass();
		$this->vd->social->twitter = $twitter_auth && $twitter_auth->is_valid();
		$this->vd->social->facebook = $facebook_auth && $facebook_auth->is_valid();
		
		// attempt to renew facebook token
		if ($this->vd->social->facebook)
			$facebook_auth->renew_if_needed();
		
		if ($m_content)
			$m_content->date_publish_str = 
				Date::out($m_content->date_publish)
				->format('Y-m-d H:i');
		
		return get_defined_vars();
	}
	
	protected function edit_save($content_type)
	{
		// failed required.js validation 
		Required_JS_Enforcer::enforce();	
		
		$content_id = (int) $this->input->post('id');
		$is_new_content = !$content_id;
		$company_id = (int) $this->newsroom->company_id;
		$post = Raw_Data::from_array($this->input->post());
		$is_preview = (bool) $post->is_preview;
		$m_content = null;
		
		if ($content_id)
		{		
			$m_content = Model_Content::find($content_id);
			if (!$m_content || (int) $m_content->company_id !== $company_id) 
				$this->denied();
			if ($m_content->is_under_writing)
				$this->denied();
			$m_content->load_local_data();
			$m_content->load_content_data();
		}
		
		if (empty($post->date_publish))
		{
			if ($m_content)
			     $dt_date_publish = Date::utc($m_content->date_publish);
			else $dt_date_publish = Date::$now;
		}		     
		else
		{
			$dt_date_publish = Date::in($post->date_publish);
		}

		// handle the case where user provided bad input
		if (!$dt_date_publish) 
		{
			$dt_date_publish = Date::$now;
			$feedback = new Feedback('warning');
			$feedback->set_title('Warning!');
			$feedback->set_text('Unsuitable date format.');
			$this->add_feedback($feedback);
		}
		
		$content = value_or_null($this->vd->pure($post->content));
		$summary_max_length = $this->conf('summary_max_length');
		$summary = value_or_null(substr($post->summary, 0, $summary_max_length));
		$title_max_length = $this->conf('title_max_length');
		$title = substr(trim($post->title), 0, $title_max_length);
		$post_to_facebook = (bool) $post->post_to_facebook;
		$post_to_twitter = (bool) $post->post_to_twitter;

		$is_backdated = false;
		if ($m_content) $is_backdated = (bool) $m_content->is_backdated;
		if ($this->input->post('is_backdated'))
			$is_backdated = true;
		
		// slug is initially set to current value
		if ($m_content) $slug = $m_content->slug;
		
		$is_draft = (bool) $post->is_draft;
		$is_premium = (bool) $post->is_premium;
		$is_published = false;
		
		if (!$title) 
		{
			// we should always have title
			// but generate one to be safe
			$title = Model_Content::__untitled();
			$is_draft = true;
		}
		
		// cannot change the type of press release
		// after it has been published once or when 
		// it is under review currently
		if ($m_content && $m_content->is_consume_locked())
			$is_premium = $m_content->is_premium;
		
		// already published? ignore request to 
		// save as draft as that is not an option
		if ($m_content && $m_content->is_published)
		{
			$is_published = true;
			$is_draft = false;
		}
		
		// if it has been approved but isn't premium
		// then we must approve it again so not published
		if ($m_content && ($m_content->is_approved || $m_content->is_published)
		&& !$m_content->is_premium && !Auth::is_admin_mode())
		{
			$m_content->is_approved = 0;
			$is_published = false;
		}
		
		// if it has been published before then 
		// we maintain the current publish date 
		if ($m_content && $m_content->is_identity_locked)
		{
			// ... but allow the date to be changed with unlock
			if (!$this->input->post('date_publish_unlock') && !$is_backdated)
				$dt_date_publish = Date::utc($m_content->date_publish);

			if (!$m_content->is_premium && $is_premium)
			{
				$dt_date_publish = Date::$now;
				$feedback = new Feedback('info');
				$feedback->set_title('Notice!');
				$feedback->set_text('Publish date has been modified for distribution.');
				$this->add_feedback($feedback);
			}
		}

		// * can only change slug if never published before
		// * can only set socials if never published
		// * legacy is prevented from doing it
		else if (!$m_content || ($m_content && !$m_content->is_legacy))
		{
			$slug = Model_Content::generate_slug($title, (($m_content ? 
				$m_content->id : null)), $content_type);
		}
		
		if ($m_content && $m_content->is_social_locked_facebook)
			$post_to_facebook = (bool) $m_content->post_to_facebook;
		if ($m_content && $m_content->is_social_locked_twitter)
			$post_to_twitter = (bool) $m_content->post_to_twitter;

		// scheduled just now (not a draft)
		$is_new_scheduled = (!$m_content || 
			$m_content->is_draft) && !$is_draft;

		$supporting_quote_name = value_or_null($post->supporting_quote_name);
		$supporting_quote_title = value_or_null($post->supporting_quote_title);
		$supporting_quote = value_or_null($post->supporting_quote);
		$rel_res_pri_title = value_or_null($post->rel_res_pri_title);
		$rel_res_pri_link = value_or_null($post->rel_res_pri_link);
		$rel_res_sec_title = value_or_null($post->rel_res_sec_title);
		$rel_res_sec_link = value_or_null($post->rel_res_sec_link);
		$rel_res_pri_link = URL::safe($rel_res_pri_link);
		$rel_res_sec_link = URL::safe($rel_res_sec_link);
		$tags = explode(chr(44), $post->tags);
		$images = array();
			
		foreach ((array) $post->image_ids as $k => $image_id)
		{
			if (!($image = Model_Image::find(value_or_null($image_id)))) continue;
			$meta_data = $image->raw_data_object('meta_data');
			if (!empty($post['image_meta_data']['alt'][$k]))
				$meta_data->alt = $post['image_meta_data']['alt'][$k];
			if (!empty($post['image_meta_data']['caption'][$k]))
				$meta_data->caption = $post['image_meta_data']['caption'][$k];
			$image->raw_data_write('meta_data', $meta_data);
			$images[$k] = $image;
			$image->save();
		}
		
		$cover_image_id = value_or_null($post->cover_image_id);
		$cover_image = Model_Image::find($cover_image_id);
		if (!$cover_image || (int) $cover_image->company_id !== $company_id) 
			$cover_image_id = null;

		$beats = array();
		foreach ((array) $post->beats as $k => $beat_id)
			if ($beat_id) $beats[] = (int) $beat_id;

		if ($is_preview)
		{
			if ($m_content)
			     $m_content = Model_Detached_Content::from_object($m_content->values());
			else $m_content = new Model_Detached_Content();
			$m_content->date_publish = $dt_date_publish->format(Date::FORMAT_MYSQL);
			if ($is_new_content) $m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->date_updated = Date::$now->format(Date::FORMAT_MYSQL);
			if (!$m_content->slug && isset($slug)) 
				$m_content->slug = $slug;
			$m_content->cover_image_id = $cover_image_id;
			$m_content->is_published = $is_published;
			$m_content->is_premium = $is_premium;
			$m_content->company_id = $company_id;
			$m_content->type = $content_type;
			$m_content->is_draft = !$is_published;
			$m_content->title = $title;
			
			$m_content->content = $content;
			$m_content->summary = $summary;
			$m_content->supporting_quote = $supporting_quote;
			$m_content->supporting_quote_name = $supporting_quote_name;
			$m_content->supporting_quote_title = $supporting_quote_title;
			$m_content->rel_res_pri_title = $rel_res_pri_title;
			$m_content->rel_res_pri_link = $rel_res_pri_link;
			$m_content->rel_res_sec_title = $rel_res_sec_title;
			$m_content->rel_res_sec_link = $rel_res_sec_link;

			$m_content->set_tags((array) $tags);
			$m_content->set_images((array) $images);
			$m_content->set_beats((array) $beats);
			
			Detached_Session::write('m_content', $m_content);
			
			$url = $m_content->url();
			if ($content_type === Model_Content::TYPE_PR && 
			    !$this->newsroom->is_active)
				  $preview_url = Detached_Session::save($this->common(), $url);
			else $preview_url = Detached_Session::save($this->newsroom, $url);
			$this->set_redirect($preview_url, false);
			
			// capture current context vars
			$defined_vars = get_defined_vars();
			return $defined_vars;
		}
		else
		{
			if ($is_new_content) $m_content = new Model_Content();
			$m_content->date_publish = $dt_date_publish->setSeconds(0)->format(Date::FORMAT_MYSQL);	
			if ($is_new_content)	$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->date_updated = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->cover_image_id = $cover_image_id;
			$m_content->is_published = $is_published;
			$m_content->is_premium = $is_premium;
			$m_content->is_on_credit_hold = 0;
			$m_content->company_id = $company_id;
			$m_content->is_draft = $is_draft;
			$m_content->is_backdated = $is_backdated;
			$m_content->type = $content_type;
			$m_content->title = $title;
			$m_content->slug = $slug;
			$m_content->save();

			if ($is_new_content)
			{
				// may need to regenerate slug now that the id is known
				$slug = Model_Content::generate_slug($title, 
					$m_content->id, $content_type);
				$m_content->slug = $slug;
				$m_content->save();
			}

			if (Auth::is_admin_online() && $_new_slug = $post->slug)
			{
				// generate a new slug, skip availability check
				$_new_slug = Model_Content::generate_slug($_new_slug, 
					$m_content->id, $content_type, false);

				// check for existing content with that slug
				$m_existing = Model_Content::find_slug($_new_slug);

				if ($m_content->slug === $_new_slug)
				{
					// the slug is not changed 
				}
				else if ($m_existing && $m_existing->id != $m_content->id)
				{
					$feedback = new Feedback('warning');
					$feedback->set_title('Warning!');
					$feedback->set_text('The requested slug is already in use.');
					$this->add_feedback($feedback);
				}
				else
				{
					if ($m_content->is_identity_locked)
					{
						Model_Content_Slug_Redirect::update_all_in_chain($m_content->slug, $_new_slug);
						Model_Content_Slug_Redirect::create($m_content->slug, $_new_slug);
					}

					$slug = $_new_slug;
					$m_content->slug = $slug;
					$m_content->save();
				}
			}
			
			if (!$m_content->is_draft)
			{
				// record the events within KM
				$kmec = new KissMetrics_Event_Library();
				$kmec->event_submitted($m_content);
			}
			
			$content_id = $m_content->id;
			
			if ($is_new_content)
				  $m_content_data = new Model_Content_Data();
			else $m_content_data = Model_Content_Data::find($content_id);
			if (!$m_content_data) $m_content_data = new Model_Content_Data();
					
			if (!$post->preserve_original_content)
				$m_content_data->content = $content;
			$m_content_data->summary = $summary;
			$m_content_data->post_to_facebook = $post_to_facebook;
			$m_content_data->post_to_twitter = $post_to_twitter;
			$m_content_data->supporting_quote = $supporting_quote;
			$m_content_data->supporting_quote_name = $supporting_quote_name;
			$m_content_data->supporting_quote_title = $supporting_quote_title;
			$m_content_data->rel_res_pri_title = $rel_res_pri_title;
			$m_content_data->rel_res_pri_link = $rel_res_pri_link;
			$m_content_data->rel_res_sec_title = $rel_res_sec_title;
			$m_content_data->rel_res_sec_link = $rel_res_sec_link;
			$m_content_data->content_id = $content_id;
			$m_content_data->save();
			
			$m_content->set_tags((array) $tags);
			$m_content->set_images((array) $images);
			$m_content->set_beats((array) $beats);
			
			$default_newsroom = Auth::user()->default_newsroom();
			if ($this->newsroom->company_id !== $default_newsroom->company_id)
			{
				$current_time = time();
				$default_newsroom->order_default = $current_time;
				$this->newsroom->order_default = $current_time - 1;
				$default_newsroom->save();
				$this->newsroom->save();
			}

			// update content hashes for dupe checking
			Model_Content_Hash::delete_for_content($m_content->id);
			Model_Content_Hash::insert_for_content($m_content->id, $m_content->title);
			Model_Content_Hash::insert_for_content($m_content->id, strip_tags($m_content_data->content));
			
			// capture current context vars
			$defined_vars = get_defined_vars();
			
			// load feedback message for the user
			$feedback_view = 'manage/publish/partials/feedback/save';
			$feedback = $this->load->view($feedback_view, $defined_vars, true);
			$this->add_feedback($feedback);
		
			// redirect back to type specific listing
			$redirect_url = "manage/publish/{$content_type}/all";
			$this->set_redirect($redirect_url);
			
			return $defined_vars;
		}
	}

	protected function save_deleted($m_content)
	{
		$m_content->load_content_data();
		$m_content->load_local_data();

		$content_change_deleted = Model_Content_Change_Deleted::create();
		$content_change_deleted->content_id = $m_content->id;
		$raw_data = new stdClass();
		$raw_data->content = Model_Detached_Content::from_model_content($m_content);
		$content_change_deleted->raw_data($raw_data);
		$content_change_deleted->save();
	
	}
	
	protected function do_delete($content_id)
	{
		$m_content = Model_Content::find($content_id);
		$owner = $m_content->owner();
		if ($owner->id != Auth::user()->id) $this->denied();
		if ($m_content->is_under_writing) $this->denied();
		$this->save_deleted($m_content);
		$m_content->load_local_data();
		$m_content->load_content_data();
		$m_content->delete();
		return $m_content;
	}
	
	public function delete($content_id)
	{
		if (!$content_id) return;
		$this->vd->type = $type = $this->uri->segment(3);
		if (!Model_Content::is_allowed_type($type))
			return;
		
		if ($this->input->post('confirm'))
		{
			$this->do_delete($content_id);
			
			// load feedback message 
			$feedback_view = 'manage/publish/partials/feedback/delete_after';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			
			// redirect back to type specific listing
			$redirect_url = "manage/publish/{$type}/all";
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->content_id = $content_id;
			$feedback_view = 'manage/publish/partials/feedback/delete_before';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($content_id);
		}
	}
	
	protected function resolve_video($provider, $video_id, $download_thumb = false)
	{
		$this->json(Video::resolve($provider, $video_id, $download_thumb));
	}

	protected function autosave_delete($type, $id)
	{
		$cas = Model_Content_Auto_Save::find($id);
		if (!$cas || $cas->company_id != $this->newsroom->company_id)
			$this->redirect('manage/publish');

		$cas->delete();
		$feedback = new Feedback('success');
		$feedback->set_title('Deleted!');
		$feedback->set_text('The autosave has been deleted.');
		$this->add_feedback($feedback);

		$redirect_url = gstring("manage/publish/{$type}/autosave");
		$this->redirect($redirect_url);
	}

	protected function autosave_create($type)
	{
		$context = (int) $this->input->post('context');

		$form = $this->input->post('form');
		$form = json_decode($form);
		$form->content = $this->vd->pure($form->content);
		unset($form->image_ids);
		unset($form->image_meta_data);
		unset($form->stored_file_id_1);
		unset($form->stored_file_id_2);
		unset($form->stored_file_name_1);
		unset($form->stored_file_name_2);
		unset($form->id);

		$cas = Model_Content_Auto_Save::create();
		$cas->company_id = $this->newsroom->company_id;
		$cas->content_id = value_or_null($context);
		$cas->content_type = $type;
		$cas->raw_data($form);
		$cas->save();

		$this->json(array(
			'success' => true,
			'context' => $context,
			'id' => $cas->id,
		));
	}

	protected function autosave_edit($id)
	{
		$cas = Model_Content_Auto_Save::find($id);
		if (!$cas || $cas->company_id != $this->newsroom->company_id)
			$this->redirect('manage/publish');
		$this->vd->autosave = $cas;
		$this->edit();
	}

	protected function autosave($type, $chunk = 1)
	{
		if (!Model_Content::is_allowed_type($type))
			show_404();
		
		$this->load->view('manage/header');
		$this->vd->type = $type;
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		$company_id = $this->newsroom->company_id;

		$sql = "SELECT SQL_CALC_FOUND_ROWS cas.*
			FROM nr_content_auto_save cas
			WHERE cas.company_id = ?
			AND cas.content_type = ?
			ORDER BY cas.date_created DESC
			{$limit_str}";
		
		$query = $this->db->query($sql, array($company_id, $type));
		$results = Model_Content_Auto_Save::from_db_all($query);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$url_format = gstring("manage/publish/{$type}/autosave/-chunk-");
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		foreach ($results as $result)
			$result->data = $result->raw_data();

		$this->load->view('manage/publish/autosave');
		$this->load->view('manage/footer');
	}

	public function pin()
	{
		$response = new stdClass();
		$content_id = (int) $this->input->get('content_id');
		$m_content = Model_Content::find($content_id);

		if (!$m_content || $m_content->company_id != $this->newsroom->company_id)
		{
			$response->success = false;
			return $this->json($response);
		}		

		$m_pinned_content = Model_Pinned_Content::find($content_id);

		if (!$m_pinned_content)
			$m_pinned_content = new Model_Pinned_Content();

		$m_pinned_content->content_id = $content_id;
		$m_pinned_content->is_pinned = 1;
		$m_pinned_content->date_pinned = Date::$now;
		$m_pinned_content->priority = (int) $this->input->get('priority');
		$m_pinned_content->save();

		$response->success = true;
		return $this->json($response);
	}

	public function pin_remove()
	{
		$response = new stdClass();
		$content_id = (int) $this->input->get('content_id');
		$m_content = Model_Content::find($content_id);

		if (!$m_content || $m_content->company_id != $this->newsroom->company_id)
		{
			$response->success = false;
			return $this->json($response);
		}			

		if ($m_pinned_content = Model_Pinned_Content::find($content_id))
			$m_pinned_content->delete();

		$response->success = true;
		return $this->json($response);
	}

	protected function record_changes(Model_Content $m_content, $other = null)
	{
		$m_content->clear_cached_data();
		$m_content->load_content_data();
		$m_content->load_local_data();

		$content_change = Model_Content_Change::create();
		$content_change->content_id = $m_content->id;
		$raw_data = new stdClass();
		$raw_data->content = Model_Detached_Content::from_model_content($m_content);
		$raw_data->other = $other;
		$raw_data->user_id = Auth::real_user()->id;
		$raw_data->user_name = Auth::real_user()->name();
		$raw_data->user_email = Auth::real_user()->email;
		$raw_data->is_admin_mode = Auth::is_admin_mode();
		$content_change->raw_data($raw_data);
		$content_change->save();
	}
	
}