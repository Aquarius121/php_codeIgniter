<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Top_Clients_Controller extends CLI_Base {

	public function index($date_start = null, $date_end = null)
	{
		if (!$date_start) $date_start = Date::years(-1);
		if (!$date_end) $date_end = Date::$now;

		$dts = escape_and_quote(Date::utc($date_start));
		$dte = escape_and_quote(Date::utc($date_end));

		$b_prefixes = Model_Billing::__prefixes('b');

		$sql = "SELECT * from (
				select u.*, SUM(o.price_total) AS price_total, {$b_prefixes}
				from co_order o 
				inner join nr_user u on o.user_id = u.id
				left join co_billing b on b.user_id = u.id
				where o.date_created >= {$dts}
				and o.date_created <= {$dte}
				group by u.id
			) tc 
			ORDER BY tc.price_total DESC
			LIMIT 100";

		$dbr = $this->db->query($sql);
		$users = Model_User::from_db_all($dbr);
		$csv = new CSV_Writer('php://stdout');
		
		$csv->write(array(
			'Rank', 
			'Total', 
			'Account', 
			'Name',
			'Address',
			'Phone',
		));

		foreach ($users as $k => $user)
		{
			$csv->write(array(
				$k + 1,
				sprintf('$%0.2f', $user->price_total),
				$user->email,
				$user->name(),
				$user->billing ? 
				trim(str_replace(PHP_EOL.PHP_EOL, PHP_EOL, implode(PHP_EOL, array(
					$user->billing->company_name,
					$user->billing->street_address,
					$user->billing->extended_address,
					$user->billing->locality,
					$user->billing->region,
					$user->billing->zip,
					@Model_Country::find($user->billing->country_id)->name,
				)))) : null,
				$user->billing
					? $user->billing->phone
					: null,
			));
		}

		$csv->close();
	}

}

?>