<?php

class Search_Query_With_Content extends Search_Query {

	protected $indexTable = 'nr_search_index_content';
	protected $termsTable = 'nr_search_term';
	protected $otherField = 'content_id';
	protected $buildLimit = 10000;

}