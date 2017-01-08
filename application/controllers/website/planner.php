<?php

load_controller('website/base');

class Planner_Controller extends Website_Base {

	const SOURCE_DIRECT = 'direct';
	const SOURCE_INTRO  = 'intro';

	public $title = 'Press Release Planner';

	public function __construct()
	{
		parent::__construct();
		$this->vd->hide_footer = true;
	}

	public function index()
	{
		$this->redirect('planner/intro');
	}

	public function intro()
	{
		$planner = Model_Sales_Planner::create();
		$planner->step_max = 0;
		$planner->source = static::SOURCE_INTRO;
		$planner->save();

		$this->vd->planner = $planner;
		$this->render_website('website/pages/planner/intro');
	}

	public function zero()
	{
		$planner = Model_Sales_Planner::create();
		$planner->step_max = 0;
		$planner->source = static::SOURCE_DIRECT;
		$planner->save();
		
		$url = sprintf('planner/one/%s',
			$planner->id);
		$this->redirect($url);
	}

	public function save($uuid)
	{
		$this->setup($uuid);
		$planner = $this->vd->planner;
		$rdata = $this->vd->rdata;

		if ($this->input->post())
		{
			$post_data = Raw_Data::from_array($this->input->post());
			unset($post_data->next);

			foreach ($post_data as $k => $v)
				$rdata->$k = $v;
			$planner->raw_data($rdata);
			if (($email = filter_var($rdata->email, FILTER_VALIDATE_EMAIL)))
				$planner->email = $email;
			$planner->save();

			$next = $this->input->post('next');
			$url = sprintf('planner/%s/%s',
				$next, $planner->id);
			$this->redirect($url);
		}
	}

	protected function setup($uuid, $max)
	{
		$planner = Model_Sales_Planner::find($uuid);
		if (!$planner) $this->redirect('planner');
		$planner->step_max = max($max, $planner->step_max);
		$planner->save();
		
		$rdata = $planner->raw_data_object();
		$this->vd->planner = $planner;
		$this->vd->rdata = $rdata;
	}

	public function one($uuid)
	{
		$this->setup($uuid, 1);
		$this->render('one');
	}

	public function two($uuid)
	{
		$this->setup($uuid, 2);
		$this->render('two');
	}

	public function three($uuid)
	{
		$this->setup($uuid, 3);
		$this->render('three');
	}

	public function four($uuid)
	{
		$this->setup($uuid, 4);
		$this->render('four');
	}

	public function five($uuid)
	{
		$this->setup($uuid, 5);
		$this->render('five');
	}

	public function six($uuid)
	{
		$this->setup($uuid, 6);
		$this->render('six');
	}

	public function seven($uuid)
	{
		$this->setup($uuid, 7);
		$this->render('seven');
	}

	public function finish($uuid)
	{
		$this->setup($uuid);
		$planner = $this->vd->planner;
		$planner->is_finished = 1;
		$planner->save();

		$this->notify_staff($planner);
		$this->redirect('planner/thanks');
	}

	public function thanks()
	{
		$this->render_website('website/pages/planner/thanks');
	}

	public function review($uuid)
	{
		$this->redirect(sprintf('admin/other/planner/review/%s', $uuid));
	}

	protected function render($step)
	{
		$this->vd->step = $step;
		$this->render_website('website/pages/planner/container');
	}

	protected function notify_staff($planner)
	{
		// create email
		$email = new Email();
		$email->__avoid_conversation();
		$email->set_from_email($this->conf('email_address'));
		$email->set_from_name($this->conf('email_name'));
		$email->set_subject('Newswire PR Planner');
		$email->set_message($this->load->view_return('email/planner'));
		$email->enable_html();

		// lookup staff who want an email and send to each
		$emails_block = Model_Setting::value('staff_email_pr_sales_planner');
		$cc_emails = Model_Setting::parse_block($emails_block);

		foreach ($cc_emails as $cc_email)
		{
			// send copy to the staff
			$email->set_to_email($cc_email);
			Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);
		}
	}
	
}