<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/nr_builder');

class MyNewsDesk_Controller extends NR_Builder {

	public $title = 'Newsroom Builder | MyNewsDesk';

	public function __construct()
	{
		parent::__construct();
		$this->vd->nr_source = Model_Company::SOURCE_MYNEWSDESK;
		$this->nr_source = Model_Company::SOURCE_MYNEWSDESK;
		$this->vd->nr_source_title = 'MyNewsDesk';
	}

	public function index($chunk = 1)
	{	
		$this->redirect("admin/nr_builder/mynewsdesk/all");
	}

	// Pulling on demand all data from
	// MyNewsDesk newsroom for a company	
	public function pull_nr_data($company_id)
	{
		if (empty($company_id))
			$this->redirect("admin/nr_builder/mynewsdesk/all");

		if (!$mnd_company = Model_MyNewsDesk_Company::find('company_id', $company_id))
			$this->redirect("admin/nr_builder/mynewsdesk/all");

		$comp = Model_Company::find($company_id);
		
		if (!$mnd_company->is_pr_list_fetched || !$mnd_company->is_prs_fetched)
			$this->pull_prs($company_id);

		if (!$mnd_company->is_news_list_fetched || !$mnd_company->is_news_fetched)
			$this->pull_news($company_id);

		if (!$mnd_company->is_events_list_fetched || !$mnd_company->is_events_fetched)
			$this->pull_events($company_id);

		if (!$mnd_company->is_contacts_fetched)
			$this->pull_contacts($company_id);

		if (!$mnd_company->is_images_list_fetched || !$mnd_company->is_images_fetched)
			$this->pull_images($company_id);

		$this->vd->newsroom = Model_Newsroom::find($company_id);
		$mnd_company = Model_MyNewsDesk_Company::find(array('company_id', $company_id));
		$this->vd->mynewsdesk_company_id = $mnd_company->id;

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/mynewsdesk/pull_data_status');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	protected function pull_contacts($company_id)
	{
		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			'mynewsdesk',
			'pull_contacts',
			$company_id
		));

