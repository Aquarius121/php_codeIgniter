<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Customize_Controller extends Manage_Base {

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Company Newsroom';
		$this->vd->title[] = 'Customize';
	}
	
	public function index()
	{
		$company_id = $this->newsroom->company_id;		
		$custom = Model_Newsroom_Custom::find($company_id);
		$this->vd->custom = $custom;

		if ($custom)
		     $this->vd->content_type_labels = $custom->content_type_labels();
		else $this->vd->content_type_labels = null;

		$now = Date::$now->format(DATE::FORMAT_MYSQL);
		$criteria = array();
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array('date_expires > ', $now);				
		if ($m_pr_token = Model_Newsroom_Preview_Token::find($criteria))
			$this->vd->m_pr_token = $m_pr_token;

		$info_modal = new Modal();
		$info_modal->set_title('What is This?');
		$this->add_eob($info_modal->render(700, 480));
		$this->vd->info_modal_id = $info_modal->id;
	
		$this->load->view('manage/header');
		$this->load->view('manage/newsroom/customize');
		$this->load->view('manage/footer');
	}
	
	public function save()
	{
		if (!($post = $this->input->post())) return;
		$post = Raw_Data::from($post);
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		// checkbox so won't post on unchecked
		if (empty($post['use_white_header']))
			$post['use_white_header'] = 0;

		$color_fields = array(
			'back_color',
			'link_color', 
			'link_hover_color', 
			'text_color', 	
			'secondary_color',
		);
		
		// must be a 6 character hex code or 
		// the string 'transparent' only.
		$pattern = '#^(transparent|\#[a-f0-9]{6})$#s';

		foreach ($color_fields as $field) 
		{
			if (!empty($post[$field]))
			{
				$post[$field] = strtolower($post[$field]);
				if (!preg_match($pattern, $post[$field]))
					$post[$field] = null;
			}
		}
					
		$company_id = $this->newsroom->company_id;		
		$custom = Model_Newsroom_Custom::find($company_id);
		if (!$custom) $custom = new Model_Newsroom_Custom();
		$custom->company_id = $company_id;
		$custom->values($post);

		// serialize using raw_data functionality
		$custom->raw_data($post['content_type_labels'],
			'content_type_labels');

		if (Auth::is_admin_online())
		{
			$rdo = $custom->raw_data_object();
			$rdo->inject_pre_header = $post['inject_pre_header'];
			$custom->raw_data($rdo);
		}
		
		if ($this->input->post('is_preview'))
		{
			Detached_Session::write('nr_custom', $custom);
			$preview_url = Detached_Session::save($this->newsroom);
			$this->redirect($preview_url, false);
		}
		else
		{
			if (Auth::is_admin_controlled())
			{
				$domain = $this->input->post('newsroom_domain');
				if (!$domain) $this->newsroom->domain = null;
				else if ($domain != $this->newsroom->domain)
				{
					if (!$this->newsroom->set_domain($domain))
					{
						// load feedback message for the user
						$feedback = new Feedback('error', 'Error!', 'The domain name could not be assigned. Make sure 
							that the domain is not in use and that the A record is setup correctly. ');
						$this->add_feedback($feedback);
					}
				}
			}

			if (($image = Model_Image::find($custom->logo_image_id)))
			{
				$cc = Model_Company_Color::find_or_create($this->newsroom->company_id);
				$filename = $image->variant('header-thumb')->filename;

				if ($cc->filename !== $filename)
				{
					$file = Stored_File::file_from_filename($filename);
					$extractor = new Logo_Color_Extractor($file);
					$color = $extractor->extract();				
					$cc->filename = $filename;
					$cc->color = $color;
					$cc->save();
				}
			}
			
			$this->newsroom->save();
			$custom->save();
			
			// redirect back to this page after
			$redirect_url = 'manage/newsroom/customize';
			
			// load feedback message for the user
			$feedback = new Feedback('success', 'Saved!', 'The information has been saved.');
			$this->add_feedback($feedback);
		
			// update the dashboard progress bar 
			Model_Bar::done('dashboard', 'customize');
			
			// change the newsroom name and redirect
			$name = Model_Newsroom::normalize_name($post['name']);
			if ($name && $name != $this->newsroom->name && 
			    Model_Newsroom::name_available($name))
			{
				// old redirect? remove it and claim
				$old_nrr = Model_Newsroom_Redirect::find($name);
				if ($old_nrr) $old_nrr->delete();

				if ($this->newsroom->is_active)
				{
					$nrr = new Model_Newsroom_Redirect();
					$nrr->old_slug = $this->newsroom->name;
					$nrr->new_slug = $name;
					$nrr->save();
				}

				$this->newsroom->name = $name;
				$this->newsroom->save();
				$new_url = $this->newsroom->url($redirect_url);
				$this->redirect($new_url, false);
			}
		
			// redirect back to the company details
			$this->set_redirect($redirect_url);
		}
	}

	public function generate_private_preview($company_id)
	{
		if (!$m_pr_token = Model_Newsroom_Preview_Token::find($company_id))
		{
			$m_pr_token = new Model_Newsroom_Preview_Token();
			$m_pr_token->company_id = $company_id;
		}
		$m_pr_token->generate();
		$m_pr_token->save();
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Private preview link generated successfully.');		
		$this->add_feedback($feedback);
		$this->redirect('manage/newsroom/customize');
	}
	
	public function name_test()
	{
		$res = new stdClass();
		$name = $this->input->post('name');
		$name = Model_Newsroom::normalize_name($name);
		$res->value = $name;
		
		if ($name == $this->newsroom->name)
		{
			$res->available = true;
			return $this->json($res);
		}
		
		$res->available = Model_Newsroom::name_available($name);
		return $this->json($res);
	}
	
	public function defaults()
	{
		// update the dashboard progress bar 
		Model_Bar::done('dashboard', 'customize');
		
		// delete the newsroom customization
		$company_id = $this->newsroom->company_id;
		$custom = Model_Newsroom_Custom::find($company_id);
		if ($custom) $custom->delete();
		$custom = new Model_Newsroom_Custom();
		$custom->company_id = $company_id;
		$custom->save();
		
		// load feedback message for the user
		$feedback = new Feedback('success', 'Saved!', 'The information has been saved.');
		$this->add_feedback($feedback);
			
		// redirect back to this page after
		$url = 'manage/newsroom/customize';
		$this->redirect($url);
	}
	
}