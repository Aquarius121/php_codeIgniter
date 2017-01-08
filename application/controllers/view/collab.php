<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Collab_Controller extends Browse_Base {

	protected function render_raw(Model_Content $m_content)
	{
		$modal = new Modal();
		$modal->set_title('Conversation');
		$modal->set_id('conversation-modal');
		$footer = $this->load->view_return('browse/collab/conversation-footer');
		$modal->set_footer($footer);
		$html = $modal->render(500, 500);
		$this->add_eob($html);

		$real_newsroom = Model_Newsroom::find($m_content->company_id);
		if (!$real_newsroom) show_404();		
		$this->newsroom = $real_newsroom;
		$this->vd->nr_custom = $this->newsroom->custom();
		$this->vd->nr_profile = $this->newsroom->profile();
		$this->vd->nr_contact = $this->newsroom->contact();
		$this->vd->m_content = $m_content;
		$this->load->view('browse/collab/raw');
	}

	public function index($sessid, $suid = null)
	{
		if (!$this->is_common_host)
		{
			$uri = build_url(array(
				'view/collab',
				$sessid,
				$suid,
			));

			$uri = $this->website_url($uri);
			$uri = gstring($uri);
			$this->redirect($uri);
		}

		$session_suid = $this->session->get('collab-suid');
		$session = Model_Content_Collab::find($sessid);
		if (!$session) show_404();
		$sessrd = $session->raw_data_object();
		$sessrd->users = Raw_Data::from_auto($sessrd->users);
		if (!isset($sessrd->detached_content))
			show_404();

		$owner = $session->owner();
		$this->vd->owner = $owner;

		if (Auth::is_admin_online())
			Auth::admo($owner);

		// the owner should always come in with the same suid
		if (Auth::is_user_online() && Auth::user()->id == $owner->id)
		{
			$session_suid = substr(md5(Auth::user()->id), 0, 16);
			$this->vd->su_name = Auth::user()->name();
			$this->vd->su_email = Auth::user()->email;
		}

		if ($suid) 
		{
			$this->session->set('collab-suid', $suid);
			$base = 'view/collab';
			$url = build_url($base, $sessid);
			$this->redirect($url);
		}

		if (!$session_suid)
		{
			$suid = uniqid();
			$session_suid = $suid;
			$this->session->set('collab-suid', $suid);
		}

		// use suid from session
		// so that the url in address
		// bar can be shared with others
		$suid = $session_suid;

		if ($session->is_deleted)
		{
			$latest_session = Model_Content_Collab::find_latest($session->content_id);

			if ($latest_session)
			{
				$feedback = new Feedback('warning');
				$feedback->set_title('Attention!');
				$feedback->set_text('You\'ve been redirected to the latest available revision.');
				$this->add_feedback($feedback);

				$base = 'view/collab';
				$url = build_url($base, $latest_session->id);
				$this->redirect($url);
			}

			$m_dcontent = Model_Detached_Content::from_object($sessrd->detached_content);
			$this->title = $m_dcontent->title;
			$this->load->view('browse/collab/deleted');
			return;
		}

		if ($sessrd->users[$suid])
		{
			$this->vd->su_name = $sessrd->users[$suid]->name;
			$this->vd->su_email = $sessrd->users[$suid]->email;
		}
		else if (!$this->vd->su_email)
		{
			$this->vd->su_name = $this->session->get('collab-su-name');
			$this->vd->su_email = $this->session->get('collab-su-email');
		}

		$this->vd->suid = $suid;
		$this->vd->sessid = $sessid;

		$latest_session = Model_Content_Collab::find_latest($session->content_id);

		if ($latest_session && $latest_session->id != $sessid)
		{
			$base = 'view/collab';
			$latest_url = build_url($base, $latest_session->id, $suid);
			$latest_url = insert_into_query_string($latest_url, array('noname' => 1));
			$feedback = new Feedback('success');
			$feedback->set_title('Updated!');
			$feedback->set_html(sprintf(
				'A new version of this content is available for review. 
				Click <a href="%s">here to switch</a>.',
				$latest_url));
			$this->use_feedback($feedback);
		}

		$m_dcontent = Model_Detached_Content::from_object($sessrd->detached_content);
		$this->vd->title[] = $m_dcontent->title;
		$this->vd->title[] = sprintf('Version %d', $session->version);
		$this->render_raw($m_dcontent);
	}

	public function annotator($sessid, $suid, $action = null, $id = null)
	{
		if ($action === null)
		{
			return $this->json(array(
				// storage api metadata
				'name' => 'Newswire Collaboration',
				'version' => '1.0',
			));
		}

		$m_collab = Model_Content_Collab::find($sessid);
		if (!$m_collab) show_404();

		$input = trim(file_get_contents('php://input'));
		$input = Raw_Data::from_auto(json_decode($input));

		$method = sprintf('annotator_%s', $action);
		if (method_exists($this, $method))
			return $this->$method($m_collab, $suid, $input, $id);
		show_404();
	}

	public function annotator_annotations($m_collab, $suid, $input, $id = null)
	{
		if ($this->env['request_method'] === $this::REQUEST_GET && $id !== null)
			return $this->annotator_read($m_collab, $suid, $input, $id);
		if ($this->env['request_method'] === $this::REQUEST_GET)
			return $this->annotator_index($m_collab, $suid, $input, $id);
		if ($this->env['request_method'] === $this::REQUEST_POST)
			return $this->annotator_create($m_collab, $suid, $input, $id);
		if ($this->env['request_method'] === $this::REQUEST_PUT)
			return $this->annotator_update($m_collab, $suid, $input, $id);
		if ($this->env['request_method'] === $this::REQUEST_DELETE)
			return $this->annotator_delete($m_collab, $suid, $input, $id);
	}

	public function annotator_index()
	{
		$this->json(array());
	}

	public function annotator_create($m_collab, $suid, $input)
	{
		$rdo = $m_collab->raw_data_object();
		$this->schedule_email($m_collab->id, $rdo);
		
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		if (!is_array($rdo->annotations[$suid]))
			$rdo->annotations[$suid] = array();
		
		$rdo->annotations[$suid][] = $input;
		$id = (-1 + count($rdo->annotations[$suid]));
		$rdo->annotations[$suid][$id]->id = $id;
		$rdo->annotations[$suid][$id]->resolved = false;
		$rdo->annotations[$suid][$id]->date_created = (string) Date::$now;
		$rdo->annotations[$suid][$id]->date_updated = (string) Date::$now;
		$rdo->annotations[$suid][$id]->conversation = array();
		$m_collab->raw_data($rdo);
		$m_collab->save();

		$this->json($rdo->annotations[$suid][$id]);
	}

	public function annotator_update($m_collab, $suid, $input, $id)
	{
		$id = (int) $id;
		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		if (!isset($rdo->annotations[$suid][$id])) show_404();
		
		foreach ($input as $k => $v)
			$rdo->annotations[$suid][$id]->$k = $v;
		$rdo->annotations[$suid][$id]->date_updated = (string) Date::$now;
		$m_collab->raw_data($rdo);
		$m_collab->save();

		$this->json($rdo->annotations[$suid][$id]);
	}

	public function annotator_read($m_collab, $suid, $input, $id)
	{
		// ---------------------------------------
	}

	public function annotator_delete($m_collab, $suid, $input, $id)
	{
		$id = (int) $id;
		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		if (!isset($rdo->annotations[$suid][$id])) show_404();
		$rdo->annotations[$suid][$id] = false;
		$m_collab->raw_data($rdo);
		$m_collab->save();

		http_response_code(204);
	}

	public function annotator_resolve($m_collab, $suid, $input, $id)
	{
		$id = (int) $id;
		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		if (!isset($rdo->annotations[$suid][$id])) show_404();
		$rdo->annotations[$suid][$id]->resolved = true;
		$m_collab->raw_data($rdo);
		$m_collab->save();

		http_response_code(204);
	}

	public function annotator_search($m_collab, $_, $input)
	{
		$id = (int) $this->input->get('id');
		$suid = $this->input->get('suid');
		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);
		if (!isset($rdo->annotations[$suid])) show_404();
		$annotations = $rdo->annotations[$suid];
		$user = (object) $rdo->users[$suid];
		$user->suid = $suid;
		$owner = $m_collab->owner();
		$is_owner = Auth::is_user_online() && 
			Auth::user()->id == $owner->id;

		if ($id === -1)
		{
			foreach ($annotations as $k => &$v)
				if ($v === false) unset($annotations[$k]);
				else $v = $this->__annotator_permissions($v, $user, $is_owner);
			return $this->json(array(
				// return all annotations for a user
				'rows' => array_values($annotations),
				'total' => count($annotations),
			));
		}

		// annotation has been deleted? => no results
		if (!isset($annotations[$id]) ||
			$annotations[$id] === false)
		{
			return $this->json(array(
				'rows' => array(),
				'total' => 0,
			));
		}

		$v = $annotations[$id];
		$v = $this->__annotator_permissions($v, $user, $is_owner);
		$this->json(array(
			// return the single matching
			// annotation result 
			'rows' => array($v),
			'total' => 1,
		));
	}

	protected function __annotator_permissions(&$annotation, &$user, $is_owner)
	{
		$annotation->user = &$user;
		$annotation->permissions = array(
			'read' => array(),
			'admin' => array($user->suid),
			'update' => array($user->suid),
			'delete' => array($user->suid),
		);
		
		return $annotation;
	}

	public function update_su()
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($data->sessid);
		if (!$m_collab) return;

		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);
		$rdo->color = (int) $rdo->color;
		
		if ($rdo->users[$data->suid])
		{
			$rdo->users[$data->suid]->name = $data->name;
			$rdo->users[$data->suid]->email = $data->email;
		}
		else
		{
			$rdo->users[$data->suid] = new Raw_Data();
			$rdo->users[$data->suid]->name = $data->name;
			$rdo->users[$data->suid]->email = $data->email;
			$rdo->users[$data->suid]->color = $rdo->color++;
			$rdo->annotations[$data->suid] = array();
		}

		$this->session->set('collab-su-name', $data->name);
		$this->session->set('collab-su-email', $data->email);

		$m_collab->raw_data($rdo);
		$m_collab->save();

		$this->json(array('user' => 
			$rdo->users[$data->suid]));
	}

	public function list_su()
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($data->sessid);
		if (!$m_collab) return;

		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);

		foreach ($rdo->users as $suid => $user)
		{
			$user->count = 0;
			if (isset($rdo->annotations[$suid]))
				$user->count = count($rdo->annotations[$suid]);
			$user->approved = in_array($suid, (array) $rdo->approvals);
		}

		$this->json(array('users' => $rdo->users));
	}

	public function list_annotations()
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($data->sessid);
		if (!$m_collab) return;

		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);

		foreach ($rdo->annotations as $suid => $annotations)
		{
			$user = $rdo->users[$suid];
			foreach ($annotations as $k => $annotation)
			{
				$annotation->user = $user;
				$annotation->replies = count($annotation->conversation);
				// set to null to reduce data transfer
				$annotation->conversation = null;
			}
		}

		$this->json(array('annotations' => $rdo->annotations));
	}

	public function conversation()
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($data->sessid);
		if (!$m_collab) return;

		$id = (int) $data->id;
		$suid = $data->suid;
		$rdo = $m_collab->raw_data_object();
		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);
		if (!isset($rdo->annotations[$suid][$id])) show_404();
		$annotation = $rdo->annotations[$suid][$id];

		$pre = new stdClass();
		$pre->suid = $data->suid;
		$pre->color = $rdo->users[$suid]->color;
		$pre->name = $rdo->users[$suid]->name;
		$pre->message = $annotation->text;
		$pre->date = $annotation->date_created;

		array_unshift($annotation->conversation, $pre);

		if (!$rdo->timezone) $rdo->timezone = Date::$utc;
		foreach ($annotation->conversation as $entry)
			$entry->date = Date::out($entry->date, $rdo->timezone)->format('F jS - H:i T');

		if ($data->offset)
		{
			$annotation->conversation = 
				array_slice($annotation->conversation,
					$data->offset);
		}

		$this->json($annotation->conversation);
	}

	public function conversation_add()
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($data->sessid);
		if (!$m_collab) return;

		$id = (int) $id;
		$rdo = $m_collab->raw_data_object();
		$this->schedule_email($data->sessid, $rdo);

		$rdo->annotations = Raw_Data::from_auto($rdo->annotations);
		$rdo->users = Raw_Data::from_auto($rdo->users);
		if (!isset($rdo->users[$data->suid])) show_404();
		if (!isset($rdo->annotations[$data->annotation_suid][$data->annotation_id]))
			show_404();
		
		$entry = new stdClass();
		$entry->suid = $data->suid;
		$entry->color = $rdo->users[$data->suid]->color;
		$entry->name = (string) $rdo->users[$data->suid]->name;
		$entry->message = (string) $data->message;
		$entry->date = (string) Date::$now;

		$annotation = $rdo->annotations[$data->annotation_suid][$data->annotation_id];
		$annotation->conversation[] = $entry;
		$m_collab->raw_data($rdo);
		$m_collab->save();

		$this->json(true);
	}

	public function approve($sessid, $suid)
	{
		$data = Raw_Data::from_auto($this->input->post());
		$m_collab = Model_Content_Collab::find($sessid);
		if (!$m_collab) return;

		$rdo = $m_collab->raw_data_object();
		$this->schedule_email($sessid, $rdo);

		$rdo->approvals = (array) $rdo->approvals;
		$rdo->approvals[] = $suid;
		$rdo->approvals = array_unique($rdo->approvals);
		$m_collab->raw_data($rdo);
		$m_collab->save();

		$this->json(true);
	}

	protected function schedule_email($sessid, $rdo)
	{
		$url = sprintf('other/collab/email/%s', $sessid);
		$label = md5(sprintf('collab_email_%s', $sessid));
		$request = Model_Scheduled_Iella_Request::find('label', $label);
		if ($request) return;

		$request = new Scheduled_Iella_Request();
		$request->data->sessid = $sessid;
		$request->data->rdo = $rdo;
		$m_sir = $request->schedule($url, Date::minutes(5));
		$m_sir->label = $label;
		$m_sir->save();
	}

}