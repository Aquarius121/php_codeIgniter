<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Email_Controller extends Iella_Base {
	
	public function index()
	{
		$sessid = $this->iella_in->sessid;
		$m_collab = Model_Content_Collab::find($sessid);
		if (!$m_collab || $m_collab->is_deleted)
			return;

		$rdo_from = Raw_Data::from($this->iella_in->rdo);
		$rdo_to = $m_collab->raw_data_object();
		
		$diff = new stdClass();
		$diff->approvals = $this->diff_approvals($rdo_from, $rdo_to);
		$diff->annotations = $this->diff_annotations($rdo_from, $rdo_to);
		$diff->conversations = $this->diff_conversations($rdo_from, $rdo_to);

		$users = Raw_Data::from($rdo_to->users);
		foreach ($users as $suid => $user)
			$user->suid = $suid;

		$this->vd->rdo = $rdo_to;
		$this->vd->m_collab = $m_collab;
		$this->vd->content = Model_Detached_Content::from_object($rdo_to->detached_content);
		$this->vd->users = $users;
		$this->vd->annotations = Raw_Data::from($rdo_to->annotations);

		$owner = $m_collab->owner();
		$owner_suid = Model_Content_Collab::suid($owner);
		$user = $users[$owner_suid];
		$this->send_owner_mail($user, $diff);

		foreach ($users as $suid => $user)
			if ($suid !== $owner_suid) 
				$this->send_collaborator_mail($user, $diff);
	}

	protected function diff_approvals($rdo_from, $rdo_to)
	{
		$rdo_to_approvals = (array) $rdo_to->approvals;
		$rdo_from_approvals = (array) $rdo_from->approvals;
		$diff = array();

		foreach ($rdo_to_approvals as $suid)
			if (!in_array($suid, $rdo_from_approvals))
				$diff[] = $suid;

		return $diff;
	}

	protected function diff_annotations($rdo_from, $rdo_to)
	{
		$rdo_to_annotations = Raw_Data::from($rdo_to->annotations);
		$rdo_from_annotations = Raw_Data::from($rdo_from->annotations);
		$diff = array();

		foreach ($rdo_to_annotations as $suid => $annotations)
		{
			foreach ($annotations as $id => $annotation)
			{
				if (!$rdo_from_annotations[$suid] ||
					 !$rdo_from_annotations[$suid][$id])
				{
					$annotation->suid = $suid;
					$diff[] = $annotation;
				}
			}
		}

		usort($diff, function($a, $b) {
			return spaceship($a->date_created, 
				$b->date_created);
		});

		return $diff;
	}

	protected function diff_conversations($rdo_from, $rdo_to)
	{
		$rdo_to_annotations = Raw_Data::from($rdo_to->annotations);
		$rdo_from_annotations = Raw_Data::from($rdo_from->annotations);
		$diff = array();

		foreach ($rdo_to_annotations as $to_suid => $to_annotations)
		{
			foreach ($to_annotations as $to_id => $to_annotation)
			{
				if (!$rdo_from_annotations[$to_suid] ||
					 !$rdo_from_annotations[$to_suid][$to_id])
				{
					if (($count = count($to_annotation->conversation)))
					{
						$cdiff = new stdClass();
						$cdiff->suid = $to_suid;
						$cdiff->id = $to_id;
						$cdiff->count = $count;
						$cdiff->entries = $to_annotation->conversation;
						$cdiff->date = $cdiff->entries[0]->date;
						$diff[] = $cdiff;
					}

					continue;
				}

				$from_annotation = $rdo_from_annotations[$to_suid][$to_id];
				$count_from = count($from_annotation->conversation);
				$count_to = count($to_annotation->conversation);

				if ($count_to > $count_from)
				{
					$count = $count_to - $count_from;
					$cdiff = new stdClass();
					$cdiff->suid = $to_suid;
					$cdiff->id = $to_id;
					$cdiff->count = $count;
					$cdiff->entries = array_slice($to_annotation->conversation, -$count);
					$cdiff->date = $cdiff->entries[0]->date;
					$diff[] = $cdiff;
				}
			}
		}

		usort($diff, function($a, $b) {
			return spaceship($a->date, 
				$b->date);
		});

		return $diff;
	}

	protected function send_owner_mail($user, $diff)
	{
		$this->vd->diff = $diff;
		$this->vd->user = $user;
		$message = $this->load->view_return('api/iella/other/collab/owner');
		$this->send_mail($user, $message);
	}

	protected function send_collaborator_mail($user, $diff)
	{
		$diff = clone $diff;
		$processed_conversations = array();
		$processed_annotations = array();

		foreach ($diff->conversations as $k => $cdiff)
		{
			$found_slice = false;
			$entries = array_reverse($cdiff->entries);

			foreach ($entries as $k => $entry)
			{
				if ($entry->suid === $user->suid)
				{
					if ($k === 0)
					     $cdiff->entries = array();
					else $cdiff->entries = array_slice($cdiff->entries, -$k);
					$found_slice = true;
					break;
				}
			}

			if (!$found_slice)
				if ($cdiff->suid === $user->suid)
					$found_slice = true;

			if ($found_slice && count($cdiff->entries))
				$processed_conversations[] = $cdiff;
		}

		foreach ($diff->annotations as $k => $annotation)
			if ($annotation->suid !== $user->suid)
				$processed_annotations[] = $annotation;

		$diff->conversations = $processed_conversations;
		$diff->annotations = $processed_annotations;
		$this->vd->diff = $diff;
		$this->vd->user = $user;
		$message = $this->load->view_return('api/iella/other/collab/collaborator');
		$this->send_mail($user, $message);
	}

	protected function send_mail($user, $message)
	{
		$email = new Email();
		$email->set_to_email($user->email);
		$email->set_from_email($this->conf('no_reply_email'));
		$email->set_to_name($user->name);
		$email->set_from_name('Newswire');
		$email->set_subject('Newswire Collaboration: Update');
		$email->set_message($message);
		$email->enable_html();

		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
}