<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PRNewswire_Release extends Raw_Data {

	const REPORT_IELLA_EVENT = 'prn_report_email';

	const DIST_WEB_ONLY = 'PWD';
	const DIST_BASIC_IMAGE = 'AAR';
	const DIST_NATIONAL = 'US1';

	public static function from_content(Model_Content $m_content)
	{
		$m_content->load_content_data();
		$m_content->load_local_data();
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
		$m_company_profile = Model_Company_Profile::find($m_newsroom->company_id);
		$m_company_contact = Model_Company_Contact::find($m_newsroom->company_contact_id);
		$m_release_plus = Model_Content_Release_Plus::find_content_with_provider($m_content->id, 
			Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE);
		$m_release_data = $m_release_plus->raw_data_object();
		$m_cover_image = Model_Image::find($m_content->cover_image_id);
		$m_user = $m_newsroom->owner();

		$instance = new static();
		$instance->DistributionTimeDate = Date::utc($m_content->date_publish);
		$instance->IsReleaseImmediate = $instance->DistributionTimeDate < Date::hours(1);
		$instance->Headline = $m_content->title;
		$instance->Subheadline = $m_content->summary;
		$instance->DatelineCity = $m_content->location;
		$instance->Body = static::generate_body($m_content);
		$instance->Source = $m_release_data->source;
		$instance->Images = array();
		$instance->Youtube = array();
		$instance->Distributions = array();

		if ($m_release_data->state)
			$instance->Distributions[] = $m_release_data->state;

		if ($m_release_data->national)
			$instance->Distributions[] = static::DIST_NATIONAL;

		if (!count($instance->Distributions))
			$instance->Distributions[] = static::DIST_WEB_ONLY;

		if (!$instance->Source)
			$instance->Source = $m_content->source;
		if (!$instance->Source)
			$instance->Source = $m_newsroom->company_name;

		$callback = Model_Email_Callback::create(static::REPORT_IELLA_EVENT);
		$instance->ReportEmailaddress = $callback->address();
		$callback->raw_data(array('content_id' => $m_content->id));
		$callback->save();
	
		if ($m_cover_image && in_array(static::DIST_WEB_ONLY, $instance->Distributions))
		{
			$Image = new Raw_Data();
			$meta = json_decode($m_cover_image->meta_data);
			$filename = $m_cover_image->variant('original')->filename;
			$stored_file = Stored_File::from_stored_filename($filename);
			$Image->File = $stored_file->actual_filename();
			$Image->Caption = !empty($meta->caption) 
				? $meta->caption : $m_newsroom->company_name;
			$instance->OnlineHostedPhoto = $Image;
		}

		$extras = Model_Content_Distribution_Extras::find($m_content->id);

		if ($extras)
		{
			$microlists = $extras->filter($extras::TYPE_MICROLIST);
			foreach ($microlists as $microlist)
				$instance->Distributions[] = $microlist->data->item_code;

			if (($eximages = $extras->filter($extras::TYPE_PRN_IMAGES)))
			{
				$eximages = array_values($eximages)[0];

				foreach ($eximages->data->confirmed as $exImageID)
				{
					// sanity check: should also be selected
					if (!in_array($exImageID, $eximages->data->selected))
						continue;

					$mImage = Model_Image::find($exImageID);
					$Image = new Raw_Data();
					$instance->Images[] = $Image;
					$meta = json_decode($mImage->meta_data);
					$filename = $mImage->variant('original')->filename;
					$stored_file = Stored_File::from_stored_filename($filename);
					$Image->File = $stored_file->actual_filename();
					$Image->Caption = !empty($meta->caption) 
						? $meta->caption : $m_newsroom->company_name;
					if (!in_array(static::DIST_BASIC_IMAGE, $instance->Distributions))
						$instance->Distributions[] = static::DIST_BASIC_IMAGE;
				}
			}

			if (($exvideo = $extras->filter($extras::TYPE_PRN_VIDEO)))
			{
				$exvideo = array_values($exvideo)[0];
				if ($exvideo->data->is_selected && $exvideo->data->is_confirmed && 
					 $m_content->web_video_provider === Video::PROVIDER_YOUTUBE)
				{
					$video = Video::get_instance($m_content->web_video_provider, 
						$m_content->web_video_id);
					$videoData = $video->data();

					$Youtube = new Raw_Data();
					$instance->Youtube[] = $Youtube;
					$Youtube->URL = $video->url();
					$Youtube->Caption = $videoData->title;
				}
			}
		}

		$instructions = array();
		$instructions[] = sprintf('Source: %s', $instance->Source);
		$instance->SpecialInstructions = implode(PHP_EOL, $instructions);

		return $instance;
	}

	protected static function generate_body(Model_Content $m_content)
	{
		$ci =& get_instance();
		$ci->vd->content = $m_content;
		$view = 'shared/distribution/prnewswire/body';
		return $ci->load->view_return($view);
	}
	
}