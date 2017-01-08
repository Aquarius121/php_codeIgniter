<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Owler_Category_Controller extends Iella_Base {
	
	public function save()
	{
		$owler_cats = $this->iella_in->owler_cats;

		$owler_cat_ids = array();

		foreach ($owler_cats as $owler_cat)
		{
			if (!$owler_c = Model_Owler_Category::find($owler_cat->id))
				$owler_c = new Model_Owler_Category();

			$owler_c->id = $owler_cat->id;
			$owler_c->name = $owler_cat->name;
			$owler_c->save();
					
			$owler_cat_ids[] = $owler_c->id;			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->owler_cat_ids = $owler_cat_ids;
		$this->send();						
	}
}

?>

