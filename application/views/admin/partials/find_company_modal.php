<div class="row-fluid">
	<div class="span12 relative">
		<input type="text" required name="email"
			class="span12 in-text has-placeholder"
			placeholder="Account Email" id="find-company-email" />
		<strong class="placeholder">Account Email</strong>
	</div>
</div>

<div class="row-fluid">
	<div class="span10 relative">
		<input type="text" required name="company_name"
			class="span12 in-text has-placeholder"
			placeholder="Company Name" id="find-company-company" />
		<strong class="placeholder">Company Name</strong>
	</div>
	<div class="span2 relative">
		<button type="button" type="button" id="find-company-find-button"
			class="span12 btn has-placeholder">Find</button>
		<strong class="placeholder"></strong>
	</div>
</div>

<div id="find-company-results" class="transfer-results find-results hidden"></div>

<script>
	
$(function() {

	var find_results = $("#find-company-results");
	var find_button = $("#find-company-find-button");
	var find_email = $("#find-company-email");
	var find_company = $("#find-company-company");
	var find_confirm = $(".confirm-find-button");

	find_button.on("click", function() {

		find_confirm.prop("disabled", true);
		find_results.addClass("loader");
		find_results.removeClass("hidden");
		find_results.empty();

		var data = {
			"find_company": find_company.val(),
			"find_email": find_email.val()
		};

		$.post("admin/find/find_company", data, function(res) {
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

	find_company.on("keypress", function(ev) {
		if (ev.which == 13) find_button.click();
	});

	// load recent results 
	find_button.trigger("click");

});

</script>