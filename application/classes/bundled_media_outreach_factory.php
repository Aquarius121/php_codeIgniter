<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Bundled_Media_Outreach_Factory {

	const CONTACT_LIMIT_BEATS = 100;
	const CONTACT_LIMIT_REGIONS = 50;

	protected $contact_limit_beats;
	protected $contact_limit_regions;
	protected $m_campaign;
	protected $m_campaign_data;
	protected $m_cbc;
	protected $m_content;
	protected $outreach_beats = array();
	protected $outreach_regions = array();
	protected $compiler_callback_beats = null;
	protected $compiler_callback_regions = null;

	public function __construct(Model_Content $m_content)
	{
		$this->contact_limit_beats = static::CONTACT_LIMIT_BEATS;
		$this->contact_limit_regions = static::CONTACT_LIMIT_REGIONS;
		$this->m_content = $m_content;
	}

	public function set_contact_limit_beats($limit)
	{
		$this->contact_limit_beats = $limit;
	}

	public function set_contact_limit_regions($limit)
	{
		$this->contact_limit_regions = $limit;
	}

	public function set_beats($id_beats)
	{
		$outreach_beats = Model_Beat::find_all_in_list($id_beats);
		foreach ($outreach_beats as $k => $v)
			$outreach_beats[$k] = $v->id;
		$this->outreach_beats = $outreach_beats;
	}

	public function set_regions($id_regions)
	{
		$outreach_regions = Model_Region::find_all_in_list($id_regions);
		foreach ($outreach_regions as $k => $v)
			$outreach_regions[$k] = $v->id;
		$this->outreach_regions = $outreach_regions;
	}

	public function get_campaign()
	{
		return $this->m_campaign;
	}

	public function get_campaign_data()
	{
		return $this->m_campaign_data;
	}

	public function set_compiler_callback_beats($callback)
	{
		$this->compiler_callback_beats = $callback;
	}

	public function set_compiler_callback_regions($callback)
	{
		$this->compiler_callback_regions = $callback;
	}
	
	public function create()
	{
		$ci =& get_instance();
		$m_content = $this->m_content;
		if (!$m_content) throw new Exception();
		$m_cbc = Model_Content_Bundled_Campaign::find($m_content->id);
		if (!$m_cbc) $m_cbc = new Model_Content_Bundled_Campaign();

		$outreach_beats = $this->outreach_beats;
		$outreach_regions = $this->outreach_regions;

		if (!count($outreach_beats) && 
			 !count($outreach_regions))
			return null;

		$m_company = Model_Company::find($m_content->company_id);
		$m_company_profile = Model_Company_Profile::find($m_company->id);
		$m_content->load_content_data();
		$m_content->load_local_data();

		$m_campaign = Model_Campaign::find($m_cbc->campaign_id);
		if (!$m_campaign) $m_campaign = new Model_Campaign();

		$m_campaign->is_stats_locked = 1;		
		$m_campaign->content_id = $m_content->id;
		$m_campaign->company_id = $m_company->id;
		$m_campaign->name = $m_content->title;
		$m_campaign->subject = $m_content->title;
		$m_campaign->sender_name = $m_company->name;
		$m_campaign->sender_email = $m_company_profile && 
			$m_company_profile->email
			? $m_company_profile->email
			: $ci->conf('outreach_email');
		$m_campaign->date_send = Date::$now;
		$m_campaign->is_draft = 0;
		$m_campaign->all_contacts = 0;
		$m_campaign->save();

		$m_campaign_data = Model_Campaign_Data::find($m_campaign->id);
		if (!$m_campaign_data) $m_campaign_data = new Model_Campaign_Data();

		$m_campaign_data->campaign_id = $m_campaign->id;
		$m_campaign_data->content = $ci->load->view_return(
			'manage/contact/partials/campaign_default_content', 
			array('m_content' => $m_content));
		$m_campaign_data->save();

		$contact_lists = array();

		if (count($outreach_beats))
		{
			$compiler = new Media_Database_Compiler();
			$compiler->set_beats($outreach_beats);
			$compiler->set_roles(array_merge(
				Media_Database_Compiler::$editor_roles,
				Media_Database_Compiler::$reporter_roles
			));

			$compiler->enable_unique_emails_only();

			if (is_callable($this->compiler_callback_beats))
			{
				$callback = $this->compiler_callback_beats;
				$callback($compiler);
			}

			$m_list = $compiler->compile($this->contact_limit_beats);
			$contact_lists[] = $m_list;
			$m_list->name = sprintf('Auto+Beats: %s', 
				$ci->vd->cut($m_campaign->name, 30));
			$m_list->last_campaign_id = $m_campaign->id;
			$m_list->company_id = $m_company->id;
			$m_list->save();

			// remove contacts that have blocked this company
			$sql = "DELETE FROM nr_contact_list_x_contact WHERE contact_id IN 
				(SELECT contact_id FROM nr_contact_company_unsubscribed
					WHERE company_id = ?) AND contact_list_id = ?";
			$ci->db->query($sql, array($m_company->id, $m_list->id));
		}

		if (count($outreach_regions))
		{
			$compiler = new Media_Database_Compiler();
			$compiler->set_beats(Media_Database_Compiler::$local_news_beats);
			$compiler->set_regions($outreach_regions);
			$compiler->set_roles(array_merge(
				Media_Database_Compiler::$editor_roles,
				Media_Database_Compiler::$reporter_roles
			));

			if (is_callable($this->compiler_callback_regions))
			{
				$callback = $this->compiler_callback_regions;
				$callback($compiler);
			}

			$m_list = $compiler->compile($this->contact_limit_regions);
			$contact_lists[] = $m_list;
			$m_list->name = sprintf('Auto+Regions: %s', 
				$ci->vd->cut($m_campaign->name, 30));
			$m_list->last_campaign_id = $m_campaign->id;
			$m_list->company_id = $m_company->id;
			$m_list->save();

			// remove contacts that have blocked this company
			$sql = "DELETE FROM nr_contact_list_x_contact WHERE contact_id IN 
				(SELECT contact_id FROM nr_contact_company_unsubscribed
					WHERE company_id = ?) AND contact_list_id = ?";
			$ci->db->query($sql, array($m_company->id, $m_list->id));
		}

		$m_campaign->set_lists($contact_lists);

		$m_cbcrd = $m_cbc->raw_data();
		if (!$m_cbcrd) $m_cbcrd = new Raw_Data();
		$m_cbcrd->outreach_beats = $outreach_beats;
		$m_cbcrd->outreach_regions = $outreach_regions;
		$m_cbc->content_id = $m_content->id;
		$m_cbc->campaign_id = $m_campaign->id;
		$m_cbc->raw_data($m_cbcrd);
		$m_cbc->save();

		$this->m_cbc = $m_cbc;
		$this->m_campaign = $m_campaign;
		$this->m_campaign_data = $m_campaign_data;

		return $m_cbc;
	}	

	
	
}
