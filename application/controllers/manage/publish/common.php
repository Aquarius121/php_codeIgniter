<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/publish/content');

class Common_Controller extends Content_Base { 
	
	public function resolve_video()
	{
		$provider = $this->input->post('web_video_provider');
		$video_id = $this->input->post('web_video_id');
		
		return parent::resolve_video($provider, $video_id, false);
	}
	
	public function upload_file()
	{
		$response = array();			
		$file = Stored_File::from_uploaded_file('file');
		if (!$file->exists()) return $this->json(null);
		
		if (!$file->has_supported_extension())
		{
			$file->delete();
			$response['status'] = false;
			$response['error'] = 'File Extension Forbidden';
		}
		else if ($file->size() > $this->conf('max_web_file_size'))
		{
			$response['status'] = false;
			$response['error'] = 'Size Limit Exceeded';
		}
		else
		{
			$file->move();
			$response['status'] = true;
			$response['file_url'] = $file->url();
			$response['stored_file_id'] = $file->save_to_db();
		}
		
		$this->json($response);
	}
	
	public function reset()
	{
		if (!Auth::is_admin_online()) show_404();
		if (!$this->input->post('confirmed'))
			return $this->json(false);
		$id = $this->input->post('id');
		$m_content = Model_Content::find($id);
		if (!$m_content) $this->json(false);
		$m_content->status_reset();
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The content has been reset.');
		$this->add_feedback($feedback);
		
		$this->json(true);
	}

	public function pure()
	{
		$value = $this->input->post('value');
		$this->json(array(
			'value' => $this->vd->pure($value),
		));
	}
	
}
