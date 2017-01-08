<!-- hidden for now -->
<div class="row-fluid dnone">
	<div class="span10 relative">
		<input type="text" required name="name"
			class="span12 in-text has-placeholder"
			placeholder="Site Name" id="find-site-name" />
		<strong class="placeholder">Site / POC</strong>
	</div>
	<div class="span2 relative">
		<button type="button" type="button" id="find-site-find-button"
			class="span12 btn has-placeholder">Find</button>
		<strong class="placeholder"></strong>
	</div>
</div>

<div id="find-site-results" class="transfer-results find-results compact hidden"></div>

<script>
	
$(function() {

	var find_results = $("#find-site-results");
	var find_button = $("#find-site-find-button");
	var find_name = $("#find-site-name");
	var find_confirm = $(".confirm-find-button");

	find_button.on("click", function() {

		find_confirm.prop("disabled", true);
		find_results.addClass("loader");
		find_results.removeClass("hidden");
		find_results.empty();

		var data = {
			"find_name": find_name.val()
		};

		$.post("admin/find/find_site", data, function(res) {
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

	find_name.on("keypress", function(ev) {
		if (ev.which == 13) find_button.click();
	});

	// load recent results 
	find_button.trigger("click");

});

</script>