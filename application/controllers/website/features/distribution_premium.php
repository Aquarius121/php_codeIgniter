<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Distribution_Premium_Controller extends Website_Base {

	public function index()
	{
		$this->title = 'Distribution';

		$this->config->load('fin_content', true);
		$fn_sources = $this->config->item('sources', 'fin_content');
		$wn_sources = (require ('raw/distribution/world_now.php'));
		$sources = array_merge($fn_sources, $wn_sources);
		
		$sql = "SELECT s.hash, si.filename
				FROM nr_distribution_site s
				LEFT JOIN nr_image_variant iv 
				ON s.logo_image_id = iv.image_id
				LEFT JOIN nr_stored_image si 
				ON iv.stored_image_id = si.id 
				WHERE iv.name = 'dist-finger'";
		
		$query = $this->db->query($sql);
		$results = Model_Distribution_Site::from_db_all($query);
		$indexed = array();

		foreach ($results as $result)
			$indexed[$result->hash] = $result;
		
		foreach ($sources as $k => $source)
		{
			if (!isset($indexed[$source->hash]))
			{
				unset($sources[$k]);
				continue;
			}

			$sources[$k]->logo = null;
			if ($indexed[$source->hash]->filename)
			{
				$logo = Stored_File::url_from_filename($indexed[$source->hash]->filename);
				$sources[$k]->logo = $logo;
			}
		}

		$this->vd->sources = array_values($sources);
		$this->render_website('website/pages/features_distribution_premium');
	}


/* 	(object) array(
		'name' => '740 KVOR',
		'url_read' => 'http://markets.financialcontent.com/citcomm.kvoram/news/read?GUID={{rcid}}',
		'url_site' => 'http://www.kvor.com',
		'hash' => '0000000000000000da8004674af3b19a',
	), */
	
}