<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Switch_Version_Controller extends CIL_Controller {

	public function index()
	{
		$default = 'use_v25';
		$this->vd->version = $default;

		$versions = array(
			'use_v25' => 'Version 25 (Live)',
			'use_v26' => 'Version 26 (Test)',
		);

		if ($this->input->get('switch'))
		{
			foreach ($versions as $version => $label)
			{
				$cookie = new Cookie($version);
				$cookie->delete();
			}

			$cookie = new Cookie($this->input->get('switch'));
			$cookie->set(1);
			$this->redirect('manage');
		}

		foreach ($versions as $version => $label)
		{
			$cookie = new Cookie($version);
			if ($cookie->get())
				$this->vd->version = $version;
		}

		$this->vd->versions = $versions;
		$this->load->view('common/switch_version');
	}

}
