<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Filters_To_Tag_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		set_memory_limit('2048M');
		set_time_limit(86400);
		
		$regions = Model_Region::find_all();
		$localities = Model_Locality::find_all();
		$countries = Model_Country::find_all();
		$media_types = Model_Contact_Media_Type::find_all();
		$roles = Model_Contact_Role::find_all();

		foreach ($regions as $region)
		{
			$tags = $this->parse_tags($region->name);
			foreach ($tags as $tag)
			{
				$tag = Tag::uniform(trim($tag));
				if (!trim($tag)) continue;
				$sql = "INSERT IGNORE INTO nr_contact_linked_tags
					(value, class, linked, relevance) VALUES (?, 'regions', ?, 1)";
				$this->db->query($sql, array($tag, $region->id));
				$this->trace('region', $region->id, $tag);
			}			
		}

		foreach ($countries as $country)
		{
			$tags = $this->parse_tags($country->name);
			foreach ($tags as $tag)
			{
				$tag = Tag::uniform(trim($tag));
				if (!trim($tag)) continue;
				$sql = "INSERT IGNORE INTO nr_contact_linked_tags
					(value, class, linked, relevance) VALUES (?, 'countries', ?, 1)";
				$this->db->query($sql, array($tag, $country->id));
				$this->trace('country', $country->id, $tag);
			}
		}

		foreach ($localities as $locality)
		{
			$tags = $this->parse_tags($locality->name);
			foreach ($tags as $tag)
			{
				$tag = Tag::uniform(trim($tag));
				if (!trim($tag)) continue;
				$sql = "INSERT IGNORE INTO nr_contact_linked_tags
					(value, class, linked, relevance) VALUES (?, 'localities', ?, 1)";
				$this->db->query($sql, array($tag, $locality->id));
				$this->trace('locality', $locality->id, $tag);
			}
		}

		foreach ($roles as $role)
		{
			$tags = $this->parse_tags($role->role);
			foreach ($tags as $tag)
			{
				$tag = Tag::uniform(trim($tag));
				if (!trim($tag)) continue;
				$sql = "INSERT IGNORE INTO nr_contact_linked_tags
					(value, class, linked, relevance) VALUES (?, 'roles', ?, 1)";
				$this->db->query($sql, array($tag, $role->id));
				$this->trace('role', $role->id, $tag);
			}
		}

		foreach ($media_types as $media_type)
		{
			$tags = $this->parse_tags($media_type->media_type);
			foreach ($tags as $tag)
			{
				$tag = Tag::uniform(trim($tag));
				if (!trim($tag)) continue;
				$sql = "INSERT IGNORE INTO nr_contact_linked_tags
					(value, class, linked, relevance) VALUES (?, 'media_types', ?, 1)";
				$this->db->query($sql, array($tag, $media_type->id));
				$this->trace('media_type', $media_type->id, $tag);
			}
		}

		$sql = "UPDATE nr_contact_linked_tags SET 
			hash = MD5(CONCAT(class, linked))";
		$this->db->query($sql);
	}
	
	protected function parse_tags($text)
	{
		$words = preg_split('#\s+#', $text);
		foreach ($words as $word)
			$tags[] = Tag::uniform($word);
		return $tags;
	}

}

?>