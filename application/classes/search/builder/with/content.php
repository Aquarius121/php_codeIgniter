<?php

class Search_Builder_With_Content extends Search_Builder {

	protected $indexTable = 'nr_search_index_content';
	protected $termsTable = 'nr_search_term';
	protected $otherField = 'content_id';

	public function build(Model_Content $mContent)
	{
		$mContent->load_content_data();
		$mContent->load_local_data();
		$mNewsroom = $mContent->newsroom();
		$terms = new StringKey_Array();

		if ($mContent->type === Model_Content::TYPE_PR)
		{
			$tagsString = $mContent->get_tags_string();
			$contentBody = HTML2Text::plain($mContent->content);
			$beatsString = implode(' ', array_map(function($i) {
				return $i->name;
			}, $mContent->get_beats()));

			$this->build_terms($terms, $mContent->title, 100);
			$this->build_terms($terms, $tagsString, 75);
			$this->build_terms($terms, $mNewsroom->company_name, 50);
			$this->build_terms($terms, $beatsString, 10, 50);
			$this->build_terms($terms, $mContent->summary, 10, 50);
			$this->build_terms($terms, $contentBody, 5, 50);
		}
		else
		{
			$this->build_terms($terms, $mContent->title, 100);
			$this->build_terms($terms, $mContent->get_tags_string(), 75);
			$this->build_terms($terms, $mNewsroom->company_name, 50);
		}

		$this->insert_terms($terms);
		$this->build_index($terms, 
			(int) $mContent->id);

		return $terms;
	}

}