<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('distribution/base');

class Generic_Controller extends Distribution_Base {

	protected $view = 'distribution/generic';
	protected $stats = true;

	public function __on_execution_start()
	{
		$this->vd->feed_url = $this->env['requested_uri'];
		$this->vd->injects = new stdClass();
		$this->vd->injects->post_item = null;
	}

	public function index()
	{
		$sql = "SELECT c.id FROM nr_content c 
			INNER JOIN nr_pb_pr p
			ON p.content_id = c.id
			WHERE c.is_published = 1 
			AND c.type = ? 
			AND c.is_premium = 1
			AND c.date_publish < UTC_TIMESTAMP()
			AND p.is_distribution_disabled = 0
			AND p.is_external = 0
			ORDER BY c.date_publish DESC
			LIMIT 200";

		$dt_last_modified = Date::first();
		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		foreach ($dbr->result() as $result)
			$id_list[] = (int) $result->id;

		$this->_index_id_list($id_list);
	}

	protected function _index_id_list($id_list)
	{
		if (!count($id_list))
			return $this->_index_sql();

		$id_list_str = sql_in_list($id_list);
		$sql = "SELECT c.id,
			c.title,
			c.slug,
			c.company_id,
			c.date_publish,
			c.date_created,
			c.date_updated,
			c.uuid,
			c.type, 
			c.cover_image_id,
			cd.summary,
			cm.name as c_name,
			cc.first_name as c_contact_first_name, 
			cc.last_name as c_contact_last_name,
			cc.phone as c_contact_phone,
			cp.website as c_website, 
			cp.address_street as c_address_street, 
			cp.address_apt_suite as c_address_apt_suite, 
			cp.address_zip as c_address_zip,
			cp.phone as c_phone, 
			ct.name as c_address_country,
			cp.address_state as c_address_state, 
			cp.address_city as c_address_city,
			cd.content, be.name as beat_name,
			tl.web_video_provider,
			tl.web_video_id,
			tl.stored_file_id_1,
			tl.stored_file_id_2,
			tl.stored_file_name_1,
			tl.stored_file_name_2,
			cd.rel_res_pri_title,
			cd.rel_res_pri_link,
			cd.rel_res_sec_title,
			cd.rel_res_sec_link,
			si_logo.filename as c_logo_filename,
			si_cover.filename as c_cover_filename,
			si_cover_full.filename as c_cover_full_filename,
			tl.location as location,
			u.id as user_id,
			u.virtual_source_id,
			vs.name as virtual_source_name,
			vs.website as virtual_source_website
			FROM nr_content c INNER JOIN nr_pb_pr tl ON 
				c.id IN ({$id_list_str}) AND c.id = tl.content_id
			INNER JOIN nr_content_data cd ON c.id = cd.content_id
			INNER JOIN nr_company cm ON c.company_id = cm.id 
			LEFT JOIN nr_company_profile cp ON cm.id = cp.company_id 
			LEFT JOIN nr_company_contact cc ON cm.company_contact_id = cc.id 
			LEFT JOIN nr_country ct ON cp.address_country_id = ct.id 
			LEFT JOIN nr_beat_x_content bxc ON bxc.content_id = c.id
			LEFT JOIN nr_beat be ON be.id = bxc.beat_id 
			LEFT JOIN nr_newsroom_custom nc ON c.company_id = nc.company_id 
			LEFT JOIN nr_image_variant iv_logo ON nc.logo_image_id = iv_logo.image_id AND iv_logo.name = 'header-sidebar'
			LEFT JOIN nr_stored_image si_logo ON iv_logo.stored_image_id = si_logo.id
			LEFT JOIN nr_image_variant iv_cover ON c.cover_image_id = iv_cover.image_id AND iv_cover.name = 'cover-feed'
			LEFT JOIN nr_stored_image si_cover ON iv_cover.stored_image_id = si_cover.id
			LEFT JOIN nr_image_variant iv_cover_full ON c.cover_image_id = iv_cover_full.image_id AND iv_cover_full.name = 'original'
			LEFT JOIN nr_stored_image si_cover_full ON iv_cover_full.stored_image_id = si_cover_full.id
			LEFT JOIN nr_user u ON cm.user_id = u.id
			LEFT JOIN nr_virtual_source vs ON u.virtual_source_id = vs.id
			GROUP BY c.id
			ORDER BY GREATEST(c.date_publish, c.date_updated) DESC";

		$this->_index_sql($sql);
	}

	protected function _index_sql($sql = false, $results = array())
	{	
		if ($sql !== null && $sql !== false)
		{
			$dbr = $this->db->query($sql);
			$results = Model_Content::from_db_all($dbr);
		}

		$this->vd->results = $results;
		$stats_enabled = $this->stats && 
			$this->conf('stats_enabled');
		$dt_last_modified = Date::first();

		foreach ($results as $result)
		{
			if ($result->date_updated)
			     $dt_updated = Date::utc($result->date_updated);
			else $dt_updated = Date::utc($result->date_created);
			if ($dt_updated > Date::$now) continue;
			$dt_last_modified = max($dt_last_modified, $dt_updated);

			if ($stats_enabled)
			{
				$m_newsroom = new Model_Newsroom();
				$m_newsroom->user_id = $result->user_id;
				$m_newsroom->company_id = $result->company_id;
			
				$builder = new Stats_URI_Builder();
				$builder->add_content_view($m_newsroom, $result);
				$builder->add_remote_content_view($result, $this->uri->uri_string);
				$result->tracking_uri = $builder->build(null, true);
			}

			foreach ($result as $k => & $v)
				if (is_string($v)) $v = $this->_text_filter($v);
			$result->content = $this->_html_filter($result->content);
		}

		$output = trim($this->load->view($this->view, null, true));
		$last_modified_str = $dt_last_modified->format(Date::FORMAT_RFC7231);
		$date_str = Date::$now->format(Date::FORMAT_RFC7231);

		ob_clean();
		header("Content-Type: application/xml; charset=UTF-8");
		header("Last-Modified: {$last_modified_str}");
		header("Date: {$date_str}");
		echo $output;
		
		exit();
	}

	protected function _text_filter($text)
	{
		return $text;
	}

	protected function _html_filter($html)
	{
		return $html;
	}

}
