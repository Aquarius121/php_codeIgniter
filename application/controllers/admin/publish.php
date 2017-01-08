<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/process_results_release_plus_trait');
load_controller('shared/process_results_distribution_bundle_trait');

class Publish_Controller extends Admin_Base {

	use Process_Results_Release_Plus_Trait;
	use Process_Results_Distribution_Bundle_Trait;

	const LISTING_CHUNK_SIZE = 20;
	public $review_mode = false;
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Distribution';
	}

	protected function process_results(&$results)
	{
		$dt_priority = Date::hours(-12);

		foreach ($results as $result)
		{
			$result->dt_publish = Date::out($result->date_publish);
			$result->dt_rejected = $result->date_rejected ? Date::out($result->date_rejected) : null;
			$result->dt_approved = $result->date_approved ? Date::out($result->date_approved) : null;
			$result->dt_hold = $result->date_hold ? Date::out($result->date_hold) : null;
			$result->is_priority = $result->is_under_review && $result->dt_publish < $dt_priority;

			if ($result->rejection_data)
			{
				$feedback = $result->rejection_data->raw_data();
				$result->has_feedback = !empty($feedback->comments) 
					|| !empty($feedback->canned);
				$result->feedback = $feedback;
				
				if ($result->has_feedback)
				{
					Model_Canned::enable_cache();
					foreach ($feedback->canned as $k => $canned_id)
						$feedback->canned[$k] = Model_Canned::find($canned_id);
				}
			}

			if ($result->hold_data)
			{
				$feedback = $result->hold_data->raw_data();
				$result->hold_comments = $feedback->comments;
			}
		}

		$results = $this->process_results_release_plus($results);
		$results = $this->process_results_distribution_bundle($results);
	}

	public function index($type = null, $status = null, $chunk = 1)
	{
		if ($type === null) $this->redirect(gstring('admin/publish/pr/under_review'));
		if ($status === null) $this->redirect(gstring("admin/publish/{$type}/under_review"));
		if (!$this->has_under_review($type) && $status === 'under_review')
			$this->redirect(gstring("admin/publish/{$type}/all"));
		
		if (!Model_Content::is_allowed_type($type)) show_404();
		if (!$this->is_allowed_status($status)) show_404();	
		
		$filters = array(
			'all' => null,
			'draft' => 'c.is_published = 0 AND c.is_draft = 1',
			'published' => 'c.is_published = 1',
			'scheduled' => 'c.is_published = 0 AND c.is_draft = 0 AND c.is_under_review = 0',
			'under_review' => 'c.is_under_review = 1',
		);
		
		if ($status === 'all')
			$this->vd->show_status = true;
		if ($status === 'under_review')
			$this->review_mode = true;

		$hold_content = new Modal();
		$hold_content->set_title('Hold Content');
		$modal_view = 'admin/partials/hold_content_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$hold_content->set_content($modal_content);
		$modal_view = 'admin/partials/hold_content_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$hold_content->set_footer($modal_content);
		$this->add_eob($hold_content->render(500, 300));
		$this->vd->hold_content_modal_id = $hold_content->id;

		$edit_dist = new Modal();
		$edit_dist->set_title('Edit Distribution Data');
		$modal_view = 'admin/publish/partials/edit_dist_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$edit_dist->set_content($modal_content);
		$modal_view = 'admin/publish/partials/edit_dist_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$edit_dist->set_footer($modal_content);
		$this->add_eob($edit_dist->render(400, 400));
		$this->vd->edit_dist_modal_id = $edit_dist->id;

		//History Modal

		$history_modal = new Modal();
		$history_modal->set_title("Show History");
		$modal_view = 'admin/publish/partials/history_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$history_modal->set_content($modal_content);
		$this->add_eob($history_modal->render(1000,500));
		$this->vd->history_modal_id = $history_modal->id;		
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/publish/{$type}/{$status}/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($type, $chunkination, $filters[$status]);
		$this->vd->title[] = Model_Content::full_type_plural($type);	
		$this->process_results($results);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			// out of bounds so redirect to first
			$url = "admin/publish/{$type}/{$status}";
			$this->redirect(gstring($url));
		}
			
		$this->vd->type = $type;
		$this->vd->status = $status;		
		$this->render_list($chunkination, $results);
	}
	
	public function approve($content_id, $view = false)
	{
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');
		
		// approve and notify
		$this->approve_sub($content);
		
		// load feedback message for the user
		$feedback_view = 'admin/publish/partials/approve_feedback';
		$feedback = $this->load->view($feedback_view, 
			array('content' => $content), true);
		$this->add_feedback($feedback);
		
		// redirect to view the content
		if ($view) $this->redirect($content->url());
		
		// redirect back to the last location
		$url = value_or_null($_SERVER['HTTP_REFERER']);
		// redirect back to list of content to be reviewed
		if (!$url) $url = "admin/publish/{$content->type}/under_review";
		$this->redirect($url, false);
	}

	public function hold($view = false)
	{
		$content_id = $this->input->post('content');
		$comments = $this->input->post('comments');
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');

		// store the hold data (keeps approval/rejection history)
		$hold = Model_Hold_Data::find_or_create($content->id);

		if ($this->input->post('remove'))
		{
			$hold->delete();

			// load feedback message for the user
			$feedback_view = 'admin/publish/partials/hold_removed_feedback';
			$feedback = $this->load->view($feedback_view, 
				array('content' => $content), true);
			$this->add_feedback($feedback);
		}
		else
		{
			$hold->raw_data(array('comments' => $comments));
			$hold->save();

			// load feedback message for the user
			$feedback_view = 'admin/publish/partials/hold_feedback';
			$feedback = $this->load->view($feedback_view, 
				array('content' => $content), true);
			$this->add_feedback($feedback);
		}
				
		// redirect to view the content
		if ($view) $this->redirect($content->url());
		
		// redirect back to the last location
		$url = value_or_null($_SERVER['HTTP_REFERER']);
		// redirect back to list of content to be reviewed
		if (!$url) $url = "admin/publish/{$content->type}/under_review";
		$this->redirect($url, false);
	}
	
	public function reject($content_id, $view = false)
	{
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');
		$content->load_content_data();
		$this->vd->canned = $canned = Model_Canned::find_all();
		$this->vd->content = $content;
		
		if ($this->input->post('confirm'))
		{
			// reject and notify
			$this->reject_sub($content);
					
			// load feedback message for the user
			$feedback_view = 'admin/publish/partials/reject_feedback';
			$feedback = $this->load->view($feedback_view, 
				array('content' => $content), true);
			$this->add_feedback($feedback);
			
			// redirect to view the content
			if ($view) $this->redirect($content->url());
			
			// redirect back to the last location
			$url = value_or_null($this->input->post('last-location'));
			if ($url) $this->redirect($url, false);
						
			// redirect back to list of content to be reviewed
			$url = "admin/publish/{$content->type}/under_review";
			$this->redirect($url);
		}
		
		$this->load->view('admin/header');
		$this->load->view('admin/publish/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/publish/reject');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	protected function approve_sub($content)
	{
		$content->approve();
		$user = $content->owner();
		
		if (!$content->is_legacy)
		{
			// store the approval data (removes rejection)
			$approval = Model_Approval_Data::find_or_create($content->id);
			$approval->raw_data($this->input->post());
			$approval->save();
			
			if (!$user->is_mail_blocked(Model_User_Mail_Blocks::PREF_CONTENT_APPROVED))
			{					
				// notify the user that the content is approved
				$sch_n = new Model_Scheduled_Notification();
				$sch_n->related_id = $content->id;
				$sch_n->class = Model_Scheduled_Notification::CLASS_CONTENT_APPROVED;
				$sch_n->user_id = $user->id;
				$sch_n->save();
			}
		}
	}
	
	protected function reject_sub($content)
	{
		$content->reject();
		$user = $content->owner();
		
		if (!$content->is_legacy)
		{
			// store the last rejection data (removes approval)
			$rejection = Model_Rejection_Data::find_or_create($content->id);
			$rejection->raw_data($this->input->post());
			$rejection->save();
						
			if (!$user->is_mail_blocked(Model_User_Mail_Blocks::PREF_CONTENT_REJECTED))
			{
				// notify the user that the content is rejected
				$sch_n = new Model_Scheduled_Notification();
				$sch_n->related_id = $content->id;
				$sch_n->class = Model_Scheduled_Notification::CLASS_CONTENT_REJECTED;
				$sch_n->user_id = $user->id;
				$sch_n->data = serialize($this->input->post());
				$sch_n->save();
			}
		}
	}
	
	public function approve_all()
	{
		$type = null;
		$content_ids = $this->input->post('selected');

		foreach ($content_ids as $content_id => $_1)
		{
			if (!$_1) continue;
			$content = Model_Content::find($content_id);
			if (!$content) continue;
			$this->approve_sub($content);
			$type = $content->type;
		}
		
		// load feedback message for the user
		$feedback_view = 'admin/publish/partials/bulk_feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		
		// redirect to review page for type (assuming all same)
		if (!$type) $this->redirect('admin/publish');
		$this->redirect("admin/publish/{$type}/under_review");
	}
	
	public function reject_all()
	{
		$type = null;
		$content_ids = $this->input->post('selected');

		foreach ($content_ids as $content_id => $_1)
		{
			if (!$_1) continue;
			$content = Model_Content::find($content_id);
			if (!$content) continue;
			$this->reject_sub($content);
			$type = $content->type;
		}
		
		// load feedback message for the user
		$feedback_view = 'admin/publish/partials/bulk_feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		
		// redirect to review page for type (assuming all same)
		if (!$type) $this->redirect('admin/publish');
		$this->redirect("admin/publish/{$type}/under_review");
	}
		
	public function edit($content_id)
	{
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');
		$user = $content->owner();

		if ($user->is_virtual())
		{
			$url = sprintf('common/vuras/%d/content/%s', 
				$user->id, $content->uuid);
			$this->redirect($url);
		}

		$url = "manage/publish/{$content->type}/edit/{$content_id}";
		$this->admin_mode_from_company($content->company_id, $url);
	}
	
	public function delete($content_id)
	{
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');
		$url = "manage/publish/{$content->type}/delete/{$content_id}";
		$this->admin_mode_from_company($content->company_id, $url);
	}
	
	public function stats($content_id)
	{
		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect('admin/publish');
		$url = "manage/analyze/content/view/{$content_id}";
		$this->admin_mode_from_company($content->company_id, $url);
	}

	public function transfer_to_company()
	{
		$company_id = $this->input->post('company');
		$content_id = $this->input->post('content');

		$content = Model_Content::find($content_id);
		$company = Model_Company::find($company_id);

		if (!$content) return;
		if (!$company) return;

		$content->company_id = $company->id;
		$content->save();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The content has been transferred.');
		$this->add_feedback($feedback);
		$this->json(array('reload' => true));
	}

	public function edit_distribution()
	{
		$content_id = (int) $this->input->post('id');
		$redirect = $this->input->post('redirect');

		$content = Model_Content::find($content_id);
		if (!$content) $this->redirect($redirect);

		if ($provider = $this->input->post('add'))
		{
			$m_crp = Model_Content_Release_Plus::find_content_with_provider($content_id, $provider);
			if (!$m_crp) $m_crp = new Model_Content_Release_Plus();
			$m_crp->content_id = $content_id;
			$m_crp->provider = $provider;
			$m_crp->is_confirmed = 1;
			$m_crp->save();

			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Distribution network added.');
			$this->add_feedback($feedback);

			$this->redirect($redirect);
		}

		if ($provider = $this->input->post('remove'))
		{
			$m_crp = Model_Content_Release_Plus::find_content_with_provider($content_id, $provider);
			
			if ($m_crp) 
			{
				$m_crp->delete();

				$feedback = new Feedback('success');
				$feedback->set_title('Success!');
				$feedback->set_text('Distribution network removed.');
				$this->add_feedback($feedback);
			}

			$this->redirect($redirect);
		}

		$this->vd->redirect = $redirect;
		$this->vd->content = $content;

		$rp_names = Model_Content_Release_Plus::names();
		$rp_codes = Model_Content_Release_Plus::codes();

		$this->vd->rp_names = $rp_names;
		$this->vd->rp_codes = $rp_codes;

		$m_crps = Model_Content_Release_Plus::find_all_content($content_id);
		$m_crp_selected = array();
		$m_crp_confirmed = array();

		foreach ($m_crps as $m_crp)
		{
			if ($m_crp->is_confirmed)
			     $m_crp_confirmed[$m_crp->provider] = $m_crp;
			else $m_crp_selected[$m_crp->provider] = $m_crp;
		}

		$this->vd->m_crp_selected = $m_crp_selected;
		$this->vd->m_crp_confirmed = $m_crp_confirmed;

		$content_view = 'admin/publish/partials/edit_dist_content';
		$this->load->view($content_view);
	}
	
	protected function fetch_results($type, $chunkination, $filter = null)
	{
		if (!$filter) $filter = 1;
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			if (($mContent = Model_Content::find_slug($filter_search)))
			{
				$this->create_filter_search($filter_search, 'slug');
				// find the exact match for the given slug
				$filter = "{$filter} AND c.id = {$mContent->id}";
			}
			else if (is_numeric_int($filter_search) && ($mContent = Model_Content::find((int) $filter_search)))
			{
				$this->create_filter_search($filter_search, 'id');
				// find the exact match for the given id
				$filter = "{$filter} AND c.id = {$mContent->id}";
			}
			else
			{
				$this->create_filter_search($filter_search);
				// restrict search results to those with a matching title
				$titleFilter = preg_replace('#[^a-z0-9]#i', '%', $filter_search);
				$titleFilter = sprintf('%%%s%%', $titleFilter);
				$titleFilter = escape_and_quote($titleFilter);
				$filter = "{$filter} AND (c.title LIKE {$titleFilter})";
			}
		}
		
		if (($filter_user = $this->input->get('filter_user')) !== false)
		{
			$filter_user = (int) $filter_user;
			$this->create_filter_user($filter_user);
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
			$use_additional_tables = true;
		}

		if (($filter_site = $this->input->get('filter_site')) !== false)
		{
			$filter_site = (int) $filter_site;
			$this->create_filter_site($filter_site);
			if ($filter_site === -1)
			     $filter = "{$filter} AND IFNULL(u.virtual_source_id, 0) = 0";
			else $filter = "{$filter} AND u.virtual_source_id = {$filter_site}";
			$use_additional_tables = true;
		}
		
		if (($filter_company = $this->input->get('filter_company')) !== false)
		{
			$filter_company = (int) $filter_company;
			$this->create_filter_company($filter_company);	
			// restrict search results to this user
			$filter = "{$filter} AND cm.id = {$filter_company}";
			$use_additional_tables = true;
		}

		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"INNER JOIN nr_company cm ON c.company_id = cm.id
			 INNER JOIN nr_user u ON cm.user_id = u.id";
		
		if ($this->review_mode)
		{
			// prefer premium and 
			// older content
			$order_str = "ORDER BY
				c.is_premium = 1 DESC,
				c.date_publish ASC";
		}
		else
		{
			// prefer newer content
			$order_str = "ORDER BY
				c.id DESC";
		}

		// calculate review totals
		if ($this->review_mode)
			$this->calculate_under_review_totals($type);

		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id FROM 
			nr_content c {$additional_tables}
			WHERE c.type = ? AND {$filter}
			{$order_str}
			{$limit_str}";

		$query = $this->db->query($sql, array($type));
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
			
		$review_fields = null;
		$review_tables = null;	

		if ($this->review_mode)
		{
			$review_fields = "
				cd.summary, cd.content,
				rd.date_created AS date_rejected,
				rd.raw_data AS rejection_data__raw_data,
				hd.date_created AS date_hold,
				hd.raw_data AS hold_data__raw_data,";

			$review_tables = "
				LEFT JOIN nr_content_data cd
				ON c.id = cd.content_id
				LEFT JOIN nr_rejection_data rd
				ON c.id = rd.content_id
				LEFT JOIN nr_hold_data hd
				ON c.id = hd.content_id";
		}

		$u_prefixes = Model_User::__prefixes('u');
		
		$sql = "SELECT c.*,
			{$review_fields}
			cm.name AS o_company_name,
			cm.id AS o_company_id,
			u.email AS o_user_email,
			u.id AS o_user_id,
			ad.date_created AS date_approved,
			{$u_prefixes}
			FROM nr_content c
			LEFT JOIN nr_company cm
			ON c.company_id = cm.id
			LEFT JOIN nr_user u 
			ON cm.user_id = u.id
			LEFT JOIN nr_approval_data ad
			ON c.id = ad.content_id			
			{$review_tables}
			WHERE c.id IN ({$id_str}) 
			{$order_str}";
			
		$query = $this->db->query($sql);
		$results = Model_Content::from_db_all($query, array(
			'rejection_data' => 'Model_Rejection_Data',
			'hold_data' => 'Model_Hold_Data',
		));
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$reject_modal = new Modal();
		$this->add_eob($reject_modal->render(800, 600));
		$this->vd->reject_modal_id = $reject_modal->id;

		$transfer_modal = new Modal();
		$transfer_modal->set_title('Transfer Content');
		$modal_view = 'admin/partials/transfer_to_company_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_content($modal_content);
		$modal_view = 'admin/partials/transfer_to_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_footer($modal_content);
		$this->add_eob($transfer_modal->render(600, 500));
		$this->vd->transfer_modal_id = $transfer_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/publish/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/publish/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	protected function calculate_under_review_totals($type)
	{
		// ----------------------------------------
	}
	
	protected function is_allowed_status($status)
	{
		if ($status === 'all') return true;
		if ($status === 'under_review') return true;
		if ($status === 'published') return true;
		if ($status === 'scheduled') return true;
		if ($status === 'draft') return true;
		return false;
	}
	
	protected function has_under_review($type)
	{
		if ($type === Model_Content::TYPE_PR || $type == Model_Content::TYPE_NEWS) 
			return true;
		return false;
	}

}

?>