<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<?php

$user = $vd->user;
$cart = $vd->cart;

foreach ($vd->cart->items() as $c_token => $c_item):

	$conversion = Model_Adword_Conversion::find($c_item->item_id);
	if (!$conversion) $conversion = Model_Adword_Conversion::defaults();
	$conversion_data = $conversion->raw_data();

	$conversion_currency = 'USD';
	$conversion_id = $conversion_data->id;
	$conversion_value = $cart->item_cost($c_token) * $c_item->quantity;
	$conversion_value = number_format($conversion_value, 2);	
	$conversion_label = $conversion_data->label;

	// construct query string for noscript
	$conversion_qs = http_build_query(array(
		'value' => $conversion_value, 
		'currency_code' => $conversion_currency,
		'label' => $conversion_label,
		'script' => 0,
	));

	?>
	
	<img src="//www.googleadservices.com/pagead/conversion/<?= $conversion_id ?>/?<?= $vd->esc($conversion_qs) ?>"
		height="1" width="1" style="border: none; position: absolute; left: -10000px;" />

<?php endforeach ?>