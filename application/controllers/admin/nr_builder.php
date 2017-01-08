<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/nr_builder_trait');

class NR_Builder extends Admin_Base {

	use NR_Builder_Trait;	
	const LISTING_CHUNK_SIZE = 20;
	protected $nr_source;

	public function index()
	{
		$this->redirect('admin/nr_builder/crunchbase/all');
	}

	public function all($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/all/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/all";
			$this->redirect(gstring($url));
		}		
		
		$this->render_list($chunkination, $results);
	}

	public function bulk_build()
	{
		$ids = $this->input->post('selected');

		if (!is_array($ids) || !count($ids) || !$this->input->post('bulk_build_nrs'))
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$this->vd->total_nrs = count($ids);
		$this->vd->ids = $ids;

		$this->session->set('bulk_build_ids', $ids);

		$ids_list = serialize($ids);

		$task = new CI_Background_Task();
		$task->set(array(
			'auto_create_nr',
			$this->nr_source,
			'build_nrs',
			$ids_list
		));

		$task->execute();

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/partials/nr_build_status');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function bulk_build_status_poll()
	{
		$response = new stdClass();
		$ids = $this->session->get('bulk_build_ids');

		if (!is_array($ids) || !count($ids))
		{
			$response->is_completed = 1;
			$this->session->delete('bulk_build_ids');
			return $this->json($response);
		}

		$id_list = sql_in_list($ids);

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($this->nr_source);
		$sql = "SELECT count(id) AS count
				FROM {$tbl_prefix}company
				WHERE id IN ({$id_list})
				AND company_id > 0";

		$counter = $this->db->query($sql)->row()->count;

		$response->counter = $counter;
		
		if ($counter == count($ids))
		{
			$response->is_completed = 1;
			$this->session->delete('bulk_build_ids');
		}
		
		return $this->json($response);		
	}

	public function auto_built_newsrooms($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);

		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "auto_built_newsrooms";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/auto_built_newsrooms/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_auto_built_nr_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			$url = "admin/nr_builder/{$this->nr_source}/auto_built_newsrooms";
			$this->redirect(gstring($url));
		}		
		
		$this->render_auto_built_nr_list($chunkination, $results);
	}

	public function auto_built_nrs_already_exported($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);

		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "auto_built_nrs_already_exported";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/auto_built_nrs_already_exported/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = " NOT ISNULL(NULLIF(cbc.date_exported_to_csv,'0000-00-00 00:00:00')) ";
		$results = $this->fetch_auto_built_nr_results($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/auto_built_nrs_already_exported";
			$this->redirect(gstring($url));
		}
		
		$this->vd->already_exported = 1;
		$this->render_auto_built_nr_list($chunkination, $results);
	}

	public function auto_built_nrs_not_exported($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "auto_built_nrs_not_exported";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/auto_built_nrs_not_exported/-chunk-");
		$chunkination->set_url_format($url_format);
		
		$filter = " ISNULL(NULLIF(cbc.date_exported_to_csv,'0000-00-00 00:00:00')) 
					AND dup_webs.dup_company_id is NULL ";

		$pre_exist_nr_websites_sql = "LEFT JOIN (
											SELECT cp.company_id AS dup_company_id,
											cp.website
											FROM nr_company_profile cp
											INNER JOIN nr_newsroom nw
											ON cp.company_id = nw.company_id) AS dup_webs 
										ON dup_webs.website = cd.website
										AND dup_company_id <> n.company_id";

		$results = $this->fetch_auto_built_nr_results($chunkination, $filter, $pre_exist_nr_websites_sql);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/auto_built_nrs_not_exported";
			$this->redirect(gstring($url));
		}		
		
		$this->vd->not_exported = 1;
		$this->render_auto_built_nr_list($chunkination, $results);
	}

	public function auto_built_nrs_prn_valid_not_exported($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "auto_built_nrs_prn_valid_not_exported";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/auto_built_nrs_prn_valid_not_exported/-chunk-");
		$chunkination->set_url_format($url_format);
		
		$now = Date::$now->format(DATE::FORMAT_MYSQL);

		$filter = " ISNULL(NULLIF(cbc.date_exported_to_csv,'0000-00-00 00:00:00')) 
					AND dup_webs.dup_company_id is NULL ";

		$filter = "{$filter} AND pvc.is_prn_valid_lead = 1
					AND pvc.date_till_lead_valid >= '$now'";

		$pre_exist_nr_websites_sql = "LEFT JOIN (
											SELECT cp.company_id AS dup_company_id,
											cp.website
											FROM nr_company_profile cp
											INNER JOIN nr_newsroom nw
											ON cp.company_id = nw.company_id) AS dup_webs 
										ON dup_webs.website = cd.website
										AND dup_company_id <> n.company_id";

		$results = $this->fetch_auto_built_nr_results($chunkination, $filter, $pre_exist_nr_websites_sql);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/auto_built_nrs_not_exported";
			$this->redirect(gstring($url));
		}		
		
		$this->vd->auto_built_nrs_prn_valid_not_exported = 1;
		$this->render_auto_built_nr_list($chunkination, $results);
	}
	
	public function auto_built_nrs_already_existing($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "auto_built_nrs_already_existing";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/auto_built_nrs_already_existing/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = 1;
		
		$pre_exist_nr_websites_sql = "INNER JOIN (
											SELECT cp.company_id AS dup_company_id,
											cp.website
											FROM nr_company_profile cp
											INNER JOIN nr_newsroom nw
											ON cp.company_id = nw.company_id) AS dup_webs 
										ON dup_webs.website = cd.website
										AND dup_company_id <> n.company_id";
										
		$results = $this->fetch_auto_built_nr_results($chunkination, $filter, $pre_exist_nr_websites_sql);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			$url = "admin/nr_builder/{$this->nr_source}/auto_built_nrs_already_existing";
			$this->redirect(gstring($url));
		}	
		
		$this->vd->already_existing_nrs = 1;
		$this->render_auto_built_nr_list($chunkination, $results);
	}

	public function warning_counters()
	{
		$data = new StdClass();
		$data->ab_not_exported_counter_48_hrs = $this->auto_built_not_exported_counter($this->nr_source);
		$data->claim_counter_48_hrs = $this->claim_submissions_counter($this->nr_source);
		$data->verified_counter_48_hrs = $this->verified_submissions_counter($this->nr_source);
		$this->json($data);
	}

	public function set_listing_chunk_size($size  = 0, $tab = null)
	{
		if ($size > 0)
		{
			$this->session->set("listing_size", $size);
			$url = gstring("admin/nr_builder/{$this->nr_source}/{$tab}");
			$this->redirect($url);
		}
	}

	public function add_sales_agent_modal()
	{
		$criteria = array();
		$criteria[] = array('is_deleted', 0);
		$criteria[] = array('is_active', 1);
		$sort_order = array('first_name', 'asc');
		$sales_agents = Model_Sales_Agent::find_all($criteria, $sort_order);
		$this->vd->sales_agents = $sales_agents;
		$sales_agent_modal = new Modal();
		$sales_agent_modal->set_title('Select Sales Agent');
		$modal_view = 'admin/nr_builder/partials/sales_agent_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$sales_agent_modal->set_content($modal_content);
		$modal_view = 'admin/nr_builder/partials/sales_agent_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$sales_agent_modal->set_footer($modal_content);
		$this->add_eob($sales_agent_modal->render(300, 250));
		$this->vd->sales_agent_modal_id = $sales_agent_modal->id;
	}
	
	protected function save_auto_built_export($company_ids, $sales_agent_id)
	{
		if (!is_array($company_ids) || !count($company_ids) || !$sales_agent_id)
			return false;

		$posts = $this->input->post();
		$ab_export = new Model_Auto_Built_NR_Export();
		$ab_export->sales_agent_id = $sales_agent_id;
		$ab_export->date_exported = Date::$now->format(DATE::FORMAT_MYSQL);
		
		$filter = null;
		if ($posts['export_selected'])
			$filter = Model_Auto_Built_NR_Export::FILTER_EXPORT_SELECTED;
		elseif ($posts['export_not_exported']) 
			$filter = Model_Auto_Built_NR_Export::FILTER_EXPORT_ALL_NOT_EXPORTED;
		elseif ($posts['export_exported'])
			$filter = Model_Auto_Built_NR_Export::FILTER_EXPORT_ALL_ALREADY_EXPORTED;

		$ab_export->filter = $filter;
		$ab_export->save();

		if ($filter == Model_Auto_Built_NR_Export::FILTER_EXPORT_ALL_ALREADY_EXPORTED)
			return false;

		$auto_built_nr_export_id = $ab_export->id;

		$values = array();
		foreach ($company_ids as $company_id)
			$values[] = "('{$company_id}', '{$auto_built_nr_export_id}')";

		if (!count($values))
			return false;

		$values_str = implode(", ", $values);

		$sql = "INSERT INTO 
				ac_nr_auto_built_nr_export_x_company (company_id, auto_built_nr_export_id) 
				VALUES {$values_str}
				ON DUPLICATE KEY 
				UPDATE company_id = company_id";

		$this->db->query($sql);
	}

	public function export_auto_built_nrs_to_csv($is_inc_contact_url = 0)
	{
		$nr_source = $this->nr_source;
		if (!$this->input->post('export_selected') && !$this->input->post('export_not_exported') && 
			!$this->input->post('export_exported'))
			$this->redirect("admin/nr_builder/{$nr_source}");
		
		$filter = 1;
		$not_exported_query = "";
		
		if ($this->input->post('export_selected'))
		{
			$ids = $this->input->post('selected');
			if (is_array($ids) && count($ids))
			{
				$ids_list = sql_in_list($ids);
				$filter = "n.company_id IN ({$ids_list})";
			}
			else
			{
				$feedback = new Feedback('error', 'Error!', 'Please select newsrooms to export');
				$this->add_feedback($feedback);
				$this->redirect("admin/nr_builder/{$nr_source}/auto_built_nrs_not_exported");
			}
		}

		elseif ($this->input->post('export_not_exported'))
		{
			$filter = "ISNULL(NULLIF(pc.date_exported_to_csv,'0000-00-00 00:00:00'))";
			$filter = "{$filter} AND dup_webs.pre_exist_web_counter is NULL ";

			$not_exported_query = "LEFT JOIN (
									SELECT website, count(website)  AS pre_exist_web_counter
									FROM nr_company_profile 
									GROUP BY website 
									HAVING count(website) > 1 ) AS dup_webs 
									ON dup_webs.website = pcd.website";
		}

		elseif ($this->input->post('export_exported'))
			$filter = "NOT ISNULL(NULLIF(pc.date_exported_to_csv,'0000-00-00 00:00:00'))";

		$date_exported = Date::$now->format(Date::FORMAT_MYSQL);

		if ($this->vd->check_prn_sop_valid_lead && !$this->input->post('export_exported'))
		{
			$filter = "{$filter} AND pvc.is_prn_valid_lead = 1
						AND pvc.date_till_lead_valid >= '$date_exported' ";
		}

		$csv = new CSV_Writer('php://memory');
		
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);
		
		$src_company_id_field = "{$nr_source}_company_id";
		if ($nr_source == Model_Company::SOURCE_CRUNCHBASE)
			$src_company_id_field = "company_id";

		$sql = "SELECT pc.id, pcd.*,
				n.company_name,
				n.date_created,
				n.*, c.newsroom,
				ct.token AS token,
				pvc.is_prn_valid_lead,
				pvc.date_checked AS date_pvc_checked,
				pvc.date_till_lead_valid
				FROM {$tbl_prefix}company pc 
				INNER JOIN {$tbl_prefix}company_data pcd
				ON pcd.{$src_company_id_field} = pc.id 
				INNER JOIN nr_newsroom n 
				ON pc.company_id = n.company_id
				LEFT JOIN nr_company c
				ON n.company_id = c.id
				LEFT JOIN nr_user_base u
				ON c.user_id = u.id 
				LEFT JOIN ac_nr_newsroom_claim_token ct
				ON ct.company_id = pc.company_id
				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = pc.id
				AND pvc.source = ?
				{$not_exported_query}
				WHERE {$filter} ORDER BY 
				n.company_id DESC";

			
		$query = $this->db->query($sql, array($nr_source));
		$results = Model_Newsroom::from_db_all($query);
		$found_ids = array();
		
		$row = array("Email", "Company", "URL", "Date Created", "Date Exported", "Contact Name", "Phone", 
				"Website");

		if ($is_inc_contact_url)
			$row[] = "Contact Us URL";

		if ($nr_source == Model_Company::SOURCE_MARKETWIRED)
			$row[] = "Contact Info";

		if ($this->vd->check_prn_sop_valid_lead)
		{
			$row[] = "Date Checked";
			$row[] = "Date till Lead Valid";
		}

		$csv->write($row);

		$company_ids = array();
		$now_time = strtotime($date_exported);
		foreach ($results as $result)
		{
			$row = array();
			$row['email'] = $result->email;
			$row['company_name'] = $result->company_name;
			//$row['newsroom_url'] = $result->url();
			$row['claim_url'] = "{$result->url()}c/{$result->token}";
			$row['date_created'] = $result->date_created;
			$row['date_exported'] = $date_exported;
			$row['contact_name'] = $result->contact_name;
			$row['phone'] = $result->phone;
			$row['website'] = $result->website;
		
			if ($is_inc_contact_url)
				$row['contact_page_url'] = $result->contact_page_url;

			if ($nr_source == Model_Company::SOURCE_MARKETWIRED)
				$row['contact_info'] = $result->contact_info;

			if ($this->vd->check_prn_sop_valid_lead)
			{
				$row['date_checked'] = $result->date_pvc_checked;
				$row['date_till_lead_valid'] = "LEAD NOT VALID";

				if ($result->is_prn_valid_lead && $result->date_till_lead_valid)
				{
					$time_till_valid = strtotime($result->date_till_lead_valid);

					if($time_till_valid > $now_time)
						$row['date_till_lead_valid'] = $result->date_till_lead_valid;					
				}

				
			}

			$found_ids[] = $result->id;
			$csv->write($row);

			$company_ids[] = $result->company_id;
		}		
		
		if (count($found_ids))
		{
			$found_id_list = sql_in_list($found_ids);

			$sql = "UPDATE {$tbl_prefix}company
					SET date_exported_to_csv = '{$date_exported}'
					WHERE id IN ({$found_id_list})";

			$this->db->query($sql);

			$sql = "UPDATE {$tbl_prefix}company
					SET date_first_exported_to_csv = '{$date_exported}'
					WHERE id IN ({$found_id_list})
					AND date_first_exported_to_csv IS NULL ";

			$this->db->query($sql);

			$sales_agent_id = $this->input->post('sales_agent_id');
			$this->save_auto_built_export($company_ids, $sales_agent_id);			
		}
		else
			$this->redirect("admin/nr_builder/{$nr_source}/auto_built_newsrooms");
		
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');
		
		$sales_agent_prefix = null;

		if (!empty($sales_agent_id) && $sales_agent = Model_Sales_Agent::find($sales_agent_id))
			$sales_agent_prefix = "{$sales_agent->first_name}_";
		
		$count = count($results);
		force_download("{$sales_agent_prefix}{$count}_NR_{$date_exported}.csv", stream_get_contents($handle));

		return;
	}

	protected function save_verified_export($company_ids, $sales_agent_id)
	{
		if (!is_array($company_ids) || !count($company_ids) || !$sales_agent_id)
			return false;

		$posts = $this->input->post();
		$ab_export = new Model_Auto_Built_Verified_Export();
		$ab_export->sales_agent_id = $sales_agent_id;
		$ab_export->date_exported = Date::$now->format(DATE::FORMAT_MYSQL);

		$filter = null;

		if ($posts['export_selected'])
			$filter = Model_Auto_Built_Verified_Export::FILTER_EXPORT_SELECTED;

		elseif ($posts['export_not_exported']) 
			$filter = Model_Auto_Built_Verified_Export::FILTER_EXPORT_ALL_NOT_EXPORTED;

		elseif ($posts['export_exported'])
			$filter = Model_Auto_Built_Verified_Export::FILTER_EXPORT_ALL_ALREADY_EXPORTED;

		$ab_export->filter = $filter;
		$ab_export->save();

		if ($filter == Model_Auto_Built_Verified_Export::FILTER_EXPORT_ALL_ALREADY_EXPORTED)
			return false;

		$auto_built_verified_export_id = $ab_export->id;

		$values = array();
		foreach ($company_ids as $company_id)
			$values[] = "('{$company_id}', '{$auto_built_verified_export_id}')";

		if (!count($values))
			return false;

		$values_str = implode(", ", $values);

		$sql = "INSERT INTO 
				ac_nr_auto_built_verified_export_x_company (company_id, auto_built_verified_export_id) 
				VALUES {$values_str}
				ON DUPLICATE KEY 
				UPDATE company_id = company_id";

		$this->db->query($sql);
	}

	public function export_confirmed_submissions_to_csv()
	{
		$nr_source = $this->nr_source;

		if (!$this->input->post('export_selected') && !$this->input->post('export_not_exported') && 
			!$this->input->post('export_exported'))
			$this->redirect("admin/nr_builder/{$nr_source}/verified_submissions_not_exported");

		$filter = 1;
		
		$sales_agent_id = $this->input->post('sales_agent_id');

		if ($this->input->post('export_selected'))
		{
			$ids = $this->input->post('selected');
			if (is_array($ids) && count($ids))
			{
				$ids_list = sql_in_list($ids);
				$filter = "cl.id IN ({$ids_list})";
			}
			else
			{
				$feedback = new Feedback('error', 'Error!', 'Please select claims to export');
				$this->add_feedback($feedback);
				$this->redirect("admin/nr_builder/{$nr_source}/verified_submissions_not_exported");
			}
		}

		elseif ($this->input->post('export_not_exported'))
			$filter = "ISNULL(NULLIF(cl.date_exported_to_csv,'0000-00-00 00:00:00'))";

		elseif ($this->input->post('export_exported'))
			$filter = "NOT ISNULL(NULLIF(cl.date_exported_to_csv,'0000-00-00 00:00:00'))";
		
			
		$posts = $this->input->post();
		$date_exported = Date::$now->format(Date::FORMAT_MYSQL);

		$csv = new CSV_Writer('php://memory');
		
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);
		
		$src_company_id_field = "{$nr_source}_company_id";
		if ($nr_source == Model_Company::SOURCE_CRUNCHBASE)
			$src_company_id_field = "company_id";

		if (!empty($sales_agent_id))
			$filter = "{$filter} AND ne.sales_agent_id = '{$sales_agent_id}'";
		else
			$filter = "{$filter} AND ne.sales_agent_id IS NULL";

		$sql = "SELECT n.*,
				cl.id AS claim_id,
				cl.rep_name AS claimant_rep_name, 
				cl.email AS claimant_email,
				cl.phone AS claimant_phone,
				cl.date_claimed AS date_claimed,
				cl.remote_addr AS remote_addr,
				cl.is_from_private_link,
				n.company_id AS id,
				u.first_name AS o_user_first_name,
				u.last_name AS o_user_last_name,
				u.email AS o_user_email,
				u.id AS o_user_id
				FROM nr_newsroom n
				LEFT JOIN nr_user u 
				ON n.user_id = u.id			
				LEFT JOIN {$tbl_prefix}company c
				ON c.company_id = n.company_id
				INNER JOIN ac_nr_newsroom_claim cl
				ON cl.company_id = c.company_id
				AND cl.status = ?

				LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
				ON xc.company_id = c.company_id
				LEFT JOIN ac_nr_auto_built_nr_export ne
				ON xc.auto_built_nr_export_id = ne.id
				LEFT JOIN nr_sales_agent sa
				ON ne.sales_agent_id = sa.id

				WHERE {$filter}
				ORDER BY cl.company_id DESC";
		
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED, $sales_agent_id));
		$results = Model_Newsroom::from_db_all($query);
		$found_ids = array();
		$company_ids = array();
		
		$row = array("Company", "URL", "Claim Date", "Date Exported", "Rep Name", "Rep Email", "Phone");
		$csv->write($row);

		foreach ($results as $result)
		{
			$row = array();
			$row['company_name'] = $result->company_name;
			$row['newsroom_url'] = $result->url();
			$row['date_claim'] = $result->date_claimed;
			$row['date_exported'] = $date_exported;
			$row['rep_name'] = $result->claimant_rep_name;
			$row['rep_email'] = $result->claimant_email;
			$row['phone'] = $result->claimant_phone;
			$found_ids[] = $result->claim_id;
			$company_ids[] = $result->company_id;
			$csv->write($row);
		}

		if (count($found_ids))
			$this->save_verified_export($company_ids, $sales_agent_id);
		else
			$this->redirect("admin/nr_builder/{$nr_source}/verified_submissions_not_exported");
		
		if (count($found_ids) && !$this->input->post('export_exported'))
		{
			$found_id_list = sql_in_list($found_ids);

			$sql = "UPDATE ac_nr_newsroom_claim
					SET date_exported_to_csv = '{$date_exported}'
					WHERE id IN ({$found_id_list})";

			$this->db->query($sql);
		}		
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');

		$sales_agent_prefix = null;

		if (!empty($sales_agent_id) && $sales_agent = Model_Sales_Agent::find($sales_agent_id))
			$sales_agent_prefix = "{$sales_agent->first_name}_";
		
		$count = count($results);
		force_download("{$sales_agent_prefix}{$count}_Verified_{$date_exported}.csv", stream_get_contents($handle));
		return;
	}

	protected function render_auto_built_nr_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->add_sales_agent_modal();

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view("admin/nr_builder/auto_built_nrs");
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}	

	protected function paid_claims($chunk)
	{
		$chunkination = new Chunkination($chunk);

		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "paid_claims";
		$url_format = gstring("admin/nr_builder/{$this->vd->nr_source}/paid_claims/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_paid_claim_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->vd->nr_source}/paid_claims";
			$this->redirect(gstring($url));
		}

		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$this->load->view("admin/header");
		$this->load->view("admin/nr_builder/menu");
		$this->load->view("admin/pre-content");
		$this->load->view("admin/nr_builder/paid_claims");
		$this->load->view("admin/post-content");
		$this->load->view("admin/footer");
	}

	protected function render_paid_claims($chunkination, $results)
	{
		$this->load->view("admin/header");
		$this->load->view("admin/nr_builder/menu");
		$this->load->view("admin/pre-content");
		$this->load->view("admin/nr_builder/paid_claims");
		$this->load->view("admin/post-content");
		$this->load->view("admin/footer");
	}

	// These are the claims which have been marked as confirmed.
	public function verified_submissions($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);

		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "verified_submissions";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/verified_submissions/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_verified_submissions_results($chunkination);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/verified_submissions";
			$this->redirect(gstring($url));
		}

		$this->render_verified_submissions_list($chunkination, $results);		
	}

	public function verified_submissions_not_exported($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "verified_submissions_not_exported";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/verified_submissions_not_exported/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = " ISNULL(NULLIF(c.date_exported_to_csv,'0000-00-00 00:00:00')) ";
		$results = $this->fetch_verified_submissions_results($chunkination, $filter);
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/verified_submissions_not_exported";
			$this->redirect(gstring($url));
		}

		$this->vd->claim_filter = "not_exported";
		$this->render_verified_submissions_list($chunkination, $results);
	}

	public function verified_submissions_already_exported($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "verified_submissions_already_exported";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/verified_submissions_already_exported/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = " NOT ISNULL(NULLIF(c.date_exported_to_csv,'0000-00-00 00:00:00')) ";
		$results = $this->fetch_verified_submissions_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/verified_submissions_already_exported";
			$this->redirect(gstring($url));
		}
		
		$this->vd->claim_filter = "already_exported";
		$this->render_verified_submissions_list($chunkination, $results);
	}

	protected function fetch_verified_submissions_results($chunkination, $filter = null)
	{
		$nr_source = $this->nr_source;

		if (!$filter) $filter = '1';
		$limit_str = $chunkination->limit_str();

		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('cbc.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);

		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				cbc.company_id AS id FROM 
				{$tbl_prefix}company cbc 
				INNER JOIN 
				ac_nr_newsroom_claim c 
				ON c.company_id = cbc.company_id 
				AND c.status = ?
				WHERE {$filter} 
				AND cbc.company_id IS NOT NULL
				ORDER BY cbc.company_id DESC 
				{$limit_str}";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
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
		$sa_prefixes = Model_Sales_Agent::__prefixes('sa');
		$sql = "SELECT n.*,
			cl.id AS claim_id,
			cl.rep_name AS claimant_rep_name, 
			cl.email AS claimant_email,
			cl.phone AS claimant_phone,
			cl.date_claimed AS date_claimed,
			cl.remote_addr AS remote_addr,
			cl.is_from_private_link,
			cl.date_exported_to_csv,
			n.company_id AS id,
			cl.date_admin_updated AS date_confirmed,
			u.first_name AS o_user_first_name,
			u.last_name AS o_user_last_name,
			u.email AS o_user_email,
			u.id AS o_user_id,
			{$sa_prefixes}
			FROM nr_newsroom n
			LEFT JOIN nr_user u 
			ON n.user_id = u.id			
			LEFT JOIN {$tbl_prefix}company c
			ON c.company_id = n.company_id
			LEFT JOIN ac_nr_newsroom_claim cl
			ON cl.company_id = c.company_id
			AND cl.status = ?
			LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
			ON xc.company_id = c.company_id
			LEFT JOIN ac_nr_auto_built_nr_export ne
			ON xc.auto_built_nr_export_id = ne.id
			LEFT JOIN nr_sales_agent sa
			ON ne.sales_agent_id = sa.id
			WHERE n.company_id IN ({$id_str})
			ORDER BY n.company_id DESC";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
		$results = Model_Newsroom::from_db_all($query, array('sales_agent' => 'Model_Sales_Agent'));		
		
		return $results;
	}

	protected function render_verified_submissions_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$this->add_sales_agent_modal();

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/verified_submissions');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function claim_submissions($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);

		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;

		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "claim_submissions";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/claim_submissions/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_claim_submissions_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/claim_submissions";
			$this->redirect(gstring($url));
		}

		$this->render_claim_submissions_list($chunkination, $results);
	}


	public function claim_submissions_from_private_link($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "claim_submissions_from_private_link";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/claim_submissions_from_private_link/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = " c.is_from_private_link = 1";
		$results = $this->fetch_claim_submissions_results($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/claim_submissions_from_private_link";
			$this->redirect(gstring($url));
		}
		
		$this->vd->claim_filter = "from_private_link";
		$this->render_claim_submissions_list($chunkination, $results);
	}

	public function claim_submissions_from_public_link($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		if (! $listing_size = $this->session->get('listing_size'))
			$listing_size = static::LISTING_CHUNK_SIZE;
		
		$chunkination->set_chunk_size($listing_size);
		$this->vd->listing_size = $listing_size;
		$this->vd->tab_filter = "claim_submissions_from_public_link";
		$url_format = gstring("admin/nr_builder/{$this->nr_source}/claim_submissions_from_public_link/-chunk-");
		$chunkination->set_url_format($url_format);
		$filter = " c.is_from_private_link = 0";
		$results = $this->fetch_claim_submissions_results($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/{$this->nr_source}/claim_submissions_from_public_link";
			$this->redirect(gstring($url));
		}
		
		$this->vd->claim_filter = "from_public_link";
		$this->render_claim_submissions_list($chunkination, $results);
	}

	protected function fetch_claim_submissions_results($chunkination, $filter = null)
	{
		$nr_source = $this->nr_source;

		if (!$filter) $filter = '1';
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('cbc.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);

		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				cbc.company_id AS id FROM 
				{$tbl_prefix}company cbc 
				INNER JOIN 
				ac_nr_newsroom_claim c 
				ON c.company_id = cbc.company_id 
				AND c.status = ?
				WHERE {$filter} 
				AND cbc.company_id IS NOT NULL
				ORDER BY c.id DESC 
				{$limit_str}";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CLAIMED));
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
		$sa_prefixes = Model_Sales_Agent::__prefixes('sa');
		$sql = "SELECT n.*,
			cl.id AS claim_id,
			cl.rep_name AS claimant_rep_name, 
			cl.email AS claimant_email,
			cl.phone AS claimant_phone,
			cl.date_claimed AS date_claimed,
			cl.remote_addr AS remote_addr,
			cl.is_from_private_link,
			cl.date_exported_to_csv AS date_exported_csv,
			n.company_id AS id,
			u.first_name AS o_user_first_name,
			u.last_name AS o_user_last_name,
			u.email AS o_user_email,
			u.id AS o_user_id,
			ip_rej_count.ip_rejected_counter,
			{$sa_prefixes}
			FROM nr_newsroom n
			LEFT JOIN nr_user u 
			ON n.user_id = u.id			
			LEFT JOIN {$tbl_prefix}company c
			ON c.company_id = n.company_id
			LEFT JOIN ac_nr_newsroom_claim cl
			ON cl.company_id = c.company_id
			AND cl.status = ?

			LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
			ON xc.company_id = c.company_id
			LEFT JOIN ac_nr_auto_built_nr_export ne
			ON xc.auto_built_nr_export_id = ne.id
			LEFT JOIN nr_sales_agent sa
			ON ne.sales_agent_id = sa.id

			LEFT JOIN (
				SELECT remote_addr, COUNT(id) AS ip_rejected_counter 
				FROM ac_nr_newsroom_claim
				WHERE status = ? OR status = ?
				GROUP BY remote_addr
			) AS ip_rej_count ON ip_rej_count.remote_addr = cl.remote_addr

			WHERE n.company_id IN ({$id_str})
			ORDER BY cl.id DESC";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CLAIMED, 
					Model_Newsroom_Claim::STATUS_REJECTED, Model_Newsroom_Claim::STATUS_IGNORED));
		$results = Model_Newsroom::from_db_all($query, array('sales_agent' => 'Model_Sales_Agent'));		
		
		return $results;
	}

	protected function render_claim_submissions_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
				
		$this->load->view("admin/header");
		$this->load->view("admin/nr_builder/menu");
		$this->load->view("admin/pre-content");
		$this->load->view("admin/nr_builder/claim_submissions");
		$this->load->view("admin/post-content");
		$this->load->view("admin/footer");
	}

	public function claim_bulk_action()
	{
		$nr_source = $this->nr_source;

		if (!$this->input->post('bulk_confirm_btn') && !$this->input->post('bulk_reject_btn') && 
			!$this->input->post('bulk_ignore_btn'))
			$this->redirect("admin/nr_builder/{$nr_source}/claim_submissions_from_private_link");

		$claim_ids = $this->input->post('selected');

		if (!is_array($claim_ids) || !count($claim_ids))
		{
			$feedback = new Feedback('error', 'Error!', 'Please select claims to perform bulk action');
			$this->add_feedback($feedback);
			$this->redirect("admin/nr_builder/{$nr_source}/claim_submissions_from_private_link");
		}

		$is_bulk = 1;
		if ($this->input->post('bulk_confirm_btn'))
		{
			$feedback_msg = "Claims confirmed successfully.";
			
			foreach ($claim_ids as $claim_id)
				$this->confirm_claim($claim_id, $is_bulk);
		}
		
		if ($this->input->post('bulk_reject_btn'))
		{
			$feedback_msg = "Claims rejected successfully.";

			foreach ($claim_ids as $claim_id)
				$this->reject_claim($claim_id, $is_bulk);
		}
			
		if ($this->input->post('bulk_ignore_btn'))
		{
			$feedback_msg = "Claims ignored successfully.";

			foreach ($claim_ids as $claim_id)
				$this->ignore_claim($claim_id, $is_bulk);
		}

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text($feedback_msg);
		$this->add_feedback($feedback);
		$this->redirect("admin/nr_builder/{$this->nr_source}/claim_submissions_from_private_link");
	}

	protected function fetch_paid_claim_results($chunkination, $filter = null)
	{
		$nr_source = $this->nr_source;

		if (!$filter) $filter = '1';
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('cbc.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);

		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				cbc.company_id AS id FROM 
				{$tbl_prefix}company cbc 
				INNER JOIN 
				ac_nr_newsroom_claim c 
				ON c.company_id = cbc.company_id 
				AND c.status = ?
				WHERE {$filter}
				AND c.is_paid = 1 
				AND cbc.company_id IS NOT NULL
				ORDER BY c.id DESC 
				{$limit_str}";

		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
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
			cl.id AS claim_id,
			cl.rep_name AS claimant_rep_name, 
			cl.email AS claimant_email,
			cl.phone AS claimant_phone,
			cl.date_claimed AS date_claimed,
			cl.remote_addr AS remote_addr,
			cl.is_from_private_link,
			cl.date_exported_to_csv AS date_exported_csv,
			n.company_id AS id,
			u.first_name AS o_user_first_name,
			u.last_name AS o_user_last_name,
			u.email AS o_user_email,
			u.id AS o_user_id
			FROM nr_newsroom n
			LEFT JOIN nr_user u 
			ON n.user_id = u.id			
			LEFT JOIN {$tbl_prefix}company c
			ON c.company_id = n.company_id
			LEFT JOIN ac_nr_newsroom_claim cl
			ON cl.company_id = c.company_id
			AND cl.status = ?
			WHERE n.company_id IN ({$id_str})
			ORDER BY cl.id DESC";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
		$results = Model_Newsroom::from_db_all($query);		
		
		return $results;
	}

	public function ignore_claim($claim_id = null, $is_bulk = 0)
	{
		if (!$claim_id)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($this->nr_source);
		
		$sql = "SELECT * 
				FROM ac_nr_newsroom_claim
				WHERE id = ?";

		$claim = Model::from_sql($sql, $claim_id);
		if (!$claim)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$sql = "UPDATE ac_nr_newsroom_claim
				SET status = ?,
				date_admin_updated = ?
				WHERE id = ?";

		$this->db->query($sql, 
			array(Model_Newsroom_Claim::STATUS_IGNORED, Date::$now->format(DATE::FORMAT_MYSQL), $claim_id));

		if ($is_bulk)
			return;

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Claim ignored successfully.');
		$this->add_feedback($feedback);
		$this->redirect("admin/nr_builder/{$this->nr_source}/claim_submissions");
	}

	public function reject_claim($claim_id = null, $is_bulk = 0)
	{
		if (!$claim_id)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$ci =& get_instance();

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($this->nr_source);
		
		$sql = "SELECT * 
				FROM ac_nr_newsroom_claim
				WHERE id = ?";

		$claim = Model::from_sql($sql, $claim_id);
		if (!$claim)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$sql = "UPDATE ac_nr_newsroom_claim
				SET status = ?,
				date_admin_updated = ?
				WHERE id = ?";

		$this->db->query($sql, 
			array(Model_Newsroom_Claim::STATUS_REJECTED, Date::$now->format(DATE::FORMAT_MYSQL), $claim_id));

		// Send rejection email to the rep.
		$comp = Model_Company::find($claim->company_id);
		$this->vd->claimant_name = $claim->rep_name;
		$this->vd->company_name = $comp->name;
		
		$message = $this->load->view("admin/nr_builder/{$this->nr_source}/emails/reject_claim", null, true);
		$subject = "Verification Failed - Please Contact Us to Verify";

		$email = new Email();
		$email->set_to_email($claim->email);		
		$email->set_to_name($claim->rep_name);
		$email->set_from_email($ci->conf('email_address'));
		$email->set_from_name('Newswire Notification');
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);

		if ($is_bulk)
			return;

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Claim rejected successfully.');
		$this->add_feedback($feedback);
		$this->redirect("admin/nr_builder/{$this->nr_source}/claim_submissions");
	}

	public function confirm_claim($claim_id = null, $is_bulk = 0)
	{
		if (!$claim_id)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$ci =& get_instance();

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($this->nr_source);

		$sql = "SELECT * 
				FROM ac_nr_newsroom_claim
				WHERE id = ?";

		$claim = Model::from_sql($sql, $claim_id);
		if (!$claim)
			$this->redirect("admin/nr_builder/{$this->nr_source}");

		$sql = "UPDATE ac_nr_newsroom_claim
				SET status = ?,
				date_admin_updated = ?
				WHERE id = ?";

		$this->db->query($sql, 
			array(Model_Newsroom_Claim::STATUS_CONFIRMED, Date::$now->format(DATE::FORMAT_MYSQL), $claim_id));

		$sql = "SELECT * 
				FROM ac_nr_newsroom_claim
				WHERE company_id = ?";

		$cb_comp = Model::from_sql($sql, array($claim->company_id));

		$newsroom = Model_Newsroom::find($claim->company_id);
		$comp = Model_Company::find($claim->company_id);
		$m_user = Model_User_Base::find('email', $claim->email);
		

		if ($user = Model_User::find(@$m_user->id))
		{
			$pass = Model_User::generate_alphanumeric_password(6);
			$user->set_password($pass);
			$user->save();
			$user_email = $user->email;
		}
		else
		{
			$user = Model_User::create();
			$pass = Model_User::generate_alphanumeric_password(6);
			$user->set_password($pass);
			$user->first_name = $comp->name;
			$user->last_name = '';
			$user->email = $claim->email;
			$user->is_admin = 0;
			$user->is_reseller = 0;
			$user->is_enabled = 1;
			$user->is_verified = 1;
			$user->save();

			$user_email = $claim->email;
		}

		$comp->user_id = $user->id;
		$comp->save();

		$newsroom->is_active = 1;
		$newsroom->save();

		// Send confirmation email to the rep.
		$this->vd->claimant_name = $claim->rep_name;
		$this->vd->company_name = $comp->name;
		$this->vd->email = $user_email;
		$this->vd->password = $pass;
		
		$message = $this->load->view("admin/nr_builder/{$this->nr_source}/emails/confirm_claim", null, true);
		$subject = "Verification Complete - Your Company Newsroom Login Details";

		$email = new Email();
		$email->set_to_email($claim->email);		
		$email->set_to_name($claim->rep_name);
		$email->set_from_email($ci->conf('email_address'));
		$email->set_from_name('Newswire Notification');
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);

		if ($is_bulk)
			return;

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Claim confirmed successfully.');
		$this->add_feedback($feedback);
		$this->redirect("admin/nr_builder/{$this->nr_source}/claim_submissions");
	}

	public function delete($id)
	{
		if (!$id) return;

		$nr_source = $this->nr_source;
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);

		$sql = "SELECT * 
				FROM {$tbl_prefix}company
				WHERE id = ?";

		$comp = Model::from_sql($sql, $id);

		$src_company_id_field = "{$nr_source}_company_id";
		if ($nr_source == Model_Company::SOURCE_CRUNCHBASE)
			$src_company_id_field = "company_id";

		$sql = "SELECT * 
				FROM {$tbl_prefix}company_data
				WHERE {$src_company_id_field} = ?";

		$c_data = Model::from_sql($sql, array($id));		

		if ($this->input->post('confirm'))
		{
			$sql = "DELETE FROM {$tbl_prefix}company
					WHERE id = {$id}";

			$this->db->query($sql);

			$sql = "DELETE FROM {$tbl_prefix}company_data
					WHERE {$src_company_id_field} = {$id}";

			$this->db->query($sql);

			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Company deleted successfully.');
			$this->add_feedback($feedback);
			$this->redirect("admin/nr_builder/{$nr_source}");
		}
		else
		{
			// load confirmation feedback 
			$this->vd->comp = $comp;
			$this->vd->c_data = $c_data;
			$this->vd->company_id = $id;
			$feedback_view = "admin/nr_builder/{$nr_source}/partials/company_delete_before_feedback";
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($id);
		}
	}
}

?>