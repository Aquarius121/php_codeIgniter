<div class="row-fluid">
	<div class="span10 relative">
		<input type="text" required name="email"
			class="span12 in-text has-placeholder"
			placeholder="Account Email" id="find-user-email" />
		<strong class="placeholder">Account Email</strong>
	</div>
	<div class="span2 relative">
		<button type="button" type="button" id="find-user-find-button"
			class="span12 btn has-placeholder">Find</button>
		<strong class="placeholder"></strong>
	</div>
</div>

<div id="find-user-results" class="transfer-results find-results compact hidden"></div>

<script>
	
$(function() {

	var find_results = $("#find-user-results");
	var find_button = $("#find-user-find-button");
	var find_email = $("#find-user-email");
	var find_confirm = $(".confirm-find-button");

	find_button.on("click", function() {

		find_confirm.prop("disabled", true);
		find_results.addClass("loader");
		find_results.removeClass("hidden");
		find_results.empty();

		var data = {
			"find_email": find_email.val()
		};

		$.post("admin/find/find_user", data, function(res) {
			find_results.removeClass("loader");
			find_results.html(res);
		});

	});

	find_results.on("change", "input", function() {
		find_confirm.prop("disabled", false);
	});

	find_results.on("click", ".transfer-result", function() {
		var radio = $(this).find("input");
		radio.prop("checked", true)
		radio.trigger("change");
	});

	find_email.on("keypress", function(ev) {
		if (ev.which == 13) find_button.click();
	});

	// load recent results 
	find_button.trigger("click");

});

</script>