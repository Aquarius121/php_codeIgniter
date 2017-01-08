<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Resim_Controller extends CIL_Controller {
	
	public function index($variant, $id)
	{
		$v_sizes = $this->conf('v_sizes');
		if (!isset($v_sizes[$variant])) show_404();
		$image = Model_Image::find($id);
		$resim_variant = $image->variant($variant);
		
		if (!$resim_variant->filename)
		{
			$original_variant = $image->variant('original');
			$si_original = Stored_Image::from_stored_filename($original_variant->filename);
			$si_resim = $si_original->from_this_resized($v_sizes[$variant]);
			$image->add_variant($si_resim->save_to_db(), $variant);
			$resim_variant = $image->variant($variant);
			$this->redirect($si_resim->url());
		}
		else
		{
			$si_resim = Stored_Image::from_stored_filename($resim_variant->filename);
			$this->redirect($si_resim->url());
		}
	}

}

?>