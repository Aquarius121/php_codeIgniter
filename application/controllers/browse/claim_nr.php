<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Claim_NR_Controller extends Browse_Base {

	public function __construct()
	{
		$this->__ci_setup();
		$this->is_private_preview();
		$this->check_token_for_newsroom_redirect();
		parent::__construct();
	}

	protected function is_private_preview()
	{
		$host_pattern = $this->conf('host_pattern');
		$host_suffix = $this->conf('host_suffix');

		if (preg_match($host_pattern, $GLOBALS['env']['host'], $match))
		{
			if ($this->uri->segment(2) == 'claim_nr' && $this->uri->segment(3) == 'rep')
			{
				$token = $this->uri->segment(4);

				if ($token)
					$this->ac_nr_preview_token = $token;

				return true;
			}
		}
	}

	// this method detects newsroom redirects with a claim token
	// and redirects to the new newsroom with the new claim token
	protected function check_token_for_newsroom_redirect()
	{
		$host_pattern = $this->conf('host_pattern');
		$host_suffix = $this->conf('host_suffix');

		if (preg_match($host_pattern, $GLOBALS['env']['host'], $match))
		{
			if (($redirect = Model_Newsroom_Redirect::find($match[1])) &&
					$this->uri->segment(2) == 'claim_nr' &&
					$this->uri->segment(3) == 'rep')
			{
				// extract the token for the old newsroom
				$provided_token = $this->uri->segment(4);

				$old_newsroom = Model_Newsroom::find_name($redirect->old_slug);
				$new_newsroom = Model_Newsroom::find_name($redirect->new_slug);

				if (!$new_newsroom) return;

				$new_token = Model_Newsroom_Claim_Token::find($new_newsroom->company_id);

				if ($new_newsroom && $new_token)
				{
					if ($old_newsroom)
					{
						// if the old newsroom still exists we will try and find 
						// the correct/expected token for that old newsroom 
						// to check it before we continue. if no old newsroom
						// is found we must assume the token was correct and
						// redirect anyway

						$old_token = Model_Newsroom_Claim_Token::find($old_newsroom->company_id);
						if ($old_token->token != $provided_token) return;
					}

					// construct the redirect url manually
					// because the newsroom url method doesnt work
					// until the CIL_Controller is constructed
					$redirect_url = sprintf('c/%s', $new_token->token);
					$host = "{$new_newsroom->name}{$host_suffix}";		
					$url = "http://{$host}/{$redirect_url}";
					$this->redirect_301($url, true);
				}
			}
		}
	}

	public function index()
	{
		$company_id = $this->newsroom->company_id;
		$nr_source = $this->newsroom->source;

		$criteria = array();
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array('status', Model_Newsroom_Claim::STATUS_CLAIMED);

		if (!$this->newsroom->is_scraped() || $m_claim = Model_Newsroom_Claim::find($criteria))
				$this->redirect('');

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);

		$cat_field = "{$nr_source}_category_id";
		if ($nr_source === Model_Company::SOURCE_CRUNCHBASE)
			$cat_field = "cb_category_id";

		$sql = "SELECT sc.*,
				c.name AS category_name
				FROM {$tbl_prefix}company sc
				LEFT JOIN {$tbl_prefix}category c
				ON sc.{$cat_field} = c.id
				WHERE company_id = ?";

		$scraped_comp = Model::from_sql($sql, array($company_id));

		$this->vd->cb_category_name = $scraped_comp->category_name;

		$this->vd->is_claim_nr = 1;

		$sess_tokened_visit_nr_id = $this->session->get('ac_nr_tokened_visit_nr_id');
		if($sess_tokened_visit_nr_id && $sess_tokened_visit_nr_id == $this->newsroom->company_id)
			$this->vd->is_from_private_link = 1;

		// for the time being redirecting to
		// fill_form to fill form instead of payment

		$this->redirect('browse/claim_nr/fill_form');

		// $this->load->view('browse/header');
		// $this->load->view('browse/claim_nr/main');
		// $this->load->view('browse/footer');			
	}

	public function checkout()
	{
		$item_nr_claim = Model_Item::find_slug('claim-nr');
		Cart::instance()->reset();
		$cart = Cart::instance();
		$cart_item = $cart->add($item_nr_claim);
		$cart_item->track->claim_company_id = $this->newsroom->company_id;
		$cart_item->callback = 'manage/newsroom/company';

		$this->session->set('skip_welcome_email', 1);
		$this->session->set('skip_create_newsroom', 1);

		$coupon = Model_Coupon::find_code('44OFF');
		$cart->set_coupon($coupon);

		$this->set_redirect('order');
		$cart->save();
	}

	public function fill_form()
	{
		$company_id = $this->newsroom->company_id;		
		$nr_source = $this->newsroom->source;

		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($nr_source);
		$cat_field = "{$nr_source}_category_id";
		if ($nr_source === Model_Company::SOURCE_CRUNCHBASE)
			$cat_field = "cb_category_id";
		
		$sql = "SELECT sc.*,
				c.name AS category_name
				FROM {$tbl_prefix}company sc
				LEFT JOIN {$tbl_prefix}category c
				ON sc.{$cat_field} = c.id
				WHERE company_id = ?";

		if (!$scraped_comp = Model::from_sql($sql, array($company_id)))
			$this->redirect('');

		$status = array(Model_Newsroom_Claim::STATUS_CLAIMED, Model_Newsroom_Claim::STATUS_CONFIRMED);
		$status_list = sql_in_list($status);
		
		$criteria = array();
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array("status IN ({$status_list})");

		if ($m_claim = Model_Newsroom_Claim::find($criteria))
			$this->redirect('');		

		$this->vd->category_name = $scraped_comp->category_name;

		$this->vd->is_claim_nr = 1;
		$this->vd->full_width = true;

		$sess_tokened_visit_nr_id = $this->session->get('ac_nr_tokened_visit_nr_id');
		if($sess_tokened_visit_nr_id && $sess_tokened_visit_nr_id == $this->newsroom->company_id)
			$this->vd->is_from_private_link = 1;

		$this->load->view('browse/header');
		$this->load->view('browse/claim_nr/verification_form');
		$this->load->view('browse/footer');
	}

	public function save()
	{
		if (!$this->input->post('claim'))
			$this->redirect('browse/claim_nr');

		$company_id = $this->newsroom->company_id;

		if (!$this->newsroom->is_scraped())
				$this->redirect('');

		$claim = new Model_Newsroom_Claim();

		$post = $this->input->post();
		
		$claim->company_id = $company_id;
		$claim->rep_name = $post['rep_name'];
		$claim->email = $post['email'];
		$claim->phone = $post['phone'];
		$claim->date_claimed = Date::$now->format(Date::FORMAT_MYSQL);
		$claim->status = Model_Newsroom_Claim::STATUS_CLAIMED;

		$sess_tokened_visit_nr_id = $this->session->get('ac_nr_tokened_visit_nr_id');
		if($sess_tokened_visit_nr_id && $sess_tokened_visit_nr_id == $this->newsroom->company_id)
			$claim->is_from_private_link = 1;

		if(getenv('REMOTE_ADDR'))
			$claim->remote_addr = getenv('REMOTE_ADDR');

		$claim->save();

		$this->redirect('browse/claim_nr/success');
	}

	public function success()
	{
		$this->vd->full_width = true;

		$this->load->view('browse/header');
		$this->load->view('browse/claim_nr/success');
		$this->load->view('browse/footer');
	}

	public function rep($token = null)
	{
		if ( ! $token)
			$this->redirect('');

		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('token', $token);
		if ( ! $token = Model_Newsroom_Claim_Token::find($criteria))
			$this->redirect('');

		$this->session->set('ac_nr_tokened_visit_nr_id', $this->newsroom->company_id);
		$sess_var_name = "show_video_guide_for_{$this->newsroom->company_id}";
		$this->session->set($sess_var_name, 1);
		$this->redirect('');

	}


}

?>