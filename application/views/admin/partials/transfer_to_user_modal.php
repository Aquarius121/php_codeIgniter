<div class="row-fluid">
	<div class="span10 relative">
		<input type="text" required name="email"
			class="span12 in-text has-placeholder"
			placeholder="Account Email" id="transfer-email" />
		<strong class="placeholder">Account Email</strong>
	</div>
	<div class="span2 relative">
		<button type="button" type="button" id="transfer-find-button"
			class="span12 btn has-placeholder">Find</button>
		<strong class="placeholder"></strong>
	</div>
</div>

<div id="transfer-results" class="transfer-results compact hidden"></div>

<script>
	
$(function() {

	var transfer_results = $("#transfer-results");
	var find_button = $("#transfer-find-button");
	var transfer_email = $("#transfer-email");
	var transfer_confirm = $("#confirm-transfer-button");

	find_button.on("click", function() {

		transfer_confirm.prop("disabled", true);
		transfer_results.addClass("loader");
		transfer_results.removeClass("hidden");
		transfer_results.empty();

		var data = {
			"filter": <?= json_encode($vd->transfer_to_user_filter) ?>,
			"find_email": transfer_email.val(),
		};

		$.post("admin/transfer/find_user", data, function(res) {
			transfer_results.removeClass("loader");
			transfer_results.html(res);
		});

	});

	transfer_results.on("change", "input", function() {
		transfer_confirm.prop("disabled", false);
	});

	transfer_results.on("click", ".transfer-result", function() {
		var radio = $(this).find("input");
		radio.prop("checked", true)
		radio.trigger("change");
	});

	transfer_email.on("keypress", function(ev) {
		if (ev.which == 13) find_button.click();
	});

	// load recent results 
	find_button.trigger("click");

});

</script>