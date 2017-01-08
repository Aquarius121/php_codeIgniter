<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Collab_Controller extends Manage_Base { 

	public function index($content_id, $create = false)
	{
		$m_content = Model_Content::find($content_id);
		if (!$m_content) $this->denied();
		if ($m_content->company_id != $this->newsroom->company_id)
			$this->denied();

		$criteria = array();
		$criteria[] = array('content_id', $content_id);
		$criteria[] = array('is_deleted', 0);
		$order = array('date_created', 'desc');
		$previous = Model_Content_Collab::find_all($criteria, $order);
		
		if (!count($previous))
		{
			$m_collab = Model_Content_Collab::create($m_content);
			$this->redirect(build_url(array(
				'manage/publish/collab', 
				$content_id
			)));
		}
		else if ($create)
		{
			$previous_rdo = $previous[0]->raw_data_object();
			$m_collab = Model_Content_Collab::create($m_content);
			$rdo = $m_collab->raw_data_object();
			$rdo->invite_suggests = Raw_Data::from_auto($previous_rdo->users);
			$rdo->invite_message = $previous_rdo->invite_message;
			if (isset($rdo->invite_suggests[$this->self_suid()]))
				unset($rdo->invite_suggests[$this->self_suid()]);
			$m_collab->raw_data($rdo);
			$m_collab->save();

			$this->redirect(build_url(array(
				'manage/publish/collab', 
				$content_id
			)));
		}
		else
		{
			$this->vd->show_create = true;
			$m_collab = $previous[0];
		}

		$this->vd->m_collab = $m_collab;
		$this->vd->previous = $previous;
		$this->vd->m_content = $m_content;

		$this->vd->title[] = $m_content->title;
		$this->vd->title[] = 'Collaboration';

		$collab_rd = $m_collab->raw_data_object();
		$this->vd->collab_rd = $collab_rd;
		$collab_rd->users = Raw_Data::from_object($collab_rd->users);
		$this->vd->users = $collab_rd->users;

		$this->vd->self_suid = $this->self_suid();
		$collab_rd->invite_suggests = Raw_Data::from_auto($collab_rd->invite_suggests);
		$this->vd->invite_suggests = $collab_rd->invite_suggests;
		foreach ($collab_rd->invite_suggests as $k => $v)
			if ($this->find_existing_user($collab_rd->users, $v->email))
				unset($collab_rd->invite_suggests[$k]);

		$this->load->view('manage/header');
		$this->load->view('manage/publish/collab');
		$this->load->view('manage/footer');
	}

	public function view($sessid)
	{
		$m_collab = Model_Content_Collab::find($sessid);
		if (!$m_collab) $this->denied();
		$m_content = Model_Content::find($m_collab->content_id);
		if (!$m_content || $m_content->owner()->id != Auth::user()->id)
			$this->denied();

		$this->redirect(sprintf('view/collab/%s?noname=1', $sessid));
	}

	public function delete()
	{
		$id = $this->input->post('id');
		$m_collab = Model_Content_Collab::find($id);
		if (!$m_collab) $this->denied();
		$m_collab->is_deleted = 1;
		$m_collab->save();
	}

	public function send()
	{
		$sessid = $this->input->post('sessid');
		$m_collab = Model_Content_Collab::find($sessid);
		if (!$m_collab) $this->denied();
		$m_content = Model_Content::find($m_collab->content_id);
		if (!$m_content || $m_content->owner()->id != Auth::user()->id)
			$this->denied();

		$collab_rd = $m_collab->raw_data_object();
		$collab_rd->users = Raw_Data::from_object($collab_rd->users);
		$collab_rd->annotations = Raw_Data::from_object($collab_rd->annotations);
		$collab_rd->color = (int) $collab_rd->color;

		$no_reply = $this->conf('no_reply_email');
		$names    = $this->input->post('name');
		$emails   = $this->input->post('email');
		$message  = $this->input->post('message');

		foreach ($emails as $k => $email)
		{
			$email = filter_var($email, FILTER_VALIDATE_EMAIL);
			if (!$email) continue;

			if (($suid = $this->find_existing_user($collab_rd->users, $email)))
			{
				$collab_rd->users[$suid]->name = $names[$k];
			}
			else if (($suid = $this->find_existing_user($collab_rd->invite_suggests, $email)))
			{
				$collab_rd->users[$suid] = new Raw_Data();
				$collab_rd->users[$suid]->name = $names[$k];
				$collab_rd->users[$suid]->email = $emails[$k];
				$collab_rd->users[$suid]->color = $collab_rd->color++;
				$collab_rd->annotations[$suid] = array();
			}
			else
			{
				$suid = uniqid();
				$collab_rd->users[$suid] = new Raw_Data();
				$collab_rd->users[$suid]->name = $names[$k];
				$collab_rd->users[$suid]->email = $emails[$k];
				$collab_rd->users[$suid]->color = $collab_rd->color++;
				$collab_rd->annotations[$suid] = array();
			}				
						
			$_link = sprintf('view/collab/%s/%s', $sessid, $suid);
			$_link = $this->website_url($_link);
			$_message  = String_Util::inject($message, array(
				'name' => $names[$k],
				'link' => $_link,
			));

			$em = new Email('Newswire');
			$em->set_to_email($email);
			$em->set_from_email($no_reply);
			$em->set_header('Reply-To', Auth::user()->email);
			$em->set_subject('Feedback requested on our Press Release');
			$em->set_message($_message);
			Mailer::queue($em);
		}

		$collab_rd->invite_message = $message;
		$m_collab->raw_data($collab_rd);
		$m_collab->save();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Your email invitations have been sent!');
		$this->add_feedback($feedback);

		$url = sprintf('manage/publish/collab/view/%s', $sessid);
		$this->redirect($url);		
	}

	protected function find_existing_user($rd_users, $email)
	{
		foreach ($rd_users as $suid => $user)
			if ($user->email == $email)
				return $suid;
		return false;
	}

	protected function self_suid()
	{
		Model_Content_Collab::suid(Auth::user());
	}

}