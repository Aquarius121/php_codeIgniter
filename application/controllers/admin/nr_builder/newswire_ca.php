<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/nr_builder');

class Newswire_CA_Controller extends NR_Builder {

	public $title = 'Newsroom Builder | Newswire CA';

	public function __construct()
	{
		parent::__construct();
		$this->vd->nr_source = Model_Company::SOURCE_NEWSWIRE_CA;
		$this->nr_source = Model_Company::SOURCE_NEWSWIRE_CA;
		$this->vd->nr_source_title = 'Newswire.CA';
		$this->vd->check_prn_sop_valid_lead = 1;
	}

	public function index($chunk = 1)
	{	
		$this->redirect("admin/nr_builder/newswire_ca/all");
	}

	public function instant_edit_save()
	{
		$company_id = $this->input->post("instant_edit_company_id");
		$field_name = $this->input->post("instant_edit_field");
		$field_value = $this->input->post("instant_edit_text");
		$post = $this->input->post();

		if ($company_id && $field_name && $field_name == "about_the_company")
		{
			$c_data = Model_Newswire_CA_Company_Data::find($company_id);
			$c_data->short_description = value_or_null($post['instant_edit_short_description']);
			$c_data->about_company = value_or_null($post['instant_edit_about_company']);
			$c_data->save();
			$response = "Saved successfully";
		}

		elseif ($company_id && $field_name && $field_name == "address")
		{
			$c_data = Model_Newswire_CA_Company_Data::find($company_id);
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
			$c_data = Model_Newswire_CA_Company_Data::find($company_id);
			$c_data->{$field_name} = $field_value;

			$feed_status_field = "{$field_name}_feed_status";
			$c_data->{$feed_status_field} = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
			$c_data->save();
			$response = "Saved successfully";
		}

		elseif ($company_id && $field_name && $field_value)
		{
			$c_data = Model_Newswire_CA_Company_Data::find($company_id);
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
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('n.company_name', 'n.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
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
				FROM ac_nr_newswire_ca_company cbc 
				INNER JOIN ac_nr_newswire_ca_company_data cd
				ON cd.newswire_ca_company_id = cbc.id
				INNER JOIN nr_newsroom n 
				ON cbc.company_id = n.company_id				
				LEFT JOIN ac_nr_newsroom_claim cl
				ON cl.company_id = cbc.company_id
				AND cl.status='confirmed'

				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = cbc.id
				AND pvc.source = ?

				{$additional_tables}
				{$pre_exist_nr_websites_sql}
				WHERE {$filter} 
				AND cl.id is NULL
				ORDER BY 
				n.company_id DESC {$limit_str}";		
		
		$query = $this->db->query($sql, array(Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA));
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
		$sql = "SELECT n.*,
				n.company_id AS id,
				u.first_name AS o_user_first_name,
				u.last_name AS o_user_last_name,
				cd.email AS o_user_email,
				u.id AS o_user_id,
				c.date_first_exported_to_csv,
				c.date_exported_to_csv,
				ct.token AS token,
				cd.website,
				pvc.is_prn_valid_lead,
				pvc.date_till_lead_valid
				FROM nr_newsroom n
				LEFT JOIN nr_user u 
				ON n.user_id = u.id
				LEFT JOIN ac_nr_newswire_ca_company c
				ON c.company_id = n.company_id
				LEFT JOIN ac_nr_newswire_ca_company_data cd
				ON cd.newswire_ca_company_id = c.id
				LEFT JOIN ac_nr_newsroom_claim_token ct
				ON ct.company_id = c.company_id	

				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = c.id
				AND pvc.source = ?

				WHERE n.company_id IN ({$id_str})
				ORDER BY n.company_id DESC";
			
		$query = $this->db->query($sql, array(Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA));
		$results = Model_Newsroom::from_db_all($query);

		$websites = array();
		$company_ids = array();

		$now_time = strtotime($now);
		foreach ($results as $result)	
		{
			$websites[] = $result->website;
			$company_ids[] = $result->id;

			if ($result->is_prn_valid_lead && $result->date_till_lead_valid)
			{
				$time_till_valid = strtotime($result->date_till_lead_valid);

				if($time_till_valid > $now_time)
					$result->valid_till_now = 1;
				else
					$result->lead_expired = 1;

			}
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
		$this->vd->heading = "Paid Claims (Newswire.ca)";
		parent::paid_claims($chunk);
	}

	protected function render_claim_submissions_list($chunkination, $results)
	{
		$this->vd->heading = "Claim Submissions (Newswire.ca)";
		parent::render_claim_submissions_list($chunkination, $results);
	}

	protected function render_verified_submissions_list($chunkination, $results)
	{
		$this->vd->heading = "Claimed Submissions (Newswire.ca)";
		parent::render_verified_submissions_list($chunkination, $results);
	}

	public function export_auto_built_nrs_to_csv()
	{
		$is_inc_contact_url = 1;
		parent::export_auto_built_nrs_to_csv($is_inc_contact_url);
	}	
	
	public function edit($id = null, $modal = 0)
	{
		if (!$id)
			$this->redirect('admin/nr_builder/newswire_ca');

		if ($this->input->post('save'))
		{
			$this->edit_save($id);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Company detail updated successfully.');
			$this->add_feedback($feedback);
			$this->redirect("admin/nr_builder/newswire_ca/edit/{$id}");
		}

		$comp = Model_Newswire_CA_Company::find($id);
		$c_data = Model_Newswire_CA_Company_Data::find($id);
		$this->vd->comp = $comp;
		$this->vd->c_data = $c_data;
		$order = array('name', 'asc');
		$criteria = array();
		$this->vd->countries = Model_Country::find_all($criteria, $order);

		if ($modal == 1)
			$this->load->view('admin/nr_builder/newswire_ca/edit_with_modal');
		else
		{
			$this->load->view('admin/header');
			$this->load->view('admin/nr_builder/menu');
			$this->load->view('admin/pre-content');
			$this->load->view('admin/nr_builder/newswire_ca/edit');
			$this->load->view('admin/post-content');
			$this->load->view('admin/footer');
		}		
	}

	public function edit_save($id)
	{
		if (!$id || ! $this->input->post('name'))
			$this->redirect('admin/nr_builder/newswire_ca');

		$post = $this->input->post();
		$comp = Model_Newswire_CA_Company::find($id);
		$comp->name = $post['name'];
		$comp->save();

		$response = array();	

		$c_data = Model_Newswire_CA_Company_Data::find($id);
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
				$c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_fb} (Facebook)";
				$invalid_socials[] = "soc_fb";
				$c_data->soc_fb = null;
				$c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_twitter = $post['soc_twitter'];

		if ($c_data->soc_twitter)
		{
			if (Social_Twitter_Feed::is_valid($c_data->soc_twitter))
				$c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_twitter} (Twitter)";
				$invalid_socials[] = 'soc_twitter';
				$c_data->soc_twitter = null;
				$c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_gplus = $post['soc_gplus'];

		if ($c_data->soc_gplus)
		{
			if (Social_GPlus_Feeds::is_valid($c_data->soc_gplus))
				$c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_gplus} (Google Plus)";
				$invalid_socials[] = 'soc_gplus';
				$c_data->soc_gplus = null;
				$c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_linkedin = $post['soc_linkedin'];
		$c_data->soc_youtube = $post['soc_youtube'];

		if ($c_data->soc_youtube)
		{
			if (Social_Youtube_Feed::is_valid($c_data->soc_youtube))
				$c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_youtube} (Youtube)";
				$invalid_socials[] = 'soc_youtube';
				$c_data->soc_youtube = null;
				$c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
			}
		}

		$c_data->soc_pinterest = $post['soc_pinterest'];

		if ($c_data->soc_pinterest)
		{
			if (Social_Pinterest_Feed::is_valid($c_data->soc_pinterest))
				$c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
			else
			{
				$warning_msg = "{$warning_msg} <br> Unable to validate the following social media ";
				$warning_msg = "{$warning_msg} account: {$c_data->soc_pinterest} (Pinterest)";
				$invalid_socials[] = 'soc_pinterest';
				$c_data->soc_pinterest = null;
				$c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
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
			$cat = Model_Newswire_CA_Category::find($filter_category);
			$list_filter = new stdClass();
			$list_filter->name = 'category';
			$list_filter->value = $cat->name;
			if (!$list_filter->value)
				$list_filter->value = $filter_category;
			$gstring = array('filter_category' => $filter_category);
			$list_filter->gstring = http_build_query($gstring);
			array_push($this->vd->filters, $list_filter);
			$this->vd->selected_cat_id = $filter_category;
			$filter = "{$filter} AND c.newswire_ca_category_id = {$filter_category}";
		}

		$email_condition = "";
		$now = Date::$now->format(DATE::FORMAT_MYSQL);
		
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
			
			$condition_social_missing = "(ISNULL(NULLIF(cd.soc_fb,'')) + ISNULL(NULLIF(cd.soc_twitter,'')) + 
										ISNULL(NULLIF(cd.soc_gplus,'')) + ISNULL(NULLIF(cd.soc_youtube,'')) + 
										ISNULL(NULLIF(cd.soc_pinterest,''))) = 5";
			
			$condition_website = "NULLIF(cd.website, '') IS NOT NULL";
			$condition_email = "NULLIF(cd.email, '') IS NOT NULL";
			$condition_logo = "NULLIF(cd.logo_image_path, '') IS NOT NULL";
			$condition_logo_valid = "is_logo_valid = 1";
			$condition_logo_null = "NULLIF(cd.logo_image_path, '') IS NULL";
			$condition_website_valid = "cd.is_website_valid";
			$condition_name = "NOT ISNULL(NULLIF(c.name, ''))";

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
				$filter = "{$filter} AND pvc.is_prn_valid_lead = 1
						AND pvc.date_till_lead_valid >= '$now'";
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND {$condition_email}";
				$filter = "{$filter} AND {$condition_social}";
				$filter = "{$filter} AND {$condition_name}";
				$filter = "{$filter} AND {$condition_website_valid}";
				$filter = "{$filter} AND c.id IN (
								SELECT newswire_ca_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_newswire_ca_pr p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
			}


