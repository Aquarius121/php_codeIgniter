<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script>

// this could be executed before the 
// google analytics is loaded so we 
// must call the code on document load

$(function() {
	
	var send_event = window.ga === undefined ?
		__console : window.ga;
	
	// the tracking code should already 
	// be in this form but run it through anyway
	var normalize_item_name = function(name) {
		name = name.toLowerCase();
		name = name.replace(/[^a-z0-9]/g, "_");
		name = name.replace(/__+/g, "_");
		name = name.replace(/(^_|_$)/, "");
		return name;
	};
		
	<?php foreach ($vd->cart->items() as $c_item): ?>
	
		send_event("send", "event", "Store", "PurchaseNamedItem", 
			normalize_item_name(<?= json_encode($c_item->item()->tracking) ?>), 
			<?= (int) round($vd->cart->item_cost($c_item->token()) * $c_item->quantity) ?>);
		
		send_event("send", "event", "Store", "PurchaseItem", 
			<?= json_encode($c_item->item()->id) ?>, 
			<?= (int) round($vd->cart->item_cost($c_item->token()) * $c_item->quantity) ?>);
	
		<?php if ($c_item->item()->type == Model_Item::TYPE_PLAN): ?>
		
			<?php $plan = Model_Plan::find($c_item->item()->raw_data()->plan_id); ?>
			send_event("send", "event", "Store", "PurchasePlan", 
				normalize_item_name(<?= json_encode($plan->name) ?>), 
				<?= (int) round($vd->cart->item_cost($c_item->token()) * $c_item->quantity) ?>);
		
		<?php endif ?>
	
	<?php endforeach ?>
	
	send_event("send", "event", "Store", "Order", <?= json_encode($vd->order->id) ?>, 
		<?= (int) round($vd->order->price_total) ?>);
	
});

</script>