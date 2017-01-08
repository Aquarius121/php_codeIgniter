<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Export_Stats_Controller extends Admin_Base {

	public $title = "Export Stats | Newsroom Builder";
	const LISTING_CHUNK_SIZE = 20;

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);

		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/nr_builder/export_stats/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_export_stats_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			$url = "admin/nr_builder/export_stats";
			$this->redirect(gstring($url));
		}

		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$this->load->view('admin/header');
		$this->load->view('admin/nr_builder/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/export_stats');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}	

	protected function fetch_export_stats_results($chunkination, $filter = null)
	{
		if (!$filter) $filter = '1';
		$limit_str = $chunkination->limit_str();

		$exp_filters = array();
		$exp_filters[] = Model_Auto_Built_NR_Export::FILTER_EXPORT_ALL_NOT_EXPORTED;
		$exp_filters[] = Model_Auto_Built_NR_Export::FILTER_EXPORT_SELECTED;
		$exp_str = sql_in_list($exp_filters);

		$sa_prefixes = Model_Sales_Agent::__prefixes('sa');
		$sql = "SELECT SQL_CALC_FOUND_ROWS ne.*,
			sa.first_name AS agent_first_name, 
			sa.last_name AS agent_last_name,
			v_lead_counter.lead_count,
			ne.date_exported AS date_exported_csv,
			'NR' AS export_type
			FROM ac_nr_auto_built_nr_export ne
			LEFT JOIN nr_sales_agent sa
			ON ne.sales_agent_id = sa.id

			INNER JOIN
				(SELECT auto_built_nr_export_id, COUNT(company_id) AS lead_count
					FROM ac_nr_auto_built_nr_export_x_company
					GROUP BY auto_built_nr_export_id) AS v_lead_counter
			ON v_lead_counter.auto_built_nr_export_id = ne.id
			WHERE filter IN ({$exp_str})

			UNION

			SELECT ne.*,
			sa.first_name AS agent_first_name, 
			sa.last_name AS agent_last_name,
			v_lead_counter.lead_count,
			ne.date_exported AS date_exported_csv,
			'Verified' AS export_type
			FROM ac_nr_auto_built_verified_export ne
			LEFT JOIN nr_sales_agent sa
			ON ne.sales_agent_id = sa.id

			INNER JOIN
				(SELECT auto_built_verified_export_id, COUNT(company_id) AS lead_count
					FROM ac_nr_auto_built_verified_export_x_company
					GROUP BY auto_built_verified_export_id) AS v_lead_counter
			ON v_lead_counter.auto_built_verified_export_id = ne.id
			WHERE filter IN ({$exp_str})

			ORDER BY date_exported_csv DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$results = Model_Auto_Built_NR_Export::from_db_all($query, array('sales_agent' => 'Model_Sales_Agent'));

		return $results;
	}

	public function download_nr_csv($export_id = null)
	{
		if (!$export_id)
			$this->redirect('admin/nr_builder/export_stats');

		if (!$ab_export = Model_Auto_Built_NR_Export::find($export_id))
			$this->redirect('admin/nr_builder/export_stats');
		
		$sales_agent = Model_Sales_Agent::find($ab_export->sales_agent_id);

		$sql = "SELECT nr.company_id, nr.source 
				FROM ac_nr_auto_built_nr_export_x_company xc
				INNER JOIN nr_newsroom nr
				ON xc.company_id = nr.company_id
				WHERE xc.auto_built_nr_export_id = ?";

		
		$query = $this->db->query($sql, array($export_id));
		$results = Model_Newsroom::from_db_all($query);
		$ids = Model_Newsroom::values_from_db($query, 'company_id');

		$nr_source = null;
		foreach ($results as $result)
			if (!empty($result->source))
			{
				$nr_source = $result->source;
				break;
			}
		
		if (!$nr_source)
			$this->redirect('admin/nr_builder/export_stats');
		
		$filter = 1; 
		if (is_array($ids) && count($ids))
		{
			$ids_list = sql_in_list($ids);
			$filter = "n.company_id IN ({$ids_list})";
		}
		else
			$this->redirect('admin/nr_builder/export_stats');

		$date_exported = $ab_export->date_exported;

		$csv = new CSV_Writer('php://memory');
		
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);
		
		$src_company_id_field = "{$nr_source}_company_id";
		if ($nr_source == Model_Company::SOURCE_CRUNCHBASE)
			$src_company_id_field = "company_id";

		$sql = "SELECT pc.id, pcd.email, 
				pcd.contact_name,
				pcd.phone,
				pcd.website,
				pcd.contact_page_url,
				n.company_name,
				n.date_created,
				n.*, c.newsroom,
				ct.token AS token
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
				WHERE {$filter} ORDER BY 
				n.company_id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Newsroom::from_db_all($query);
		$found_ids = array();

		$row = array("Email", "Company", "URL", "Date Created", "Date Exported", "Contact Name", "Phone", 
				"Website", "Contact Us URL");
		$csv->write($row);

		$company_ids = array();
		foreach ($results as $result)
		{
			$row = array();
			$row['email'] = $result->email;
			$row['company_name'] = $result->company_name;
			$row['claim_url'] = "{$result->url()}c/{$result->token}";
			$row['date_created'] = $result->date_created;
			$row['date_exported'] = $date_exported;
			$row['contact_name'] = $result->contact_name;
			$row['phone'] = $result->phone;
			$row['website'] = $result->website;
			$row['contact_page_url'] = $result->contact_page_url;
			$found_ids[] = $result->id;
			$csv->write($row);
		}		
		
		if (!count($found_ids))
			$this->redirect('admin/nr_builder/export_stats');
		

		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');

		$sales_agent_prefix = null;
		if ($sales_agent)
			$sales_agent_prefix = "{$sales_agent->first_name}_";
		
		$count = count($results);
		force_download("{$sales_agent_prefix}{$count}_NR_{$date_exported}.csv", stream_get_contents($handle));

		return;		
	}

	public function download_verified_csv($export_id = null)
	{
		if (!$export_id)
			$this->redirect('admin/nr_builder/export_stats');

		if (!$ab_export = Model_Auto_Built_Verified_Export::find($export_id))
			$this->redirect('admin/nr_builder/export_stats');
		
		$sales_agent = Model_Sales_Agent::find($ab_export->sales_agent_id);

		$sql = "SELECT nr.company_id, nr.source 
				FROM ac_nr_auto_built_verified_export_x_company xc
				INNER JOIN nr_newsroom nr
				ON xc.company_id = nr.company_id
				WHERE xc.auto_built_verified_export_id = ?";

		
		$query = $this->db->query($sql, array($export_id));
		$results = Model_Newsroom::from_db_all($query);
		$ids = Model_Newsroom::values_from_db($query, 'company_id');

		$nr_source = null;
		foreach ($results as $result)
			if (!empty($result->source))
			{
				$nr_source = $result->source;
				break;
			}

		if (!$nr_source)
			$this->redirect('admin/nr_builder/export_stats');
		
		
		if (is_array($ids) && count($ids))
		{
			$ids_list = sql_in_list($ids);
			$filter = "n.company_id IN ({$ids_list})";
		}
		else
			$this->redirect('admin/nr_builder/export_stats');

		$date_exported = $ab_export->date_exported;

		$csv = new CSV_Writer('php://memory');
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);
		
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
		
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
		$results = Model_Newsroom::from_db_all($query);
		$found_ids = array();

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
			$csv->write($row);
		}

		if (!count($found_ids))
			$this->redirect('admin/nr_builder/export_stats');
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');

		$sales_agent_prefix = null;
		if ($sales_agent)
			$sales_agent_prefix = "{$sales_agent->first_name}_";
		
		$count = count($results);
		force_download("{$sales_agent_prefix}{$count}_Verified_{$date_exported}.csv", stream_get_contents($handle));
		return;
	}
}


?>