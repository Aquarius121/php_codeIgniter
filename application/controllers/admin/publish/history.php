<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class History_Controller extends Admin_Base {

	protected $html_fields = array('content');

	protected function get_friendly_name($index)
	{
		switch ($index) 
		{
			case '__detached_beats':
				return "Industries";
				break;
			
			case '__detached_images':
				return "Related Images";
				break;

			case '__detached_tags':
				return "Tags";
				break;

			case 'type':
				return "Distribution Type";
				break;

			case 'supporting_quote_name':
				return "Supporting Quote (Name)";
				break;

			case 'supporting_quote_title':
				return "Supporting Quote (Title)";
				break;

			case 'stored_file_id_1':
				return "file1";
				break;

			case 'stored_file_id_2':
				return "file2";
				break;
			default:
				return str_replace("_", " ", $index);
				break;
		}
	}

	protected function generate_change_array($date, $user_name, $user_title, $key, $field, $type, $content)
	{
		$change = array();

		$change['date'] = $date;
		$change['key'] = $key;
		$change['field'] = $field;
		$change['type'] = $type;
		$change['user_name'] = $user_name;
		$change['user_title'] = $user_title;
		$change['content'] = $content;

		return $change;
	}

	protected function generate_content_array($text, $tag, $src = "", $ext1 = "", $ext2 = "")
	{
		$content = array();

		$content['text'] = $text;
		$content['tag'] = $tag;
		$content['src'] = $src;
		$content['ext1'] = $ext1;
		$content['ext2'] = $ext2;

		return $content;
	}

	protected function get_prn_images($mcd_extra)
	{		
		$prn_object = $mcd_extra->filter($mcd_extra::TYPE_PRN_IMAGES);
		foreach ($prn_object as $key => $value) {
			return $value->data->selected;
			break;
		}
		return array();		
	}

	protected function is_prn_video($mcd_extra)
	{		
		$prn_object = $mcd_extra->filter($mcd_extra::TYPE_PRN_VIDEO);
		foreach ($prn_object as $key => $value) {
			return ($value->data->is_selected)? "ON":"OFF";
			break;
		}		
		return "OFF";
	}

	protected function get_image_changes($old_images, $images, $old_prn_images, $prn_images, $date, $user_name, $user_title)
	{
		//var_dump($old_images);
		$arrChanges = array();
		
		$old_ids = array();
		$new_ids = array();

		$remain_ids = array();
		$remain_images = array();

		foreach ($old_images as $oimage)		
			array_push($old_ids, $oimage->id);

		foreach ($images as $image)
			array_push($new_ids, $image->id);	
		
		$remain_ids = array_intersect($old_ids, $new_ids);

		$content = array();
		
		foreach ($old_images as $oimage)
		{
			if(!in_array($oimage->id, $remain_ids))
			{
				$url = Stored_File::url_from_filename($oimage->variant('finger')->filename);
				array_push($content, $this->generate_content_array("<del>Deleted</del>", "del", $url));	
			}			
			else
				$remain_images[$oimage->id] = $oimage;
		}
	
		foreach($images as $image)
		{
			if(!in_array($image->id, $remain_ids))
			{
				$url = Stored_File::url_from_filename($image->variant('finger')->filename);
				$prnstr = "<ins>PRN:OFF</ins>";
				if(in_array($image->id, $prn_images))
					$prnstr = "<ins>PRN:ON</ins>";
				array_push($content, $this->generate_content_array("<ins>Added</ins>" . $prnstr, "ins", $url));
			}
		}			

		foreach ($remain_ids as $rid)
		{
			if(in_array($rid, $old_prn_images) && !in_array($rid, $prn_images))
			{
				$url = Stored_File::url_from_filename($remain_images[$rid]->variant('finger')->filename);
				array_push($content, $this->generate_content_array("<ins>PRN:OFF</ins>", "ins", $url));		
			}
			if(!in_array($rid, $old_prn_images) && in_array($rid, $prn_images))
			{
				$url = Stored_File::url_from_filename($remain_images[$rid]->variant('finger')->filename);
				array_push($content, $this->generate_content_array("<ins>PRN:ON</ins>", "ins", $url));	
			}
		}
		if(count($content) > 0)
			$arrChanges['images'] = $this->generate_change_array($date, $user_name, $user_title, null, "Images", "Images", $content);		
		return $arrChanges;
	}

	

	protected function get_tag_changes($old_tags, $tags, $date, $user_name, $user_title)
	{
		$arrChanges = array();

		$remains = array_intersect($old_tags, $tags);

		$content = array();
		foreach ($old_tags as $tag) 
		{
			if(!in_array($tag, $remains))
				array_push($content, $this->generate_content_array($tag, "del"));
		}

		$ins_tags = array();
		foreach ($tags as $tag) 
		{
			if(!in_array($tag, $remains))
				array_push($content, $this->generate_content_array($tag, "ins"));
		}

		if(count($content) > 0)
			array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, null, "Tags", "content-array", $content));

		return $arrChanges;
	}

	protected function get_beats_changes($old_beat_ids, $beat_ids, $old_beats, $beats, $date, $user_name, $user_title)
	{
		$arrChanges = array();
		$remain_ids = array_intersect($old_beat_ids, $beat_ids);

		$content = array();
		foreach ($old_beats as $beat) {
			if(!in_array($beat->id, $remain_ids))
				array_push($content, $this->generate_content_array($beat->name, "del"));
		}

		foreach ($beats as $beat) {
			if(!in_array($beat->id, $remain_ids))
				array_push($content, $this->generate_content_array($beat->name, "ins"));
		}

		if(count($content) > 0)
			array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, null, "Industries", "content-array", $content));

		return $arrChanges;
	}

	protected function get_distribution_options($mcd_extras)
	{
		$options = array();
		$dst_arrays = $mcd_extras->filter($mcd_extras::TYPE_MICROLIST);

		foreach ($dst_arrays as $dst) 
			array_push($options, $dst->data->group_name . " >> " . $dst->data->name);

		return $options;
	}

	protected function get_distribution_changes($old_dst_ops, $dst_ops, $date, $user_name, $user_title)
	{
		$remains = array_intersect($old_dst_ops, $dst_ops);
		$arrChanges = array();
		$content = array();

		foreach ($old_dst_ops as $dst) {
			if(!in_array($dst, $remains))
				array_push($content, $this->generate_content_array($dst, "del"));
		}

		foreach ($dst_ops as $dst) {
			if(!in_array($dst, $remains))
				array_push($content, $this->generate_content_array($dst, "ins"));
		}

		if(count($content) > 0)
			array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, null, "MICROLIST", "content-array", $content));

		return $arrChanges;

	}

	protected function get_video_change($old_video, $video, $date, $user_name, $user_title)
	{
		$arrChanges = array();
		$content = array();
		
		foreach($video as $key => $value)
		{
			if($value != null && $value != "" && $value != $old_video[$key])
			{
				if($key != "ID")
					array_push($content, $this->generate_content_array($key, $old_video[$key], $value));
				else
				{
					if($old_video['PROVIDER'] != "" && $old_video['ID'] != "")
						$old_url = Video::get_instance($old_video['PROVIDER'], $old_video['ID'])->url();
					else
						$old_url = "";
					$url = Video::get_instance($video['PROVIDER'], $video['ID'])->url();
					array_push($content, $this->generate_content_array("ID",$old_video['ID'],$value, $old_url, $url));
				}
			}
		}
		
		if(count($content) > 0)
			array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, null, "Video", "video-array", $content));

		return $arrChanges;		
	}

	public function get_history()
	{

		$content_id = $this->input->post('cid');
		$contentChanges = Model_Content_Change::find_all(array("content_id",$content_id),array('id', 'asc'));

		$arrChanges = array();
		$old_content = new stdClass();
		$arrCount = array();
		
		$old_bundle = "";
		$old_images = array();
		$old_prn_images = array();

		$old_tags = array();
		$old_beats = array();
		$old_beat_ids = array();

		$old_dst_ops = array();
		$old_video = array("PROVIDER" => "", "ID" => "", "PRN" => "");

		foreach ($contentChanges as $contentChange)
		{
			
			$raw_data_object = $contentChange->raw_data_object();
			$raw_content = $raw_data_object->content;			
			$mcd_extras = Model_Content_Distribution_Extras::from_object($raw_data_object->other->extras);
			$detached_object = Model_Detached_Content::from_object($raw_content);

			$date = Date::out($raw_content->date_updated);
			$user_name = $raw_data_object->user_name;
			if($user_name)
				$user_title = "Customer";
			else
				$user_title = "";

			if ($raw_data_object->is_admin_mode)
				$user_title = "Staff";

			$count = 0;
			$video = array();

			$video['PROVIDER'] = $raw_content->web_video_provider;
			$video['ID'] = $raw_content->web_video_id;
			$video['PRN'] = $this->is_prn_video($mcd_extras);			
			
			$video_changes = $this->get_video_change($old_video, $video, $date, $user_name, $user_title);

			foreach($video_changes as $video_change)
				array_push($arrChanges, $video_change);

			$count += count($video_changes);	

			$old_video = $video;

			if ($old_bundle !== $raw_data_object->other->bundle->bundle)
			{
				$content = array();
				if($old_bundle != "")
				array_push($content, $this->generate_content_array($old_bundle, "del"));
				array_push($content, $this->generate_content_array($raw_data_object->other->bundle->bundle, "ins"));				
				array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, null, "Distribution", "content-array", $content));
				$count++;
			}

			//Distribution Changes

			$dst_ops = $this->get_distribution_options($mcd_extras);	

			$dst_changes = $this->get_distribution_changes($old_dst_ops, $dst_ops, $date, $user_name, $user_title);				

			foreach($dst_changes as $dst_change)
				array_push($arrChanges, $dst_change);

			$count += count($dst_changes);	

			// Image Changes
			$prn_images = $this->get_prn_images($mcd_extras);			
			$images = $detached_object->get_images();			
			$image_changes = $this->get_image_changes($old_images, $images, $old_prn_images, $prn_images, $date, $user_name, $user_title);				

			foreach($image_changes as $img_change)
				array_push($arrChanges, $img_change);

			$count += count($image_changes);

			// Tag Changes

			$tags = $detached_object->get_tags();

			$tag_changes = $this->get_tag_changes($old_tags, $tags, $date, $user_name, $user_title);

			foreach($tag_changes as $tag_change)
				array_push($arrChanges, $tag_change);

			$count += count($tag_changes);

			// Beats Changes

			$beats = $detached_object->get_beats();
			$beat_ids = $raw_content->__detached_beats;	
			$beats_changes = $this->get_beats_changes($old_beat_ids, $beat_ids, $old_beats, $beats, $date, $user_name, $user_title);

			foreach($beats_changes as $beats_change)
				array_push($arrChanges, $beats_change);

			$count += count($beats_changes);

			foreach ($raw_content as $key => $data)
			{
				if ($key == "date_updated"  || strpos($key, "is_") !== false|| strpos($key, "__") !== false || strpos($key, "id") !== false || strpos($key, "stored_file_name_") !== false  || strpos($key, "post_") !== false || strpos($key, "outreach_") !== false || strpos($key, "stored") !== false || strpos($key, "type") !== false)
					continue;
				
				if (property_exists($old_content, $key) && trim($old_content->$key) != trim($data))
				{
					if (strpos($key, "is_") !== false)
					{				
						$output = ($data == 1 || $data == true) ? "True":"False";							
					}
					else							
					{
						$old_text = $old_content->$key;
						$new_text = $data;
						$old_text = $this->vd->esc($old_text);
						$new_text = $this->vd->esc($new_text);

						$output = $this->renderTextDifference($old_text, $new_text);
					}

					$str = ucwords($this->get_friendly_name($key));

					array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, $key, $str,"",$output));
					$count++;
				}

				if ((!property_exists($old_content, $key)) && $data)
				{
					if (strpos($key, "is_") !== false)							
					{
						$output = ($data == 1 || $data == true) ? "True":"False";							
					}
					else							
					{
						$new_text = $data;						
						$new_text = $this->vd->esc($new_text); 
						$output = $this->renderTextDifference('', $new_text);								
					}				

					$str = ucwords($this->get_friendly_name($key));

					array_push($arrChanges, $this->generate_change_array($date, $user_name, $user_title, $key, $str,"",$output));
					$count++;
				}

			}

			if ($count != 0)
				array_push($arrCount, $count);

			$old_images = $images;
			$old_content = $raw_content;
			$old_prn_images = $prn_images;
			$old_bundle = $raw_data_object->other->bundle->bundle;
			$old_tags = $tags;
			$old_beats = $beats;
			$old_beat_ids = $beat_ids;
			$old_dst_ops = $dst_ops;
		}
		$this->vd->results = $arrChanges;
		$this->vd->counts = $arrCount;
		$this->vd->html_fields = $this->html_fields;
		$this->load->view('admin/publish/partials/history_modal_content');

	}

	protected function renderTextDifference($old, $new)
	{
		$tdr = new Text_Difference_Renderer();
		return $tdr->renderHTML($old, $new, 150);
	}

}