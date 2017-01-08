<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Transaction_Report_Trait {
	
	protected function transaction_report_csv($date_start = null, $date_end = null)
	{
		if ($date_start === null) $date_start = Date::days(-1);
		if ($date_end === null)	$date_end = Date::$now;
		
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $date_end->format(Date::FORMAT_MYSQL);
		
		$file = File_Util::buffer_file();
		$csv = new CSV_Writer($file);
		
		$csv->write(array(
			'transaction_date',
			'transaction_id',
			'user_email',
			'item_name',
			'item_price',
			'item_quantity',
			'line_total_cost',
			'transaction_amount',
			'transaction_provider',
			'provider_transcation_id',
			'is_renewal',
		));
		
		$sql = "SELECT t.id, t.order_id, t.user_id, 
			t.date_created, t.price, t.gateway, 
			t.raw_data, t.virtual_cart, u.email,
			(tx.id IS NULL AND t.order_id IS NOT NULL) 
				AS is_renewal
			FROM co_transaction t INNER JOIN nr_user u 
			ON t.user_id = u.id 
			AND t.date_created >= '{$date_start_str}' 
			AND t.date_created <  '{$date_end_str}'
			LEFT JOIN (
				SELECT id FROM co_transaction
				GROUP BY order_id
				ORDER BY date_created ASC
			) tx ON tx.id = t.id
			ORDER BY t.date_created ASC";
		
		$db_result = $this->db->query($sql);
		$transactions = Model_Transaction::from_db_all($db_result);
		
		Model_Item::enable_cache();
		
		foreach ($transactions as $transaction)
		{
			$cart = Virtual_Cart::instance();
			$cart->unserialize($transaction->virtual_cart);
			$cart->allow_expired_coupon();
			$cart->allow_deleted_coupon();
			
			foreach ($cart->items() as $item)
			{
				$item_cost_in_cart_context = 
					$cart->item_cost($item->item_id);
				
				$csv->write(array(
					$transaction->date_created,
					$transaction->id,
					$transaction->email,
					$item->item()->name,
					$item_cost_in_cart_context,
					$item->quantity,
					$item->quantity * $item_cost_in_cart_context,
					number_format($transaction->price, 2),
					$transaction->gateway,
					$transaction->gateway_transaction_id(),
					$transaction->is_renewal,
				));
			}
		}
		
		Model_Item::disable_cache();
		
		$csv->close();
		return $file;
	}
	
}

?>