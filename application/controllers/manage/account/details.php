<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Details_Controller extends Manage_Base {
	
	protected $ssl_required = true;
	public $title = 'Account Details';
	
	public function index()
	{
		$user = $this->vd->user = Auth::user();
		if ($this->input->post('save'))
			$this->save();
		
		$mail_blocks = Model_User_Mail_Blocks::find_user($user->id);
		$this->vd->mail_blocks = $mail_blocks;

		$this->load->view('manage/header');
		$this->load->view('manage/account/details');
		$this->load->view('manage/footer');
	}
	
	public function save()
	{
		$user = Auth::user();		
		$password = $this->input->post('password');
		$assume_account_owner = Auth::is_admin_online() || 
			$this->session->get('assume_account_owner');

		if ($assume_account_owner || Model_User::authenticate($user->email, $password))
		{
			$this->session->delete('assume_account_owner');

			$save_success = true;
			$new_password = $this->input->post('new_password');
			$new_password_confirm = $this->input->post('new_password_confirm');
			$first_name = $this->input->post('first_name');
			$last_name = $this->input->post('last_name');
			$email = $this->input->post('email');
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			
			if ($user->email != $email)
			{
				if (Model_User::find_email($email))
				{
					// load feedback message for the user
					$feedback = new Feedback('error', 'Error!', 'An account already exists with that email address.');
					$this->add_feedback($feedback);
					$save_success = false;
				}
				else
				{
					$user->email = $email;
				}
			}
			
			if ($new_password)
			{
				if (strlen($new_password) < 6)
				{
					// load feedback message for the user
					$feedback = new Feedback('error', 'Error!', 'The new password was too short.');
					$this->add_feedback($feedback);
					$save_success = false;
				}
				else if ($new_password !== $new_password_confirm)
				{
					// load feedback message for the user
					$feedback = new Feedback('error', 'Error!', 'The new password values did not match.');
					$this->add_feedback($feedback);
					$save_success = false;
				}
				else
				{
					$user->set_password($new_password);
				}
			}

			$mail_blocks = Model_User_Mail_Blocks::find_user($user->id);
			foreach (Model_User_Mail_Blocks::collection() as $block)
			{
				$is_allowed = (bool) $this->input->post($block);
				if ($is_allowed)
				     $mail_blocks->remove($block);
				else $mail_blocks->add($block);
			}

			$mail_blocks->save();
			$user->save();
			
			if ($save_success)
			{
				// load feedback message for the user
				$feedback = new Feedback('success', 'Saved!', 'Your account has been updated.');
				$this->add_feedback($feedback);
			}
		}
		else
		{
			// load feedback message for the user
			$feedback = new Feedback('error', 'Error!', 'The password was incorrect.');
			$this->add_feedback($feedback);
		}

		$this->redirect('manage/account/details');
	}
	
}

?>