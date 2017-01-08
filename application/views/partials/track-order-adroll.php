<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script type="text/javascript">

	// depends on the inclusion of track-adroll already in the page
	adroll_conversion_value = <?= json_encode(round($vd->cart->total_with_discount(), 2)) ?>;
	adroll_currency = "USD";
	
</script>