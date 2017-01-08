<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Tutorial_Controller extends Manage_Base {

	protected static $videos = array(
		array('title' => '1. How to Find Contacts', 'id' => '0obAL7p06V4', 'length' => '2:06'),
		array('title' => '2. How to Search Using Filters and Applying Filters', 'id' => 'PwOMkS9-n0Q', 'length' => '4:09'),
		array('title' => '3. How to Use Filters and Best Practices', 'id' => 'x09SgQYucyA', 'length' => '5:49'),
		array('title' => '4. Using Negation (-) Function to Remove Irrelevant Contacts', 'id' => 'VGJpsuvB1bE', 'length' => '2:27'),
		array('title' => '5. How to Create, Add-to and Save a List', 'id' => '4V38xbHJoBI', 'length' => '3:15'),
		array('title' => '6. How to Manage, Edit and Revise List', 'id' => 'PXhUVSNr9rQ', 'length' => '4:47'),
		array('title' => '7. How to Import Your Own List to the Dashboard', 'id' => '-ye7Ym0VL2w', 'length' => '5:33'),
		array('title' => '8. How to Set Up a Email Campaign Pitch', 'id' => '0TCwS7wtjR4', 'length' => '8:33'),
		array('title' => '9. Email Templates and What to Use', 'id' => 'w8QMIRhjriE', 'length' => '2:44'),
		array('title' => '10. Tutorial - How to Submit a Press Release on Newswire', 'id' => 'aW2ZNPl4kyM', 'length' => '6:37'),
	);

	public function index()
	{
		$this->title = 'Tutorial Videos';
		$this->vd->videos = static::$videos;

		$video_modal = new Modal();
		$this->add_eob($video_modal->render(804,452));
		$this->vd->modal_id = $video_modal->id;

		$this->load->view('manage/header');
		$this->load->view('manage/tutorial/index');
		$this->load->view('manage/footer');
	}	
	
}
