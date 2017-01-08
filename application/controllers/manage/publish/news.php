<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/publish/content');

class News_Controller extends Content_Base { 

	protected $content_type = Model_Content::TYPE_NEWS;

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Distribution';
		$this->vd->title[] = 'News';
	}

	public function index()
	{
		$this->redirect('manage/publish/news/all');
	}
	
	public function edit($content_id = null)
	{
		$vars = parent::edit($content_id);
		extract($vars, EXTR_SKIP);

		// external content is handled by a different editor
		if ($m_content && $m_content->is_external)
		{
			// Removed the redirect from here
			// because for delete operation it 
			// was redirecting to edit/external
			
			$this->edit_external($m_content->id);
			return;
		}

		$this->load->view('manage/header');
		$this->load->view('manage/publish/news-edit');
		$this->load->view('manage/footer');
	}

	public function edit_save()
	{
		$vars = parent::edit_save('news');
		extract($vars, EXTR_SKIP);

		if ($is_preview)
		{
			$m_content = Detached_Session::read('m_content');
			Detached_Session::write('m_content', $m_content);
			return;
		}
		else
		{
			if ($is_new_content)
			     $m_pb_news = new Model_PB_News();
			else $m_pb_news = Model_PB_News::find($m_content->id);
			if (!$m_pb_news) $m_pb_news = new Model_PB_News();
			$m_pb_news->content_id = $m_content->id;
			$m_pb_news->save();
		}
	}

	public function edit_external($content_id = null)
	{		
		$vars = parent::edit($content_id);
		extract($vars, EXTR_SKIP);

		$this->vd->content_type = Model_Content::full_type(Model_Content::TYPE_NEWS);
		
		$this->load->view('manage/header');
		$this->load->view('manage/publish/news-edit-external');
		$this->load->view('manage/footer');
	}

	public function edit_save_external()
	{
		$vars = parent::edit_save('news');
		extract($vars, EXTR_SKIP);
		
		if ($is_new_content)
		     $m_pb_news = new Model_PB_News();
		else $m_pb_news = Model_PB_News::find($m_content->id);
		if (!$m_pb_news) $m_pb_news = new Model_PB_News();

		$m_content->is_backdated = 1;
		$m_content->is_draft = 0;
		$m_content->save();

		$m_pb_news->is_external = 1;
		$m_pb_news->content_id = $m_content->id;
		$m_pb_news->source_url = $this->input->post('source_url');
		$m_pb_news->save();
	}
	
}

?>