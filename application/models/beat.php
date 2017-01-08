<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Beat extends Model {
	
	const MAX_SLUG_LENGTH = 64;

	protected static $__table = 'nr_beat';
	protected static $__url_prefix = 'browse/beat/';

	public static function list_all_beats_by_group($list = null)
	{
		$filter = 1;
		$groups = array();
		$ci =& get_instance();

		if (is_array($list) && count($list))
		{
			// only fetch beats that match
			// the provided list of ids
			$list_str = sql_in_list($list);
			$filter = "b.id IN ({$list_str})";
		}

		$sql = "SELECT b.id, b.name, b.slug,
			b.beat_group_id as group_id,
			b.is_listed,
			g.name AS group_name,
			g.slug AS group_slug,
			g.is_listed as group_is_listed
			FROM nr_beat b INNER JOIN nr_beat_group g
			ON b.beat_group_id = g.id
			WHERE {$filter}
			ORDER BY g.name ASC,
				b.name ASC";
		
		$query = $ci->db->cached(3600)->query($sql);
		$current_group = new Model_Beat_Group();
		$current_group->id = -1;
		
		foreach ($query->result() as $result)
		{
			if ((int) $result->group_id !== $current_group->id)
			{
				$current_group = new Model_Beat_Group();
				$current_group->id = (int) $result->group_id;
				$current_group->name = $result->group_name;
				$current_group->slug = $result->group_slug;
				$current_group->is_listed = $result->group_is_listed;
				$current_group->beats = array();
				$groups[] = $current_group;
			}
			
			$current_group->beats[] = $result;
		}
		
		return $groups;
	}

	public static function get_beat_id_for_name($name)
	{
		$beat = get_instance()->db
			->select('id')
			->from('nr_beat')
			->like('name', $name)
			->limit(1)
			->get();

		if ($beat->num_rows() === 0)
			return 0;

		return $beat->row()->id;
	}
	
	public function get_group_name()
	{
		$group = $this->db
			->select('name')
			->from('nr_beat_group')
			->where('id', $this->beat_group_id)
			->get()
			->row();
			
		return $group->name;
	}

	public function title_to_slug($beat_group_slug = null)
	{
		$this->slug = static::generate_slug($this->name, (int) $this->id, $beat_group_slug);
	}

	public function url() 
	{
		return static::$__url_prefix . $this->slug;
	}

	public static function find_slug($slug)
	{
		return static::find('slug', $slug);
	}

	public static function generate_slug($title, $existing_id = 0, $beat_group_slug = null)
	{
		$ci =& get_instance();
		$slug = Slugger::create($title, static::MAX_SLUG_LENGTH);

		if ($beat_group_slug)
			$slug = "{$beat_group_slug}-{$slug}";

		$sql = "SELECT 1 FROM nr_beat WHERE slug = ? AND id != ?";
		
		while (true)
		{			
			$params = array($slug, (int) $existing_id);
			$result = $ci->db->query($sql, $params);
			if (!$result->num_rows()) return $slug;
			$slug = Slugger::create_with_random($title, static::MAX_SLUG_LENGTH);
		}
	}
	
}

?>