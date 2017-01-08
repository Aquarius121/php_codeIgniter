<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Migrate_Data_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		set_time_limit(86400);

		// Checking if any duplicate 
		// PRs are yet to be removed. 
		// We can't continue transfer 
		// before dup prs are removed
		$this->remove_prnewswire_dup_prs();

		// now proceeding with migrating data
		$sql = "SELECT c.*, cd.*, pb.*,
				sc.source_url AS url,
				si.filename AS logo_filename,
				{{ prnb.* AS prn_pr USING Model_PB_PRN_PR }},
				{{ pc.* AS prn_comp USING Model_PRN_Company }}
				FROM nr_pb_prn_pr prnb
				INNER JOIN nr_content c
				ON prnb.content_id = c.id
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_pr pb
				ON pb.content_id = prnb.content_id
				INNER JOIN nr_pb_scraped_content sc
				ON sc.content_id = pb.content_id

				LEFT JOIN ac_nr_prn_company pc
				ON prnb.prn_company_id = pc.id
				LEFT JOIN nr_newsroom_custom nrc
				ON nrc.company_id = pc.company_id
				LEFT JOIN nr_image_variant iv
				ON nrc.logo_image_id = iv.image_id
				LEFT JOIN nr_stored_image si
				ON iv.stored_image_id = si.id

				WHERE prnb.is_migrated_to_live_site = 0
				AND c.is_published = 1
				AND c.date_publish > '2016-07-26'
				AND prnb.is_pr_scraped = 1
				AND is_category_mapped = 1
				AND prnb.is_cover_image_pulled = 1
				AND (prnb.is_nr_assigned = 1 
					OR prnb.prn_company_id = 0)
				AND (iv.name = 'original' 
					OR iv.name IS NULL)
				ORDER BY c.date_publish DESC
				LIMIT 10";

		$cnt = 1;
		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_PRNEWSWIRE;

		$ci =& get_instance();
		while ($cnt++ <= 100)
		{
			$request = new Iella_Request();
			$request->base = $ci->conf('newswire_host_url');

			$results = Model_Content::from_sql_all($sql);

			foreach ($results as $content)
			{
				$beats = $content->get_beats();
				$content->beats = $this->get_beat_ids($beats);
				$raw_data = $content->prn_pr->raw_data();

				$cover_image = null;
				if ($content->cover_image_id)
				{
					$c_image = $this->get_cover_image($content->cover_image_id);
					$file_url = "files/". $c_image->filename;

					$content->cover_image_filename = $request->add_file($file_url);
				}

				if (isset($content->prn_comp->id) && $content->prn_comp->id)
				{
					if ($content->logo_filename)
					{
						$file_url = "files/". $content->logo_filename;
						$content->prn_comp->logo_filename = $request->add_file($file_url);
					}
				}
			}

			$request->data->prs = $results;
			$this->inspect("sending");
			$result = $request->send('dev_listener/prnewswire/prs/save');
			// var_dump($request->raw_response);
			if ($result->success)
			{
				$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
				$scrape->save();

				$content_ids = $result->content_ids;
				if (is_array($content_ids) && count($content_ids))
				{
					$ids_list = sql_in_list($content_ids);
					$sql_update = "UPDATE nr_pb_prn_pr
								SET is_migrated_to_live_site = 1,
								is_last_pr_date_migrated = 1
								WHERE content_id IN ({$ids_list})";

					$this->db->query($sql_update);

					$this->inspect("done! --> {$cnt}");
				}

				if (isset($result->nw_prn_pr_ids))
				{
					$nw_prn_pr_ids = $result->nw_prn_pr_ids;
					if (is_array($nw_prn_pr_ids) && count($nw_prn_pr_ids))
					{
						$nw_ids_list = sql_in_list($nw_prn_pr_ids);
						$sql_update = "UPDATE nr_pb_prn_pr
									SET is_nw_prn_pr = 1
									WHERE content_id IN ({$nw_ids_list})";

						$this->db->query($sql_update);
					}
				}

				sleep(2);

			}
		}
	}

	protected function get_cover_image($cover_image_id)
	{
		$sql = "SELECT iv.image_id, iv.name, si.filename
				FROM nr_image_variant iv 
				INNER JOIN nr_stored_image si 
				ON iv.stored_image_id = si.id 
				WHERE iv.image_id = '$cover_image_id'
				AND iv.name = 'original'";

		$result = Model::from_sql($sql, array($cover_image_id));
		return $result;
	}

	protected function get_beat_ids($beats)
	{
		$beat_ids = array();
		foreach ($beats as $beat)
			$beat_ids[] = $beat->id;

		return $beat_ids;
	}
}