		$task->execute();
	}

	protected function pull_prs($company_id)
	{
		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			'mynewsdesk',
			'pull_press_releases',
			$company_id
		));

		$task->execute();
	}

	protected function pull_news($company_id)
	{
		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			'mynewsdesk',
			'pull_news',
			$company_id
		));

		$task->execute();
	}

	protected function pull_images($company_id)
	{
		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			'mynewsdesk',
			'pull_images',
			$company_id
		));

		$task->execute();
	}

	protected function pull_events($company_id)
	{
		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			'mynewsdesk',
			'pull_events',
			$company_id
		));

		$task->execute();
	}


	public function status_poll($mynewsdesk_company_id)
	{
		$response = new stdClass();

		$mnd_c_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company_id);

		$response->finished = $mnd_c_data->is_fetching_completed;
		$response->prs = $mnd_c_data->is_prs_fetched;
		$response->news = $mnd_c_data->is_news_fetched;
		$response->events = $mnd_c_data->is_events_fetched;
		$response->contacts = $mnd_c_data->is_contacts_fetched;
		$response->images = $mnd_c_data->is_images_fetched;

		if ($mnd_c_data->is_prs_fetched == 1 && $mnd_c_data->is_news_fetched == 1
			&& $mnd_c_data->is_events_fetched == 1 && $mnd_c_data->is_contacts_fetched == 1 
			&& $mnd_c_data->is_images_fetched == 1)
		{
			$mnd_c_data->is_fetching_completed = 1;
			$mnd_c_data->save();
		}
		
		return $this->json($response);
	}

	public function instant_edit_save()
	{
		$company_id = $this->input->post("instant_edit_company_id");
		$field_name = $this->input->post("instant_edit_field");
		$field_value = $this->input->post("instant_edit_text");
		$post = $this->input->post();

		if ($company_id && $field_name && $field_name == "about_the_company")
		{
			$c_data = Model_MyNewsDesk_Company_Data::find($company_id);
			$c_data->short_description = value_or_null($post['instant_edit_short_description']);
			$c_data->about_company = value_or_null($post['instant_edit_about_company']);
			$c_data->save();
			$response = "Saved successfully";
		}

		elseif ($company_id && $field_name && $field_name == "address")
		{
			$c_data = Model_MyNewsDesk_Company_Data::find($company_id);
			if (!empty($post['instant_edit_address']))
				$c_data->address = $post['instant_edit_address'];

			if (!empty($post['instant_edit_city']))
					$c_data->city = $post['instant_edit_city'];

			if (!empty($post['instant_edit_state']))
				$c_data->state = $post['instant_edit_state'];

			if (!empty($post['instant_edit_zip']))
				$c_data->zip = $post['instant_edit_zip'];

			if (!empty($post['instant_edit_country_id']))
				$c_data->country_id = $post['instant_edit_country_id'];

			$c_data->save();
			$response = "Saved successfully";
		}

		elseif ($company_id && $field_name && ($field_name == "soc_fb" || $field_name == "soc_twitter" 
				|| $field_name == "soc_gplus" || $field_name == "soc_youtube" || $field_name == "soc_pinterest"))
		{
			$c_data = Model_MyNewsDesk_Company_Data::find($company_id);
			$c_data->{$field_name} = $field_value;

			$feed_status_field = "{$field_name}_feed_status";
			$c_data->{$feed_status_field} = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
			$c_data->save();
			$response = "Saved successfully";
		}

		elseif ($company_id && $field_name && $field_value)
		{
			$c_data = Model_MyNewsDesk_Company_Data::find($company_id);
			$c_data->{$field_name} = $field_value;

			$c_data->save();
			$response = "Saved successfully";
		}
		
		else
			$response = "Save failed";
		//$response .= "val=".$field_value;
		$this->json($response);		
	}	

	protected function fetch_auto_built_nr_results($chunkination, $filter = null, $pre_exist_nr_websites_sql = '')
	{
		if (!$filter) $filter = '1';
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();

		$lang_join = "";
		$lang_select = "";
		$lang_having = "";

		$pr = Model_Content::TYPE_PR;

		$l_join = "INNER JOIN (
						SELECT mynewsdesk_company_id, SUM(ISNULL(language)) 
						FROM nr_pb_mynewsdesk_content mc
						INNER JOIN nr_content c
						ON mc.content_id = c.id
						AND c.type = '{$pr}'
						GROUP BY mynewsdesk_company_id 
						HAVING SUM(ISNULL(language)) = 0
					) AS l_counter ON l_counter.mynewsdesk_company_id = cbc.id

					LEFT JOIN (
						SELECT mynewsdesk_company_id, 
						COUNT(mynewsdesk_company_id) AS num_en_prs
						FROM nr_pb_mynewsdesk_content mc
						INNER JOIN nr_content c
						ON mc.content_id = c.id
						AND c.type = '{$pr}'
						WHERE language = 'en' 
						GROUP BY mynewsdesk_company_id 
					) AS e_counter ON e_counter.mynewsdesk_company_id = cbc.id

					LEFT JOIN (
						SELECT mynewsdesk_company_id,
						COUNT(content_id) AS num_prs
						FROM nr_pb_mynewsdesk_content mc
						INNER JOIN nr_content c
						ON mc.content_id = c.id
						AND c.type = '{$pr}'
						GROUP BY mynewsdesk_company_id
					) AS p_counter ON p_counter.mynewsdesk_company_id = cbc.id";
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('n.company_name', 'n.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		if ($filter_country = $this->input->get('filter_country'))
		{
			$list_filter = new stdClass();
			$list_filter->name = 'country';
			$list_filter->value = $filter_country;
			if (!$list_filter->value)
				$list_filter->value = $filter_country;
			$gstring = array('filter_country' => $filter_country);
			$list_filter->gstring = http_build_query($gstring);
			array_push($this->vd->filters, $list_filter);
			$this->vd->selected_country = $filter_country;
			$filter = "{$filter}  AND cd.country = '{$filter_country}'";
		}


		if ($filter_lang = $this->input->get('filter_lang'))
		{
			$list_filter = new stdClass();
			$list_filter->name = 'Language';
			$list_filter->value = $filter_lang;
			if (!$list_filter->value)
				$list_filter->value = $filter_lang;
			$gstring = array('filter_lang' => $filter_lang);
			$list_filter->gstring = http_build_query($gstring);
			array_push($this->vd->filters, $list_filter);

			$lang_join = $l_join;

			$lang_select = ",p_counter.num_prs AS num_prs,
							e_counter.num_en_prs AS num_en_prs,
							cd.about_company_lang";				
			
			if ($filter_lang == "English")
				$lang_having = " HAVING num_prs = num_en_prs AND
								(ISNULL(NULLIF(cd.about_company_lang, '')) OR cd.about_company_lang = 'en')";
			else
				$lang_having = " HAVING (num_en_prs IS NULL OR num_prs <> num_en_prs)
									OR (cd.about_company_lang IS NOT NULL && cd.about_company_lang <> 'en')";
		}
		

		if ($filter_user = (int) $this->input->get('filter_user'))
		{
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
			$use_additional_tables = true;
		}
		
		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"INNER JOIN nr_user u ON n.user_id = u.id";	

		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				DISTINCT(n.company_id) AS id 
				{$lang_select}
				FROM ac_nr_mynewsdesk_company cbc 
				INNER JOIN ac_nr_mynewsdesk_company_data cd
				ON cd.mynewsdesk_company_id = cbc.id
				INNER JOIN nr_newsroom n 
				ON cbc.company_id = n.company_id
				LEFT JOIN ac_nr_newsroom_claim cl
				ON cl.company_id = cbc.company_id
				AND cl.status='confirmed'
				{$lang_join}
				{$additional_tables}
				{$pre_exist_nr_websites_sql}
				WHERE {$filter} 
				AND cl.id is NULL
				{$lang_having}
				ORDER BY 
				n.company_id DESC {$limit_str}";		
		
		$query = $this->db->query($sql);
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$now = Date::$now->format(DATE::FORMAT_MYSQL);

		$dup_nr_select = "";
		if (!empty($pre_exist_nr_websites_sql))
			$dup_nr_select = "dup_webs.web_counter,";

		$sql = "SELECT n.*,
				n.company_id AS id,
				u.first_name AS o_user_first_name,
				u.last_name AS o_user_last_name,
				cd.email AS o_user_email,
				cd.website,
				cd.country,
				cd.about_company_lang,
				u.id AS o_user_id,
				c.date_first_exported_to_csv,
				c.date_exported_to_csv,				
				ct.token AS token,
				cd.newsroom_url,
				ld.geo_country,
				cd.is_fetching_completed,
				p_counter.num_prs,
				e_counter.num_non_en_prs,
				l_counter.num_lang_checked
				FROM nr_newsroom n
				LEFT JOIN nr_user u 
				ON n.user_id = u.id
				LEFT JOIN ac_nr_mynewsdesk_company c
				ON c.company_id = n.company_id
				LEFT JOIN ac_nr_mynewsdesk_company_data cd
				ON cd.mynewsdesk_company_id = c.id
				LEFT JOIN ac_nr_newsroom_claim_token ct
				ON ct.company_id = c.company_id

				

				LEFT JOIN (
					SELECT geo_country, country_name 
					FROM location_data
					GROUP BY geo_country
				) AS ld ON cd.country = ld.country_name


				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_prs
					FROM nr_pb_mynewsdesk_content mc
					INNER JOIN nr_content c
					ON mc.content_id = c.id
					AND c.type = '{$pr}'
					GROUP BY mynewsdesk_company_id
				) AS p_counter ON p_counter.mynewsdesk_company_id = c.id


				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_non_en_prs
					FROM nr_pb_mynewsdesk_content mc
					INNER JOIN nr_content c
					ON mc.content_id = c.id
					AND c.type = '{$pr}'
					WHERE language IS NOT NULL
					AND language <> 'en'
					GROUP BY mynewsdesk_company_id
				) AS e_counter ON e_counter.mynewsdesk_company_id = c.id

				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_lang_checked
					FROM nr_pb_mynewsdesk_content mc
					INNER JOIN nr_content c
					ON mc.content_id = c.id
					AND c.type = '{$pr}'
					WHERE language IS NOT NULL
					GROUP BY mynewsdesk_company_id
				) AS l_counter ON l_counter.mynewsdesk_company_id = c.id

				WHERE n.company_id IN ({$id_str})
				ORDER BY n.company_id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Newsroom::from_db_all($query);

		$websites = array();
		$company_ids = array();
		foreach ($results as $result)
		{
			$result->flag = $this->find_flag($result->geo_country);
			if (($result->num_lang_checked == $result->num_prs && $result->num_non_en_prs) || 
				(!empty($result->about_company_lang) && $result->about_company_lang <> 'en'))
				$result->is_non_en = 1;
			
			elseif ($result->num_lang_checked == $result->num_prs)
				$result->is_en = 1;

			$websites[] = $result->website;
			$company_ids[] = $result->id;
		}

		$web_list = sql_in_list($websites);
		$comp_id_list = sql_in_list($company_ids);

		$sql = "SELECT website
				FROM nr_company_profile
				WHERE website IN ({$web_list})
				AND company_id NOT IN ({$comp_id_list})";

		$query = $this->db->query($sql);
		$profiles = Model_Company_Profile::from_db_all($query);

		$dup_webs = array();
		foreach ($profiles as $profile)
			$dup_webs[] = $profile->website;

		foreach ($results as $result)
			if (in_array($result->website, $dup_webs))
				$result->is_dup_website = 1;
		
		return $results;
	}

	public function paid_claims($chunk = 1)
	{
		$this->vd->heading = "Paid Claims (MyNewsDesk)";
		parent::paid_claims($chunk);
	}

	protected function render_claim_submissions_list($chunkination, $results)
	{
		$this->vd->heading = "Claim Submissions (MyNewsDesk)";
		parent::render_claim_submissions_list($chunkination, $results);
	}

	public function export_auto_built_nrs_to_csv()
	{
		$is_inc_contact_url = 1;
		parent::export_auto_built_nrs_to_csv($is_inc_contact_url);
	}

	protected function render_verified_submissions_list($chunkination, $results)
	{
		$this->vd->heading = "Claimed Submissions (MND)";
		parent::render_verified_submissions_list($chunkination, $results);
	}

	protected function render_auto_built_nr_list($chunkination, $results)
	{
		$sql = "SELECT DISTINCT(country) AS name 
				FROM ac_nr_mynewsdesk_company_data cd 
				INNER JOIN ac_nr_mynewsdesk_company c 
				ON cd.mynewsdesk_company_id = c.id 
				WHERE c.company_id IS NOT NULL
				ORDER BY country";

		$query = $this->db->query($sql);
		$countries = Model_MyNewsDesk_Company_Data::from_db_all($query);

		$this->vd->countries = $countries;

		

		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->add_sales_agent_modal();

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view("admin/nr_builder/mynewsdesk/auto_built_newsrooms");
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function edit($id = null, $modal = 0)
	{
		if (!$id)
			$this->redirect('admin/nr_builder/mynewsdesk');

		if ($this->input->post('save'))
		{
			$this->edit_save($id);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Company detail updated successfully.');
			$this->add_feedback($feedback);
			$this->redirect("admin/nr_builder/mynewsdesk/edit/{$id}");
		}

		$comp = Model_MyNewsDesk_Company::find($id);
		$c_data = Model_MyNewsDesk_Company_Data::find($id);
		$this->vd->comp = $comp;
		$this->vd->c_data = $c_data;
		$order = array('name', 'asc');
		$criteria = array();
		$this->vd->countries = Model_Country::find_all($criteria, $order);

		if ($modal == 1)
			$this->load->view('admin/nr_builder/mynewsdesk/edit_with_modal');
		else
		{
			$this->load->view('admin/header');
			$this->load->view('admin/nr_builder/menu');
			$this->load->view('admin/pre-content');
			$this->load->view('admin/nr_builder/mynewsdesk/edit');
			$this->load->view('admin/post-content');
			$this->load->view('admin/footer');
		}
		
	}

	public function edit_save($id)
	{
		if (!$id || ! $this->input->post('name'))
			$this->redirect('admin/nr_builder/mynewsdesk');

		$post = $this->input->post();
		$comp = Model_MyNewsDesk_Company::find($id);
		$comp->name = $post['name'];
		$comp->save();

		$response = array();	

		$c_data = Model_MyNewsDesk_Company_Data::find($id);
		$c_data->email = value_or_null($post['email']);
		$c_data->website = $post['website'];
		$c_data->short_description = $post['short_description'];
		$c_data->about_company = $post['about_company'];
		$c_data->logo_image_path = $post['logo_image_path'];
		$c_data->address = $post['address'];
		$c_data->city = $post['city']; 
		$c_data->state = $post['state'];
		$c_data->zip = $post['zip'];
		$c_data->country_id = $post['country_id'];
		$c_data->phone = $post['phone'];

		$c_data->soc_fb = $post['soc_fb'];
		$response = array();
		$invalid_socials = array();
		$warning_msg = '';
		if ($c_data->soc_fb)
		{
			if (Social_Facebook_Feed::is_valid($c_data->soc_fb))
				$c_data->soc_fb_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_fb} (Facebook)";
				$invalid_socials[] = "soc_fb";
				$c_data->soc_fb = null;
				$c_data->soc_fb_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_twitter = $post['soc_twitter'];

		if ($c_data->soc_twitter)
		{
			if (Social_Twitter_Feed::is_valid($c_data->soc_twitter))
				$c_data->soc_twitter_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_twitter} (Twitter)";
				$invalid_socials[] = 'soc_twitter';
				$c_data->soc_twitter = null;
				$c_data->soc_twitter_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_gplus = $post['soc_gplus'];

		if ($c_data->soc_gplus)
		{
			if (Social_GPlus_Feeds::is_valid($c_data->soc_gplus))
				$c_data->soc_gplus_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_gplus} (Google Plus)";
				$invalid_socials[] = 'soc_gplus';
				$c_data->soc_gplus = null;
				$c_data->soc_gplus_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_linkedin = $post['soc_linkedin'];
		$c_data->soc_youtube = $post['soc_youtube'];

		if ($c_data->soc_youtube)
		{
			if (Social_Youtube_Feed::is_valid($c_data->soc_youtube))
				$c_data->soc_youtube_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_youtube} (Youtube)";
				$invalid_socials[] = 'soc_youtube';
				$c_data->soc_youtube = null;
				$c_data->soc_youtube_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_pinterest = $post['soc_pinterest'];

		if ($c_data->soc_pinterest)
		{
			if (Social_Pinterest_Feed::is_valid($c_data->soc_pinterest))
				$c_data->soc_pinterest_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_pinterest} (Pinterest)";
				$invalid_socials[] = 'soc_pinterest';
				$c_data->soc_pinterest = null;
				$c_data->soc_pinterest_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->blog_url = $post['blog_url'];
		$c_data->blog_rss = $post['blog_rss'];

		if ($c_data->blog_rss)
		{
			$sxml = simplexml_load_file($c_data->blog_rss);
			if ( ! count($sxml->channel->item) && ! count($sxml->entry))
			{					
				$warning_msg = "{$warning_msg} <br> Unable to validate the following blog RSS URL: ".
								$c_data->blog_rss;
				$invalid_socials[] = 'blog_rss';
				$c_data->blog_rss = "";
			}
		}

		$c_data->save();
		$response['status'] = 1;		
			
		$response['warning_msg'] = $warning_msg;
		$response['invalid_socials'] = $invalid_socials;
		$this->json(array('response' => $response));
	}

	protected function fetch_results($chunkination, $filter = null)
	{
		if (!$filter) $filter = 1;
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();

		$order = array('name', 'asc');
		$criteria = array();
		$this->vd->countries = Model_Country::find_all($criteria, $order);

		if ($filter_category = $this->input->get('filter_category'))
		{
			$cat = Model_MyNewsDesk_Category::find($filter_category);
			$list_filter = new stdClass();
			$list_filter->name = 'category';
			$list_filter->value = $cat->name;
			if (!$list_filter->value)
				$list_filter->value = $filter_category;
			$gstring = array('filter_category' => $filter_category);
			$list_filter->gstring = http_build_query($gstring);
			array_push($this->vd->filters, $list_filter);
			$this->vd->selected_cat_id = $filter_category;
			$filter = "{$filter} AND c.mynewsdesk_category_id = {$filter_category}";
		}

		if ($filter_country = $this->input->get('filter_country'))
		{
			$list_filter = new stdClass();
			$list_filter->name = 'country';
			$list_filter->value = $filter_country;
			if (!$list_filter->value)
				$list_filter->value = $filter_country;
			$gstring = array('filter_country' => $filter_country);
			$list_filter->gstring = http_build_query($gstring);
			array_push($this->vd->filters, $list_filter);
			$this->vd->selected_country = $filter_country;
			$filter = "{$filter} AND cd.country = '{$filter_country}'";
		}

		$email_condition = "";
		$lang_join = "";
		$lang_having = "";
		$lang_select = "";
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);

			$condition_social = "(ISNULL(NULLIF(cd.soc_fb,'')) + ISNULL(NULLIF(cd.soc_twitter,'')) + 
								ISNULL(NULLIF(cd.soc_gplus,'')) + ISNULL(NULLIF(cd.soc_youtube,'')) + 
								ISNULL(NULLIF(cd.soc_pinterest,''))) <= 4
								AND (ISNULL(NULLIF(cd.soc_fb_feed_status, 'valid')) +
									ISNULL(NULLIF(cd.soc_twitter_feed_status, 'valid')) +
									ISNULL(NULLIF(cd.soc_gplus_feed_status, 'valid')) +
									ISNULL(NULLIF(cd.soc_youtube_feed_status, 'valid')) + 
									ISNULL(NULLIF(cd.soc_pinterest_feed_status, 'valid')) >= 1)";
			
			$condition_social_miss = "(ISNULL(NULLIF(cd.soc_fb,'')) + ISNULL(NULLIF(cd.soc_twitter,'')) + 
								ISNULL(NULLIF(cd.soc_gplus,'')) + ISNULL(NULLIF(cd.soc_youtube,'')) + 
								ISNULL(NULLIF(cd.soc_pinterest,''))) >= 5";
			
			$condition_website = "NULLIF(cd.website, '') IS NOT NULL";
			$condition_email = "NULLIF(cd.email, '') IS NOT NULL";
			$condition_logo = "NULLIF(cd.logo_image_path, '') IS NOT NULL";
			$condition_logo_valid = "is_logo_valid = 1";
			$condition_logo_null = "NULLIF(cd.logo_image_path, '') IS NULL";
			$condition_website_valid = "cd.is_website_valid";
			$condition_name = "NOT ISNULL(NULLIF(c.name, ''))";

			$filter_ready = "{$filter} AND {$condition_website}";
			$filter_ready = "{$filter_ready} AND {$condition_email}";
			$filter_ready = "{$filter_ready} AND {$condition_social}";
			$filter_ready = "{$filter_ready} AND {$condition_website_valid}";
			$filter_ready = "{$filter_ready} AND {$condition_name}";

			$l_join = "INNER JOIN (
							SELECT mynewsdesk_company_id, SUM(ISNULL(language)) 
							FROM nr_pb_mynewsdesk_content 
							GROUP BY mynewsdesk_company_id 
							HAVING SUM(ISNULL(language)) = 0
						) AS l_counter ON l_counter.mynewsdesk_company_id = c.id

						LEFT JOIN (
							SELECT mynewsdesk_company_id, 
							COUNT(language) AS num_en_prs
							FROM nr_pb_mynewsdesk_content 
							WHERE language = 'en' 
							GROUP BY mynewsdesk_company_id 
						) AS e_counter ON e_counter.mynewsdesk_company_id = c.id

						LEFT JOIN (
							SELECT mynewsdesk_company_id,
							COUNT(content_id) AS num_prs
							FROM nr_pb_mynewsdesk_content 
							GROUP BY mynewsdesk_company_id
						) AS p_counter ON p_counter.mynewsdesk_company_id = c.id";

			if ($filter_search == "CHECK_LOGO")	
			{
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND {$condition_email}";
				$filter = "{$filter} AND {$condition_logo}";
				$filter = "{$filter} AND NOT {$condition_logo_valid}";
				$filter = "{$filter} AND {$condition_social}";

			}

			elseif ($filter_search == "CHECK_WEBSITE")	
			{
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND NOT {$condition_website_valid}";
			}

			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_NEW")	
			{
				$this->vd->search_ready_newsrooms = 1;				
				
				$filter = "{$filter_ready} AND c.id IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_OLD")	
			{
				$this->vd->search_ready_newsrooms = 1;

				$filter = "{$filter_ready} AND c.id NOT IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_EN_NEW")
			{
				$this->vd->search_ready_newsrooms = 1;
				
				$filter = "{$filter_ready} AND c.id IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$lang_join = $l_join;
				
				$lang_select = ",p_counter.num_prs AS num_prs,
								e_counter.num_en_prs AS num_en_prs,
								cd.about_company_lang";				
				
				$lang_having = " AND num_prs = num_en_prs AND
								(ISNULL(NULLIF(cd.about_company_lang, '')) OR cd.about_company_lang = 'en')";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_EN_OLD")
			{
				$this->vd->search_ready_newsrooms = 1;
				
				$filter = "{$filter_ready} AND c.id NOT IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";


				$lang_join = $l_join;
				
				$lang_select = ",p_counter.num_prs AS num_prs,
								e_counter.num_en_prs AS num_en_prs,
								cd.about_company_lang";				
				
				$lang_having = " AND num_prs = num_en_prs AND
								(ISNULL(NULLIF(cd.about_company_lang, '')) OR cd.about_company_lang = 'en')";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_NON_EN_NEW")
			{
				$this->vd->search_ready_newsrooms = 1;				
							
				$filter = "{$filter_ready} AND c.id IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";

				$lang_join = $l_join;
				
				$lang_select = ",p_counter.num_prs AS num_prs,
								e_counter.num_en_prs AS num_en_prs,
								cd.about_company_lang";				
				
				$lang_having = " AND (num_prs <> num_en_prs
									OR (cd.about_company_lang IS NOT NULL && cd.about_company_lang <> 'en'))";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_NON_EN_OLD")
			{
				$this->vd->search_ready_newsrooms = 1;				
							
				$filter = "{$filter_ready} AND c.id NOT IN (
								SELECT mynewsdesk_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_mynewsdesk_content p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";

				$lang_join = $l_join;
				
				$lang_select = ",p_counter.num_prs AS num_prs,
								e_counter.num_en_prs AS num_en_prs,
								cd.about_company_lang";				
				
				$lang_having = " AND (num_prs <> num_en_prs
									OR (cd.about_company_lang IS NOT NULL && cd.about_company_lang <> 'en'))";
			}

			else if ($filter_search == "ONLY_MISSING_LOGO_NEWSROOMS")
			{
				$this->vd->search_logo_missing_nrs = 1;
				
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND {$condition_email}";
				$filter = "{$filter} AND NULLIF(cd.logo_image_path, '') IS NULL";
				$filter = "{$filter} AND {$condition_social}";
			}

			else if ($filter_search == "ONLY_MISSING_SOCIALS_NEWSROOMS")
			{
				$this->vd->search_ready_newsrooms = 1;
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND {$condition_email}";
				$filter = "{$filter} AND {$condition_logo}";
				$filter = "{$filter} AND {$condition_social_miss}";
			}

			else if ($filter_search == "MISSING_LOGO_N_SOCIALS_NEWSROOMS")
			{
				$this->vd->search_ready_newsrooms = 1;
				
				$filter = "{$filter} AND cd.website IS NOT NULL AND cd.website <> ''";
				$filter = "{$filter} AND cd.email IS NOT NULL AND cd.email <> ''";
				$filter = "{$filter} AND NULLIF(cd.logo_image_path, '') IS NULL";
				$filter = "{$filter} AND (cd.soc_fb IS NULL OR cd.soc_fb = '' 
							OR cd.soc_twitter IS NULL OR cd.soc_twitter = '' )";
			}

			elseif ($filter_search == "ONLY_MISSING_EMAIL_NEWSROOMS")
			{
				$this->vd->search_ready_newsrooms = 1;
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND NULLIF(cd.email, '') IS NULL";
				$filter = "{$filter} AND {$condition_logo}";
				$filter = "{$filter} AND {$condition_social}";
			}

			elseif ($filter_search == "DUPLICATE_EMAIL_NEWSROOMS")
			{
				$this->vd->search_ready_newsrooms = 1;
				$email_condition = "INNER JOIN nr_user_base u ON u.email = cd.email";
			}

			else
			{
				$search_fields = array('c.name');
				$terms_filter = sql_search_terms($search_fields, $filter_search);
				$filter = "{$filter} AND {$terms_filter}";
			}
		}

		if (count($this->vd->filters))
		{
			foreach ($this->vd->filters as $q_filter)
			{
				if ($q_filter->name == "category")
					$this->vd->category_filter =  "&{$q_filter->gstring}";
				if ($q_filter->name == "search")
					$this->vd->search_filter =  "&{$q_filter->gstring}";
			}
		}
				
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				cd.mynewsdesk_company_id as id
				{$lang_select}
				FROM ac_nr_mynewsdesk_company c
				LEFT JOIN ac_nr_mynewsdesk_company_data cd 
				ON cd.mynewsdesk_company_id = c.id 
				{$email_condition}
				{$lang_join}
				WHERE {$filter} 
				AND c.company_id IS NULL
				HAVING cd.mynewsdesk_company_id IS NOT NULL
				{$lang_having}
				ORDER BY c.id DESC
				{$limit_str}";
				
		$query = $this->db->query($sql);
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$now = Date::$now->format(DATE::FORMAT_MYSQL);
		$sql = "SELECT c.name, cd.*, 
				u.email AS inews_user_email,
				p_counter.num_prs,
				ld.geo_country,
				e_counter.num_non_en_prs,
				l_counter.num_lang_checked,
				cd.mynewsdesk_company_id AS source_company_id
				FROM ac_nr_mynewsdesk_company c
				LEFT JOIN ac_nr_mynewsdesk_company_data cd 
				ON cd.mynewsdesk_company_id = c.id 
				LEFT JOIN nr_user_base u 
				ON u.email = cd.email

				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_prs
					FROM nr_pb_mynewsdesk_content 
					GROUP BY mynewsdesk_company_id
				) AS p_counter ON p_counter.mynewsdesk_company_id = c.id

				LEFT JOIN (
					SELECT geo_country, country_name 
					FROM location_data
					GROUP BY geo_country
				) AS ld ON cd.country = ld.country_name

				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_non_en_prs
					FROM nr_pb_mynewsdesk_content
					WHERE language IS NOT NULL
					AND language <> 'en'
					GROUP BY mynewsdesk_company_id
				) AS e_counter ON e_counter.mynewsdesk_company_id = c.id

				LEFT JOIN (
					SELECT mynewsdesk_company_id,
					COUNT(content_id) AS num_lang_checked
					FROM nr_pb_mynewsdesk_content
					WHERE language IS NOT NULL
					GROUP BY mynewsdesk_company_id
				) AS l_counter ON l_counter.mynewsdesk_company_id = c.id


				WHERE c.id IN ({$id_str})
				ORDER BY c.id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Newsroom::from_db_all($query);

		foreach ($results as $result)
		{
			$soc_count = 0;
			$soc_valid_count = 0;
			if ($result->soc_fb)
				$soc_count++;
			if ($result->soc_twitter)
				$soc_count++;
			if ($result->soc_gplus)
				$soc_count++;
			if ($result->soc_youtube)
				$soc_count++;
			if ($result->soc_pinterest)
				$soc_count++;

			if ($result->soc_fb_feed_status == Model_MyNewsDesk_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_twitter_feed_status == Model_MyNewsDesk_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_gplus_feed_status == Model_MyNewsDesk_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_youtube_feed_status == Model_MyNewsDesk_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_pinterest_feed_status == Model_MyNewsDesk_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			$company_name = trim($result->name);
			if ($result->website && $result->email && $soc_count >= 1 && 
				$soc_valid_count >= 1  && $result->is_website_valid && !empty($company_name))
					$result->is_ready_to_build = 1;

			$result->flag = $this->find_flag($result->geo_country);

			if (($result->num_lang_checked == $result->num_prs && $result->num_non_en_prs) || 
				(!empty($result->about_company_lang) && $result->about_company_lang <> 'en'))
				$result->is_non_en = 1;
			
			elseif ($result->num_lang_checked == $result->num_prs)
				$result->is_en = 1;
		}


		$prs_modal = new Modal();
		$prs_modal->set_title("Company's MyNewsDesk PRs");
		$this->add_eob($prs_modal->render(500, 230));
		$this->vd->prs_modal_id = $prs_modal->id;

		$contacts_modal = new Modal();
		$contacts_modal->set_title("Company's Contacts");
		$this->add_eob($contacts_modal->render(700, 400));
		$this->vd->contacts_modal_id = $contacts_modal->id;

		return $results;
	}

	public function contacts($mynewsdesk_company_id)
	{
		$sql = "SELECT c.*, mc.area_of_specialization,
				is_press_contact, image_url
				FROM ac_nr_mynewsdesk_contact mc
				INNER JOIN nr_company_contact c
				ON mc.company_contact_id = c.id
				WHERE mc.mynewsdesk_company_id = {$mynewsdesk_company_id}
				ORDER BY mc.is_press_contact DESC,
				c.id";

		$query = $this->db->query($sql);
		$results = Model_MyNewsDesk_Contact::from_db_all($query);
		$this->vd->results = $results;

		$m_mw_comp = Model_MyNewsDesk_Company::find($mynewsdesk_company_id);
		$this->vd->company_name = $m_mw_comp->name;
		$this->load->view('admin/nr_builder/mynewsdesk/contacts');

	}

	public function pr_links($mynewsdesk_company_id)
	{
		$sql = "SELECT c.title, p.url, c.id
				FROM nr_content c
				INNER JOIN nr_pb_mynewsdesk_content p
				ON p.content_id = c.id
				WHERE p.mynewsdesk_company_id = {$mynewsdesk_company_id}";

		$query = $this->db->query($sql);
		$results = Model_PB_MyNewsDesk_Content::from_db_all($query);
		$this->vd->results = $results;

		$m_mw_comp = Model_MyNewsDesk_Company::find($mynewsdesk_company_id);
		$this->vd->company_name = $m_mw_comp->name;
		$this->load->view('admin/nr_builder/mynewsdesk/pr_links');

	}

	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;	

		$this->vd->cb_cat = Model_MyNewsDesk_Category::find_all();

		$sql = "SELECT DISTINCT(country) AS name
				FROM ac_nr_mynewsdesk_company_data
				ORDER BY country";

		$query = $this->db->query($sql);
		$countries = Model_MyNewsDesk_Company_Data::from_db_all($query);

		$this->vd->countries = $countries;
		
		$company_modal = new Modal();
		$company_modal->set_title('Edit Company Data');
		$this->add_eob($company_modal->render(970, 430));
		$this->vd->company_modal_id = $company_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/mynewsdesk/main');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function retry_logo()
	{
		if ( ! $id = $this->input->post("id"))
			return false;

		if (! $comp = Model_MyNewsDesk_Company::find("company_id", $id))
			return false;

		$c_data = Model_MyNewsDesk_Company_Data::find($comp->id);
		
		$nr_custom = Model_Newsroom_Custom::find($id);

		// fetching and setting the logo
		if (!empty($c_data->logo_image_path))
		{
			$logo_file = "logo";
			$logo_url = $c_data->logo_image_path;
			@copy($logo_url, $logo_file);

			if (Image::is_valid_file($logo_file))
			{
				// import the logo image into the system
				$logo_im = LEGACY_Image::import("logo", $logo_file);
				 
				// assign to the new company and save
				$logo_im->company_id = $id;
				$logo_im->save();
				 
				// set it to use the new logo image and save
				$nr_custom->logo_image_id = $logo_im->id;
			}
		}
		
		$nr_custom->save();

		$nr_custom2 = Model_Newsroom_Custom::find($id);
		if ($nr_custom2->logo_image_id)
			$success = 1;
		else
			$success = 0;
		$this->json($success);
	}

	public function update_logo_status()
	{
		$mynewsdesk_company_id = $this->input->post('mynewsdesk_company_id');
		$is_logo_valid = $this->input->post('is_logo_valid');

		if (!$c_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company_id))
			$this->json(0);
		
		else
		{
			if ($is_logo_valid)
				$c_data->is_logo_valid = 1;
			else
				$c_data->logo_image_path = null;

			$c_data->save();

			$this->json(1);
		}
	}

	public function update_web_status()
	{
		$mynewsdesk_company_id = $this->input->post('mynewsdesk_company_id');
		$is_website_valid = $this->input->post('is_website_valid');

		if (!$mynewsdesk_comp = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company_id))
			$this->json(0);
		
		else
		{
			if ($is_website_valid)
				$mynewsdesk_comp->is_website_valid = 1;
			else
			{
				$mynewsdesk_comp->website = null;
				$mynewsdesk_comp->is_website_valid = 0;
				$mynewsdesk_comp->website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_NONE;
				$mynewsdesk_comp->name = null;
			}

			$mynewsdesk_comp->save();

			$this->json(1);
		}
	}

	public function generate_tokens_for_all_generated_nrs()
	{
		$sql = "SELECT n.company_id AS company_id FROM 
				ac_nr_mynewsdesk_company pc 
				INNER JOIN 
				nr_newsroom n 
				ON pc.company_id = n.company_id
				INNER JOIN nr_newsroom_custom nc
				ON nc.company_id = n.company_id 
				WHERE 1 ORDER BY 
				n.company_id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_MyNewsDesk_Company::from_db_all($query);
		foreach ($results as $result)
		{
			$token = new Model_MyNewsDesk_NR_Claim_Token();
			$token->company_id = $result->company_id;
			$token->generate();
			$token->save();
		}
	}


	protected function find_flag($geo_country)
	{
		$relative = sprintf('assets/im/flags/%s.png', $geo_country);
		if (!is_file(sprintf('%s', $relative)))
		     return 'assets/im/globe.png';
		else return $relative;
	}

	
}

?>