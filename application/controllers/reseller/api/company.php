<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');
load_controller('reseller/api/company_trait');

class Company_Controller extends API_Base {
	
	const PER_PAGE = 100;
	
	use Company_Trait;
	
	public function index()
	{
		if (!($chunk = @$this->iella_in->page)) $chunk = 1;
		$archived_str = (int) @$this->iella_in->is_archived;
			
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::PER_PAGE);		
		$limit_str = $chunkination->limit_str();
				
		$user_id = Auth::user()->id;
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			n.company_id, n.company_name
			FROM nr_newsroom n
			WHERE n.user_id = {$user_id} 
			AND n.is_archived = {$archived_str}
			ORDER BY n.company_name ASC
			{$limit_str}";
		
		$dbr = $this->db->query($sql);
		$results = Model_Newsroom::from_db_all($dbr);
		$companies = array();
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;	
		
		foreach ($results as $result)
		{			
			$company = array();
			$company['company_id'] = $result->company_id;
			$company['company_name'] = $result->company_name;
			$companies[] = $company;
		}
		
		$this->iella_out->page = $chunk;
		$this->iella_out->results = $companies;
		$this->iella_out->total_results = $total_results;
		$this->iella_out->results_per_page = static::PER_PAGE;
	}
	
	public function add()
	{
		if ($this->add_company_validation())
			$this->add_company();
	}
	
	public function view()
	{
		if (!isset($this->iella_in->company_id))
		{
			$this->iella_out->errors[] = '<company_id> field is required';
			$this->iella_out->success = false;
			return;
		}
		
		if (!($this->iella_out->result = $this->find_company($this->iella_in->company_id)))
		{
			$this->iella_out->errors[] = 'not found';
			$this->iella_out->success = false;
			return;
		}
	}
	
}

?>
