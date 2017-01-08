<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Subscribe_Controller extends Iella_Base {
	
	public function index()
	{
		if ($user = Model_User::find($this->iella_in->user_id))
		{
			// determine if we have already added this user
			// and use the move to list feature instead
			// * move_to_list will check anyway to make
			// sure that it still works when the user changes email
			$has_aweber = Model_User_AWeber_Status::find($user->id);
			$aweber = new AWeber_Process();

			try 
			{
				if ($has_aweber) 
					  $aweber->move_to_list($user);
				else $aweber->add_to_list($user);
			}
			catch (Exception $e)
			{
				// the api is unavailable or timed out so try again later
				if ($e instanceof AWeberAPIException && 
					 ($e->type == "ServiceUnavailableError" ||
					  $e->type == "APIUnreachableError"))
					return $this->reschedule($user->id);

				// in case of rate-limit errors, try again later
				if ($e instanceof AWeberAPIException &&
					 $e->type == "ForbiddenError" &&
					 str_contains($e->message, "Rate limit exceeded"))
					return $this->reschedule($user->id);

				// the email address is blocked so ignore
				if ($e instanceof AWeberAPIException && 
					 $e->type == "WebServiceError" &&
					 str_contains($e->message, "address blocked"))
					return;

				// the user is already subscribed
				if ($e instanceof AWeberAPIException && 
					 $e->type == "WebServiceError" &&
					 (str_contains($e->message, "already subscribed") ||
					  str_contains($e->message, "already belongs"))) 
					return;

				$alert = new Critical_Alert($e);
				$alert->send();
				return;
			}
			
			if (!$has_aweber)
			{
				$has_aweber = new Model_User_AWeber_Status();
				$has_aweber->user_id = $user->id;
				$has_aweber->save();
			}
		}
	}

	protected function reschedule($user_id, $when = null)
	{
		if (!$when) $when = Date::hours(1);
		$event = new Scheduled_Iella_Event();
		$event->data->user_id = $user_id;
		$event->schedule('aweber_subscribe', $when);
	}
	
	public function schedule()
	{
		if (isset($this->iella_in->user))
		     $user_id = $this->iella_in->user->id;
		else $user_id = $this->iella_in->user_id;
		$user = Model_User::find($user_id);
		if (!$user) return;
		
		// user has a non-free package after registration
		// so we can assume that we will invoke again 
		// for the item_order_plan event => exit now
		if ($this->iella_in->event->name === 'user_verify'
		 && $user->package > 0) return;
			
		// schedule subscribe event for next run
		// as some source events are run direct
		$event = new Scheduled_Iella_Event();
		$event->data->user_id = $user_id;
		$event->schedule('aweber_subscribe');
	}
	
}

?>