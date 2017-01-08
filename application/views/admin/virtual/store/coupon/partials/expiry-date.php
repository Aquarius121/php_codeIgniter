<div class="relative">
	<input type="text" name="date_expires" required
		id="date_expires" class="span12 in-text datepicker" 
		value="<?= @$vd->coupon->date_expires ?>"	
		placeholder="Expiry Date" />
</div>
<script>

$(function() {

	var nowTemp = new Date();
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
		nowTemp.getDate(), 0, 0, 0, 0);
	
	var date_expires = $("#date_expires");
	
	date_expires.datetimepicker({
		startDate: now,
		autoclose: true,
		todayBtn: true,
		minView: 1,
	});
	
	date_expires.on("changeDate", function(ev) {
		ev.date.setMinutes(0);
	});
	
});

</script>
<?php 

	$render_basic = $ci->is_development();
	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datetimepicker.css');	
	echo $loader->render($render_basic);

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datetimepicker.js');
	echo $loader->render($render_basic);

?>