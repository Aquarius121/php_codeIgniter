<?php 

trait Search_Util_Trait {

	protected function extract_terms($text, $unique = true)
	{
		$text  = strtolower($text);
		$text  = preg_replace('#[^a-z0-9\s]#', null, $text);
		$terms = preg_split('#\s+#', $text);
		$terms = array_filter($terms);
		if ($unique) $terms = array_unique($terms);
		$terms = $this->remove_stop_words($terms);
		return $terms;
	}

	protected function parse_query($text)
	{
		$text  = strtolower($text);
		$text  = preg_replace('#[^a-z0-9\+\-\s]#', null, $text);
		$terms = preg_split('#\s+#', $text);
		$terms = array_filter($terms);
		$terms = array_unique($terms);
		$positive = $terms;
		$negative = $terms;
		
		$positive = array_filter(array_map(function($term) {
			if ($term[0] === '-') return false;
			if ($term[0] === '+') $term = substr($term, 1);
			return preg_replace('#[^a-z0-9]#', null, $term);
		}, $positive));

		$negative = array_filter(array_map(function($term) {
			if ($term[0] !== '-') return false;
			return preg_replace('#[^a-z0-9]#', 
				null, substr($term, 1));
		}, $negative));

		$positive = $this->remove_stop_words($positive);
		$negative = $this->remove_stop_words($negative);

		return (object) array(
			'positive' => $positive,
			'negative' => $negative,
		);
	}

	protected function remove_stop_words($terms)
	{
		$stop_words = $this->stop_words();
		$filtered = array();

		foreach ($terms as $term)
			if (!in_array($term, $stop_words))
				$filtered[] = $term;

		return $filtered;
	}

	protected function stop_words()
	{
		return Search_Stop_Words::$_;
	}

}