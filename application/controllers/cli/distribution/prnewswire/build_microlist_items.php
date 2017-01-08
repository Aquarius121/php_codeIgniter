<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Build_MicroList_Items_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index()
	{
		set_time_limit(3600);

		$microlists = (require 'raw/distribution/prnewswire/microlists.php');

		foreach ($microlists as $microlist)
		{
			$slug = $this->item_slug($microlist);
			$item = Model_Item::find_slug($slug);
			if (!$item) $item = new Model_Item();

			$item->slug = $slug;
			$item->price = $microlist->item_price;
			$item->name = sprintf('Microlist: %s', $microlist->name);
			$item->comment = 'PR Newswire Microlist';
			$item->activate_event = null;
			$item->descriptor = null;
			$item->help_text = null;
			$item->is_custom = 0;
			$item->is_disabled = 0;
			$item->is_listed = 1;
			$item->order_event = 'item_order_microlist';
			$item->secret = md5(microtime(true));
			$item->tracking = $this->item_tracking($microlist);
			$item->type = null;
			$item->raw_data($microlist);
			$item->save();

			$this->trace_success($slug, $item->id);
		}
	}

	protected function item_slug($microlist)
	{
		return PRNewswire_Distribution::microlist_slug($microlist->item_code);
	}

	protected function item_tracking($microlist)
	{
		return sprintf('asi_microlist_%s',
			$microlist->item_code);
	}

}