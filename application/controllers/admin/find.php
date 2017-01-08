<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Find_Controller extends Admin_Base {

	public function find_company()
	{
		$filter = $this->create_filter();
		$find_email = $this->input->post('find_email');
		$find_company = $this->input->post('find_company');
		$u_prefixes = Model_User::__prefixes('u');

		if (!$find_email && !$find_company && 
			 $recents = Admo::recent_newsrooms())
		{
			$recents = sql_in_list($recents);
			$sql = "SELECT c.id, c.name, c.newsroom, 
				{$u_prefixes}
				FROM nr_company c INNER JOIN 
				nr_user u ON c.user_id = u.id
				WHERE c.id IN ({$recents})
				  AND {$filter}
				ORDER BY FIELD(c.id, {$recents}) DESC
				LIMIT 10";
		}
		else
		{	
			$find_email = sql_loose_term($find_email);
			$find_company = sql_loose_term($find_company);
			$sql = "SELECT c.id, c.name, c.newsroom, 
				{$u_prefixes}
				FROM nr_company c INNER JOIN 
				nr_user u ON c.user_id = u.id
				WHERE c.name LIKE '%{$find_company}%'
				  AND u.email LIKE '%{$find_email}%'
				  AND {$filter}
				ORDER BY c.id DESC
				LIMIT 10";
		}

		$dbr = $this->db->query($sql);
		$this->vd->companies = Model_Company::from_db_all($dbr);

		$this->load->view('admin/find/find_company');
	}

	public function find_user()
	{
		$filter = $this->create_filter();

		if (!($find_email = $this->input->post('find_email')) && 
			 $recents = Admo::recent_users())
		{
			$recents = sql_in_list($recents);
			$sql = "SELECT u.* FROM nr_user u 
				WHERE u.id IN ({$recents})
				  AND {$filter}
				ORDER BY FIELD(u.id, {$recents}) DESC
				LIMIT 10";
		}
		else
		{
			$find_email = sql_loose_term($find_email);
			$sql = "SELECT u.* FROM nr_user u 
				WHERE ( u.email LIKE '%{$find_email}%'
				     OR u.virtual_source_email LIKE '%{$find_email}%' )
				  AND {$filter}
				ORDER BY u.id DESC
				LIMIT 10";
		}

		$dbr = $this->db->query($sql);
		$this->vd->users = Model_User::from_db_all($dbr);

		$this->load->view('admin/find/find_user');
	}

	public function find_site()
	{
		if (!($find_site = $this->input->post('find_site')))
		{
			$sql = "SELECT vs.* FROM nr_virtual_source vs 
				WHERE vs.is_common = 1
				ORDER BY vs.id DESC
				LIMIT 10";
		}
		else
		{
			$find_site = sql_loose_term($find_site);
			$sql = "SELECT vs.* FROM nr_virtual_source vs 
				WHERE vs.name LIKE '%{$find_site}%'
				ORDER BY vs.is_common DESC, vs.id DESC
				LIMIT 10";
		}

		$dbr = $this->db->query($sql);
		$this->vd->sites = Model_Virtual_Source::from_db_all($dbr);

		$this->load->view('admin/find/find_site');
	}

	protected function create_filter($filter = 1)
	{
		if ($_filter = $this->input->post('filter'))
		{
			$_filter = Raw_Data::from_array($_filter);
			
			if ($_filter->site)
			{
				$filter_site = (int) $_filter->site;
				if ($filter_site === -1)
				     $filter = "{$filter} AND IFNULL(u.virtual_source_id, 0) = 0";
				else $filter = "{$filter} AND u.virtual_source_id = {$filter_site}";
			}
		}

		return $filter;
	}
	
}

?>