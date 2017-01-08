<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Update_Content_Slug_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($old = null, $new = null)
	{
		if (!$old || !$new)
		{
			$this->trace_warn('usage: update_content_slug <old> <new>');
			return;
		}

		$new = Slugger::create($new, Model_Content::MAX_SLUG_LENGTH);

		$m_old = Model_Content::find_slug($old);
		$m_new = Model_Content::find_slug($new);

		if (!$m_old)
		{
			$this->trace_failure('cannot find content');
			return;
		}

		if ($m_new)
		{
			$this->trace_failure('slug unavailable');
			return;
		}

		$m_old->slug = $new;
		$m_old->save();

		$m_redirect = new Model_Content_Slug_Redirect();
		$m_redirect->old_slug = $old;
		$m_redirect->new_slug = $new;
		$m_redirect->save();

		$this->trace_success($this->website_url($m_old->url()));
		return;
	}

}