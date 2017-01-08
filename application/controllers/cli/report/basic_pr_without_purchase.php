<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Basic_PR_Without_Purchase_Controller extends CLI_Base {

	public function index()
	{
		$dt_orders_from = escape_and_quote(Date::months(-7));
		$dt_content_from = escape_and_quote(Date::months(-6));

		$sql = "SELECT u.*, b.phone, c.date_created as date_content_created 
			from nr_content c
			inner join nr_company cm on c.company_id = cm.id
			inner join nr_user u on cm.user_id = u.id
			left join co_order o on o.user_id = u.id
				and o.date_created >= {$dt_orders_from}
			left join co_billing b on b.user_id = u.id
			where c.date_created >= {$dt_content_from}
			and c.is_premium = 0 
			and c.is_published = 1
			and o.id is null
			group by u.id
			order by c.date_created desc";

		$dbr = $this->db->query($sql);
		$users = Model_User::from_db_all($dbr);
		$csv = new CSV_Writer('php://stdout');

		$csv->write(array(
			'Date', 
			'Month', 
			'Account', 
			'Name',
			'Phone',
		));

		foreach ($users as $user)
		{
			$csv->write(array(
				Date::utc($user->date_content_created)->__toString(),
				Date::utc($user->date_content_created)->format('F'),
				$user->email,
				$user->name(),
				$user->phone,
			));
		}

		$csv->close();
	}

}

?>