			else if ($filter_search == "READY_TO_BUILD_NEWSROOMS_OLD")	
			{
				$this->vd->search_ready_newsrooms = 1;
				$filter = "{$filter} AND pvc.is_prn_valid_lead = 1
						AND pvc.date_till_lead_valid >= '$now'";
				$filter = "{$filter} AND {$condition_website}";
				$filter = "{$filter} AND {$condition_email}";
				$filter = "{$filter} AND {$condition_social}";
				$filter = "{$filter} AND {$condition_name}";
				$filter = "{$filter} AND c.id NOT IN (
								SELECT newswire_ca_company_id 
								FROM nr_content c
								INNER JOIN nr_pb_newswire_ca_pr p
								ON p.content_id = c.id
								WHERE c.date_publish >= '2014-01-01'
							)";

				$email_condition = "LEFT JOIN nr_user_base u ON u.email = cd.email";
				$filter = "{$filter} AND u.email is NULL";
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
				$filter = "{$filter} AND {$condition_social_missing}";
			}

			else if ($filter_search == "MISSING_LOGO_N_SOCIALS_NEWSROOMS")
			{
				$this->vd->search_ready_newsrooms = 1;
				
				$filter = "{$filter} AND cd.website IS NOT NULL AND cd.website <> ''";
				$filter = "{$filter} AND cd.email IS NOT NULL AND cd.email <> ''";
				$filter = "{$filter} AND NULLIF(cd.logo_image_path, '') IS NULL";
				$filter = "{$filter} AND {$condition_social_missing}";
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
				cd.newswire_ca_company_id as id 
				FROM ac_nr_newswire_ca_company c
				LEFT JOIN ac_nr_newswire_ca_company_data cd 
				ON cd.newswire_ca_company_id = c.id 
				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = c.id
				AND pvc.source = ?
				{$email_condition}
				WHERE {$filter} 
				AND c.company_id IS NULL
				HAVING cd.newswire_ca_company_id IS NOT NULL
				ORDER BY c.id DESC
				{$limit_str}";
				
		$query = $this->db->query($sql, Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA);
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
				cd.newswire_ca_company_id AS source_company_id,
				pvc.is_prn_valid_lead,
				pvc.date_till_lead_valid
				FROM ac_nr_newswire_ca_company c
				LEFT JOIN ac_nr_newswire_ca_company_data cd 
				ON cd.newswire_ca_company_id = c.id 
				LEFT JOIN nr_user_base u 
				ON u.email = cd.email

				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = c.id
				AND pvc.source = ?

				LEFT JOIN (
					SELECT newswire_ca_company_id,
					COUNT(content_id) AS num_prs
					FROM nr_pb_newswire_ca_pr 
					GROUP BY newswire_ca_company_id
				) AS p_counter ON p_counter.newswire_ca_company_id = c.id

				WHERE c.id IN ({$id_str})
				ORDER BY c.id DESC";
			
		$query = $this->db->query($sql, array(Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA));
		$results = Model_Newsroom::from_db_all($query);

		$now_time = strtotime($now);
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

			if ($result->soc_fb_feed_status == Model_Newswire_CA_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_twitter_feed_status == Model_Newswire_CA_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_gplus_feed_status == Model_Newswire_CA_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_youtube_feed_status == Model_Newswire_CA_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			if ($result->soc_pinterest_feed_status == Model_Newswire_CA_Company_Data::SOCIAL_VALID)
				$soc_valid_count++;

			$company_name = trim($result->name);
			if ($result->website && $result->email && $soc_count >= 1 && 
				$soc_valid_count >= 1  && $result->is_website_valid && !empty($company_name))
					$result->is_ready_to_build = 1;

			if ($result->is_prn_valid_lead && $result->date_till_lead_valid)
			{
				$time_till_valid = strtotime($result->date_till_lead_valid);

				if($time_till_valid > $now_time)
					$result->valid_till_now = 1;
				else
					$result->lead_expired = 1;

			}
		}


		$prs_modal = new Modal();
		$prs_modal->set_title("Company's Newswire_CA PRs");
		$this->add_eob($prs_modal->render(500, 230));
		$this->vd->prs_modal_id = $prs_modal->id;

		return $results;
	}

	public function pr_links($newswire_ca_company_id)
	{
		$sql = "SELECT c.title, p.url
				FROM nr_content c
				INNER JOIN nr_pb_newswire_ca_pr p
				ON p.content_id = c.id
				WHERE p.newswire_ca_company_id = {$newswire_ca_company_id}";

		$query = $this->db->query($sql);
		$results = Model_PB_Newswire_CA_PR::from_db_all($query);
		$this->vd->results = $results;

		$m_mw_comp = Model_Newswire_CA_Company::find($newswire_ca_company_id);
		$this->vd->company_name = $m_mw_comp->name;
		$this->load->view('admin/nr_builder/newswire_ca/pr_links');

	}

	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;	

		$this->vd->cb_cat = Model_Newswire_CA_Category::find_all();
		
		$company_modal = new Modal();
		$company_modal->set_title('Edit Company Data');
		$this->add_eob($company_modal->render(970, 430));
		$this->vd->company_modal_id = $company_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/newswire_ca/main');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function retry_logo()
	{
		if ( ! $id = $this->input->post("id"))
			return false;

		if (! $comp = Model_Newswire_CA_Company::find("company_id", $id))
			return false;

		$c_data = Model_Newswire_CA_Company_Data::find($comp->id);
		
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
		$newswire_ca_company_id = $this->input->post('newswire_ca_company_id');
		$is_logo_valid = $this->input->post('is_logo_valid');

		if (!$c_data = Model_Newswire_CA_Company_Data::find($newswire_ca_company_id))
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
		$newswire_ca_company_id = $this->input->post('newswire_ca_company_id');
		$is_website_valid = $this->input->post('is_website_valid');

		if (!$newswire_ca_comp = Model_Newswire_CA_Company_Data::find($newswire_ca_company_id))
			$this->json(0);
		
		else
		{
			if ($is_website_valid)
				$newswire_ca_comp->is_website_valid = 1;
			else
			{
				$newswire_ca_comp->website = null;
				$newswire_ca_comp->is_website_valid = 0;
				$newswire_ca_comp->website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_NONE;
				$newswire_ca_comp->name = null;
			}

			$newswire_ca_comp->save();

			$this->json(1);
		}
	}

	public function generate_tokens_for_all_generated_nrs()
	{
		$sql = "SELECT n.company_id AS company_id FROM 
				ac_nr_newswire_ca_company pc 
				INNER JOIN 
				nr_newsroom n 
				ON pc.company_id = n.company_id
				INNER JOIN nr_newsroom_custom nc
				ON nc.company_id = n.company_id 
				WHERE 1 ORDER BY 
				n.company_id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Newswire_CA_Company::from_db_all($query);
		foreach ($results as $result)
		{
			$token = new Model_Newswire_CA_NR_Claim_Token();
			$token->company_id = $result->company_id;
			$token->generate();
			$token->save();
		}
	}

	
}

?>