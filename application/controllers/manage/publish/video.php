<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/publish/content');

class Video_Controller extends Content_Base { 

	protected $content_type = Model_Content::TYPE_VIDEO;

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Distribution';
		$this->vd->title[] = 'Videos';
	}

	public function index()
	{
		$this->redirect('manage/publish/video/all');
	}
	
	protected function process_results($results)
	{
		if (!count($results))
			return $results;
		
		$indexed = array();
		foreach ($results as $result)
			$indexed[$result->id] = $result;

		$content_id_str = sql_in_list(array_keys($indexed));
		$sql = "SELECT pb.content_id, iv.image_id, si.filename
			FROM nr_pb_video pb LEFT JOIN nr_image_variant iv 
			ON pb.image_id = iv.image_id
			LEFT JOIN nr_stored_image si 
			ON iv.stored_image_id = si.id 
			WHERE pb.content_id IN ({$content_id_str})
			AND iv.name = 'finger'";
			
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $result)
			$indexed[$result->content_id]->values($result);
		
		foreach ($results as $result)
		{
			$result->image_url = null;
			if (empty($result->filename)) continue;
			$result->image_url = Stored_Image::url_from_filename($result->filename);
		}
		
		return $results;
	}
	
	public function resolve_video()
	{
		$provider = $this->input->post('external_provider');
		$video_id = $this->input->post('external_video_id');
		
		return parent::resolve_video($provider, $video_id, true);
	}
	
	public function edit($content_id = null)
	{		
		$vars = parent::edit($content_id);
		extract($vars, EXTR_SKIP);
		
		$vd = array();
		$vd['image'] = null;
		$vd['licenses'] = array();
		$vd['licenses'][] = 'CCA';
		$vd['licenses'][] = 'CCA: Share-alike';
		$vd['licenses'][] = 'CCA: Noncommercial';
		$vd['licenses'][] = 'CCA: No Derivatives';
		$vd['licenses'][] = 'All Rights Reserved';
		$vd['providers'] = Video::providers();
		
		if ($m_content)
		{
			if ($image = Model_Image::find($m_content->image_id))
				$vd['image'] = $image;
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/publish/video-edit', $vd);
		$this->load->view('manage/footer');
	}
	
	public function edit_save()
	{
		$vars = parent::edit_save('video');
		extract($vars, EXTR_SKIP);
		
		$license = value_or_null($post['license']);
		$source = value_or_null($post['source']);
		$image_id = $post['image_id'];
		
		$external_provider  = value_or_null(@$post['external_provider']);
		$external_video_id  = value_or_null(@$post['external_video_id']);
		$external_author    = value_or_null(@$post['external_author']);
		$external_duration  = value_or_null(@$post['external_duration']);
		
		if ($is_preview)
		{
			$m_content = Detached_Session::read('m_content');
			$m_content->external_provider = $external_provider;
			$m_content->external_video_id = $external_video_id;
			$m_content->external_author = $external_author;
			$m_content->external_duration = $external_duration;
			$m_content->license = $license;
			$m_content->source = $source;
			$m_content->image_id = $image_id;
			Detached_Session::write('m_content', $m_content);
			return;
		}
		else
		{
			if ($is_new_content)
			     $m_pb_video = new Model_PB_Video();
			else $m_pb_video = Model_PB_Video::find($m_content->id);
			if (!$m_pb_video) $m_pb_video = new Model_PB_Video();			
			
			$m_pb_video->external_provider = $external_provider;
			$m_pb_video->external_video_id = $external_video_id;
			$m_pb_video->external_author = $external_author;
			$m_pb_video->external_duration = $external_duration;
			$m_pb_video->license = $license;
			$m_pb_video->source = $source;
			$m_pb_video->image_id = $image_id;
			$m_pb_video->content_id = $m_content->id;
			$m_pb_video->save();
		}
	}
	
}

?>