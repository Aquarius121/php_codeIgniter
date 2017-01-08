<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Company_Contact extends Model {
	
	const MAX_SLUG_LENGTH = 64;

	const SOURCE_NEWSWIRE 	= 'newswire';
	const SOURCE_MYNEWSDESK = 'mynewsdesk';
	const SOURCE_PR_CO		= 'pr_co';
	
	protected static $__table = 'nr_company_contact';
	protected static $__primary = 'id';
	
	public function __get($name)
	{
		if ($name === 'name')
			return $this->name();
		return parent::__get($name);
	}

	public function url()
	{
		// the relative url of the contact
		// * works on newsroom only
		return "contact/{$this->slug}";
	}

	public function name()
	{
		return trim(sprintf('%s %s', 
			$this->first_name, 
			$this->last_name));
	}
	
	public function name_to_slug()
	{
		$this->id = (int) $this->id;
		$this->company_id = (int) $this->company_id;
		$this->slug = Slugger::create($this->name(), static::MAX_SLUG_LENGTH);
		
		while (true)
		{
			$result = $this->db->query(
				"SELECT 1 FROM nr_company_contact
				 WHERE slug = ? AND company_id = ? AND id != ?", 
			array($this->slug, $this->company_id, $this->id));
			if (!$result->num_rows()) return $this->slug;
			$this->slug = Slugger::create_with_random($this->name(), static::MAX_SLUG_LENGTH);
		}
	}
	
}

?>