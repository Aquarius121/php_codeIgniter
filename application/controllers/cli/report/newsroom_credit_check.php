<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Newsroom_Credit_Check_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index()
	{
		$offset = 0;
		$csv = new CSV_Writer('php://stdout');
		$csv->write(array(
			'email',
			'newsrooms',
			'credits',
		));

		while ($offset >= 0)
		{
			$sql = "SELECT count(*) AS count, n.user_id 
				FROM nr_newsroom n 
				WHERE n.is_active = 1
				AND n.user_id != 1
				GROUP BY n.user_id
				LIMIT {$offset}, 100";

			$xs = Model::from_sql_all($sql);
			if (!$xs) break;
			$offset += 100;

			foreach ($xs as $x)
			{
				if (!($user = Model_User::find($x->user_id))) 
				{
					$this->trace_failure('no user', $x->user_id);
					continue;
				}

				$credits = $user->newsroom_credits_total();

				if ($credits < $x->count)
				{
					$csv->write(array(
						$user->email,
						$x->count,
						$credits,
					));
				}
			}
		}
	}

}