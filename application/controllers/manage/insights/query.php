<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/process_results_image_variant_trait');
load_controller('shared/process_results_company_profile_trait');
load_controller('shared/process_results_company_name_trait');

class Query_Controller extends Manage_Base {

	const MAX_OFFSET_LIMIT = 1000;
	const CACHE_DURATION = 1800;

	use Process_Results_Image_Variant_Trait;
	use Process_Results_Company_Name_Trait;
	use Process_Results_Company_Profile_Trait;
	
	public function index()
	{
		$params = (array) $this->input->post('params');
		$params = Raw_Data::from_array($params);
		$this->transform_params($params);
		$offset = (int) $this->input->post('offset');
		$limit = max(50, (int) $this->input->post('limit'));

		// if we reach the offset limit, then return no results
		if ($offset + $limit >= static::MAX_OFFSET_LIMIT)
			$limit = 0;

		if (($_response = $this->read_cache($params, $offset, $limit)))
		{
			$_response->cached = true;
			$_response->id = $this->input->post('id');
			return $this->json($_response);
		}

		$iCt = new Insights_Search_Content($params);
		$iCtFetch = $iCt->fetch($offset, $limit);
		$iCtFetch->results = $this->process_results_image_variant($iCtFetch->results, 'cover_image_id', 'cover_image', 'finger');
		$iCtFetch->results = $this->process_results_company_name($iCtFetch->results);

		$_response = new Raw_Data();
		$_response->id = $this->input->post('id');
		$_response->results = array();

		foreach ($iCtFetch->results as $result)
		{
			$_result = new Raw_Data();
			if ($result->cover_image)
				$_result->image = Stored_Image::url_from_filename($result->cover_image->filename);
			$_result->company = $result->company_name;
			$_result->company_id = $result->company_id;
			$_result->id = $result->id;
			$_result->title = $result->title;
			$_result->summary = trim($this->vd->cut($result->summary, 150));
			$_result->url = $this->website_url($result->url());
			$_result->when = Date::difference_in_words(Date::utc($result->date_publish));
			$_response->results[] = $_result;
		}

		// if ($offset == 0 && $iCtFetch->terms)
		// {
		// 	$iCm = new Insights_Search_Company($params);
		// 	$iCmFetch = $iCm->fetch(0, 10);

		// 	$iCmFetch->results = $this->process_results_company_profile($iCmFetch->results);
		// 	foreach ($iCmFetch->results as $result)
		// 		$result->logo_image_id = $result->company_profile
		// 			? $result->company_profile->logo_image_id
		// 			: null;

		// 	$iCmFetch->results = $this->process_results_image_variant($iCmFetch->results, 'logo_image_id', 'logo_image', 'finger');			
		// 	$_response->companies = array();

		// 	foreach ($iCmFetch->results as $result)
		// 	{
		// 		$_result = new Raw_Data();
		// 		$_result->id = $result->id;
		// 		if ($result->logo_image)
		// 			$_result->image = Stored_Image::url_from_filename($result->logo_image->filename);
		// 		$_result->name = $result->name;
		// 		$_result->website = $result->company_profile ? $result->company_profile->website : null;
		// 		$_result->summary = $result->company_profile ? $this->vd->cut($result->company_profile->summary, 200) : null;
		// 		$_response->companies[] = $_result;
		// 	}
		// }

		$this->write_cache($params, $offset, $limit, $_response);
		$this->json($_response);
	}

	protected function transform_params(&$params)
	{
		if (!$params->types)
		{
			$params->types = array(
				Model_Content::TYPE_PR,
			);
		}

		$dt_from = Date::utc($params->date_from);
		$dt_to = Date::utc($params->date_to);
		$dt_from->setTime(0, 0, 0);
		$dt_to->setTime(23, 59, 59);
		$params->date_from = (string) $dt_from;
		$params->date_to = (string) $dt_to;
	}

	protected function write_cache($params, $offset, $limit, $_response)
	{
		$hash = $this->cache_hash($params, $offset, $limit);
		$encoded = Raw_Data::encode($_response, true);
		Data_Cache_ST::write($hash, $encoded,
			static::CACHE_DURATION);
	}

	protected function read_cache($params, $offset, $limit)
	{
		if ($this->is_development()) return false;
		$hash = $this->cache_hash($params, $offset, $limit);
		$encoded = Data_Cache_ST::read($hash);
		if (!$encoded) return false;
		return Raw_Data::decode($encoded, true);
	}

	protected function cache_hash($params, $offset, $limit)
	{
		$hash = new Data_Hash();
		$hash->params = $params;
		$hash->offset = $offset;
		$hash->limit = $limit;
		return $hash->hash_hex();
	}

}