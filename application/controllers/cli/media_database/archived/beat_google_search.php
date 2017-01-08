<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('cli/contact_keyword_builder/google_search');

class Beat_Google_Search_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		set_memory_limit('2048M');
		set_time_limit(86400);
		
		$sql = "SELECT b.* FROM nr_beat b 
			INNER JOIN nr_beat_group bg
			ON b.beat_group_id = bg.id
			WHERE b.beat_group_id != b.id
			ORDER BY bg.name, b.name";
		$dbr = $this->db->query($sql);
		
		$gs = new Google_Search();
		Model_Beat_Group::enable_cache();
		$beats = Model_Beat::from_db_all($dbr);
		foreach ($beats as $beat)
		{
			$group = Model_Beat_Group::find($beat->beat_group_id);
			if (!$group) continue;
			
			// already saved for this beat
			if (__beats_google_urls::find($beat->id))
				continue;
			
			$query = null;
			$parts = array();
			if ($beat->needs_group)
				$parts = array_merge($parts, preg_split('#\b(,|and|or)\b#i', $group->name));
			$parts = array_merge($parts, preg_split('#\b(,|and|or)\b#i', $beat->name));
			$parts = array_unique($parts);
			
			foreach ($parts as $part)
			{
				$part = trim($part);
				$query = sprintf('%s "%s"', $query, $part);
			}
			
			$links = $gs->search($query);
			
			if ($links === false)
			{
				$this->inspect($gs->error);
				return;
			}
			
			$smodel = new __beats_google_urls();
			$smodel->beat_id = $beat->id;
			$smodel->raw_data($links);
			$smodel->save();
			
			$this->trace($query, count($links));
			sleep(30);
		}
	}

}

// temporary model class 
class __beats_google_urls extends Model {
	use Raw_Data_Trait;
	protected static $__table = '__beats_google_urls';
	protected static $__primary = 'beat_id';
}

?>