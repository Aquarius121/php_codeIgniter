<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Cat_To_Beat_Controller extends Admin_Base {

	public $title = 'Cat to Beat';

	public function index($chunk = 1)
	{
		$cats = Model_Cat::list_all_cats_by_group();
		$beats = Model_Beat::list_all_beats_by_group();
		$this->vd->beats = $beats;
		$this->vd->cats = $cats;

		$add_more_modal = new Modal();
		$add_more_modal->set_title('Add More');
		$modal_view = 'admin/settings/partials/cat_to_beat_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$add_more_modal->set_content($modal_content);
		$modal_view = 'admin/settings/partials/cat_to_beat_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$add_more_modal->set_footer($modal_content);
		$this->add_eob($add_more_modal->render(600, 500));
		$this->vd->add_more_modal_id = $add_more_modal->id;

		$sql = "SELECT 
			c.id AS cat__id,
			b.id AS beat__id,
			c.name AS cat__name,
			b.name AS beat__name,
			cg.name AS cat__group_name,
			bg.name AS beat__group_name
			FROM nr_cat_to_beat ctb
			INNER JOIN nr_cat c 
			ON ctb.cat_id = c.id
			INNER JOIN nr_beat b
			ON ctb.beat_id = b.id
			INNER JOIN nr_cat_group cg 
			ON cg.id = c.cat_group_id
			INNER JOIN nr_beat_group bg
			ON bg.id = b.beat_group_id";

		$dbr = $this->db->query($sql);
		$results = Model_Base::from_db_all($dbr, array(
			'cat' => 'Model_Cat',
			'beat' => 'Model_Cat',
		));

		$this->vd->results = $results;
		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/cat_to_beat');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function add()
	{
		$cats = $this->input->post('cat_id');
		$beats = $this->input->post('beat_id');
		$count = 0;

		foreach ($cats as $k => $cat_id)
		{
			$cat = Model_Cat::find($cats[$k]);
			$beat = Model_Beat::find($beats[$k]);
			if (!$cat) continue;
			if (!$beat) continue;

			$exists = Model_Cat_To_Beat::find_pair($cat, $beat);
			if ($exists) continue;
			
			$created = new Model_Cat_To_Beat();
			$created->cat_id = $cat->id;
			$created->beat_id = $beat->id;
			$created->save();
			$count++;
		}

		$fback = new Feedback('success');
		$fback->set_title('Success!');
		$fback->set_text(sprintf('Added %d new relationships.', $count));
		$this->add_feedback($fback);
		$this->redirect('admin/settings/cat_to_beat');
	}

	public function delete()
	{
		$cat = $this->input->post('cat_id');
		$beat = $this->input->post('beat_id');
		$exists = Model_Cat_To_Beat::find_pair($cat, $beat);
		if ($exists) $exists->delete();

		$fback = new Feedback('success');
		$fback->set_title('Success!');
		$fback->set_text('Deleted relationship.');
		$this->add_feedback($fback);
		$this->redirect('admin/settings/cat_to_beat');
	}
	
}

?>