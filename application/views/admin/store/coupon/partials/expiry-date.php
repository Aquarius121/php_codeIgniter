<div class="relative">
	<input type="text" name="date_expires" required
		id="date_expires"
		class="span12 in-text datepicker" 
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