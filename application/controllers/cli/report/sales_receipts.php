<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Sales_Receipts_Controller extends CLI_Base {

	public function index($date_start = null, $date_end = null)
	{
		if (!$date_start) $date_start = (string) Date::days(-30);
		if (!$date_end) $date_end = (string) Date::$now;

		$dts = escape_and_quote(Date::utc($date_start));
		$dte = escape_and_quote(Date::utc($date_end));

		Model_Item::enable_cache();

		$t_prefixes = Model_Transaction::__prefixes('t');
		$u_prefixes = Model_User::__prefixes('u');
		$b_prefixes = Model_Billing::__prefixes('b');

		$sql = "SELECT o.*, {$t_prefixes}, {$u_prefixes}, {$b_prefixes} from co_order o 
			inner join co_transaction t on t.order_id = o.id and t.is_renewal = 0 
			inner join nr_user u on o.user_id = u.id
			left join co_billing b on b.user_id = u.id
			where o.date_created >= {$dts}
			and o.date_created <= {$dte}
			order by o.date_created asc";

		$dbr = $this->db->query($sql);
		$orders = Model_Order::from_db_all($dbr);
		$csv = new CSV_Writer('php://stdout');

		$csv->write(array(
			'Date', 
			'Order',
			'Account',
			'Cart',
			'Total', 
			'Name',
			'Phone',
		));

		foreach ($orders as $order)
		{
			$cart = array();
			$vcart = $order->transaction->raw_data_read('virtual_cart');
			foreach ($vcart->items as $token => $item)
				$cart[] = sprintf('[%d] %s', $item->quantity, 
					Model_Item::find($item->item_id)->name);

			$csv->write(array(
				Date::utc($order->date_created)->__toString(),
				$order->id,
				$order->user->email,
				implode(PHP_EOL, $cart),
				sprintf('%0.2f', $order->price_total),
				$order->user->name(),
				$order->billing
					? $order->billing->phone
					: null,
			));
		}

		$csv->close();
	}

}

?>