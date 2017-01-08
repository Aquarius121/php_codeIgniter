<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Media_Database_Beats_Trait {

	protected function list_beats_non_zero()
	{
		$sql = "SELECT distinct(cx.beat_id) from (
			  select distinct(c1.beat_1_id) as beat_id
			  from nr_contact c1
			  where c1.is_media_db_contact = 1
			  union all 
			  select distinct(c2.beat_2_id) as beat_id
			  from nr_contact c2
			  where c2.is_media_db_contact = 1
			  union all 
			  select distinct(c3.beat_3_id) as beat_id
			  from nr_contact c3
			  where c3.is_media_db_contact = 1
			) cx";

		$dbr = $this->db->cached->query($sql);
		$beat_ids = Model_Base::values_from_db($dbr, 'beat_id');
		$beats = Model_Beat::list_all_beats_by_group($beat_ids);
		return $beats;
	}

}