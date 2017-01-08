<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Rename_Slug_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($old, $new)
	{
		$old_c = Model_Content::find_slug($old);
		$new_c = Model_Content::find_slug($new);

		if (!$old_c)
		{
			$this->trace_failure('not found');
			return;
		}

		if ($new_c)
		{
			$this->trace_failure('slug exists');
			return;
		}

		$old_c->slug = $new;
		$old_c->save();

		$redirect = new Model_Content_Slug_Redirect();
		$redirect->old_slug = $old;
		$redirect->new_slug = $new;
		$redirect->save();

		$this->trace_success($this->website_url($old_c->url()));
	}


}

?>