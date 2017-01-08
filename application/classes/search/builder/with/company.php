<?php

class Search_Builder_With_Company extends Search_Builder {

	protected $indexTable = 'nr_search_index_company';
	protected $termsTable = 'nr_search_term';
	protected $otherField = 'company_id';

	public function build(Model_Company $mCompany)
	{
		$terms = new StringKey_Array();
		$this->build_terms($terms, $mCompany->name, 100);
		if (($mProfile = Model_Company_Profile::find($mCompany->id)))
			$this->build_terms($terms, $mProfile->summary, 10, 50);

		$this->insert_terms($terms);
		$this->build_index($terms, 
			(int) $mCompany->id);

		return $terms;
	}

}