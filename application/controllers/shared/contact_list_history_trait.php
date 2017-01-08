<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Contact_List_History_Trait {
	
	protected function add_history_modal()
	{
		$history_modal = new Modal();
		$history_modal->set_title('List History');
		$this->add_eob($history_modal->render(450, 430));
		$this->vd->history_modal_id = $history_modal->id;
	}
	
	protected function render_history_modal($list_id)
	{
		$list_id = (int) $list_id;
		$sql = "SELECT cla.*, cla.id AS action_id, 
				clad.id AS action_detail_id,
				clad.detail, clad.value,
				aif.name AS csv_file_name,
				st.text AS search_text,
				md.media_type,
				b.name AS beat_name,
				cr.role AS role,
				cc.coverage AS coverage,
				c.name AS country_name,
				r.name AS region_name,
				l.name AS locality_name
				FROM nr_contact_list_action cla
				INNER JOIN nr_contact_list_action_detail clad
				ON clad.contact_list_action_id = cla.id
				LEFT JOIN nr_contact_list_action_import_file aif
				ON clad.value = aif.stored_file_id
				LEFT JOIN nr_contact_list_action_mdb_search_text st
				ON clad.value = st.id
				LEFT JOIN nr_beat b
				ON clad.value = b.id
				LEFT JOIN nr_contact_media_type md
				ON clad.value = md.id
				LEFT JOIN nr_contact_role cr
				ON clad.value = cr.id
				LEFT JOIN nr_contact_coverage cc
				ON clad.value = cc.id
				LEFT JOIN nr_country c
				ON clad.value = c.id
				LEFT JOIN nr_region r
				ON clad.value = r.id
				LEFT JOIN nr_locality l
				ON clad.value = l.id
				
				WHERE cla.contact_list_id = '{$list_id}'
				ORDER BY cla.id, clad.id";

		$query = $this->db->query($sql);
		$results = Model_Contact_List_Action::from_db_all($query);

		$actions = array();
		$details = array();		
		$prev_action_id = 0;

		foreach ($results as $result)
		{
			if ($result->action_id != $prev_action_id)
			{
				$action = new stdClass();
				$action->id = $result->action_id;
				$action->date_action_taken = $result->date_action_taken;
				$action->type = $result->type;

				if ($prev_action_id)
				{
					$prev_action->details = $details;
					$details = array();
				}

				$prev_action = $action;
				$prev_action_id = $result->action_id;
				$actions[] = $action;
			}

			$details[] = $result;
		}

		if ($prev_action_id)
			$prev_action->details = $details;
		
		$this->vd->actions = $actions;
		$this->load->view('manage/contact/partials/list_history_modal');
	}
}

?>