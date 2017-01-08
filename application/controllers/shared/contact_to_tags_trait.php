<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Contact_To_Tags_Trait {
			
	protected function generate_tags($contact)
	{
		$tags = array();	
		$tags = array_merge($tags, $this->parse_tags($contact->twitter));
		$tags = array_merge($tags, $this->parse_tags($contact->company_name));
		$tags = array_merge($tags, $this->parse_tags($contact->first_name));
		$tags = array_merge($tags, $this->parse_tags($contact->last_name));
		
		if ($region = Model_Region::find($contact->region_id))
			$tags = array_merge($tags, $this->parse_tags($region->name));				
		if ($locality = Model_Locality::find($contact->locality_id))
			$tags = array_merge($tags, $this->parse_tags($locality->name));				
		if ($country = Model_Country::find($contact->country_id))
			$tags = array_merge($tags, $this->parse_tags($country->name));				
		if ($contact_role = Model_Contact_Role::find($contact->contact_role_id))
			$tags = array_merge($tags, $this->parse_tags($contact_role->role));
		if ($beat = Model_Beat::find($contact->beat_1_id))
			$tags = array_merge($tags, $this->parse_tags($beat->name));
		if ($beat = Model_Beat::find($contact->beat_2_id))
			$tags = array_merge($tags, $this->parse_tags($beat->name));
		if ($beat = Model_Beat::find($contact->beat_3_id))
			$tags = array_merge($tags, $this->parse_tags($beat->name));
		
		return $tags;
	}
	
	protected function parse_tags($text)
	{
		$words = preg_split('#\s+#', $text);
		foreach ($words as $word)
			$tags[] = Tag::uniform($word);
		return $tags;
	}

}