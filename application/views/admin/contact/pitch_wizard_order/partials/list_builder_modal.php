<form action="admin/contact/pitch_wizard_order/save_list" method="post" id="builder-select-form">
	<input type="hidden" id="builder-for-order-id" name="pitch_order_id" />
	<input type="hidden" name="is_reupload" value="<?= @$vd->is_reupload ?>" />
	<div class="row-fluid">
		<div class="span10 relative">
			<input type="text" required name="list_name"
				class="span12 in-text has-placeholder"
				placeholder="List Name" id="builder-list-name" />
			<strong class="placeholder">List Name</strong>
		</div>
		<div class="span2 relative">
			<button type="button" type="button" id="builder-find-button"
				class="span12 btn has-placeholder">Find</button>
			<strong class="placeholder"></strong>
		</div>
	</div>

	<div id="builder-results" class="builder-results hidden">
	</div>

	<script>
		
	$(function() {

		var builder_results = $("#builder-results");
		var find_button = $("#builder-find-button");
		var builder_name = $("#builder-list-name");
		var builder_confirm = $("#confirm-builder-button");

		find_button.on("click", function() {

			builder_confirm.prop("disabled", true);
			builder_results.addClass("loader");
			builder_results.removeClass("hidden");
			builder_results.empty();

			var data = {
				'builder_name': builder_name.val()
			};

			$.post('admin/contact/pitch_wizard_order/find_builder_list', data, function(res) {
				builder_results.removeClass("loader");
				builder_results.html(res);
			});

		});

		builder_results.on("change", "input", function() {
			builder_confirm.prop("disabled", false);
		});

		builder_results.on("click", ".builder-result", function() {
			var radio = $(this).find("input");
			radio.prop("checked", true)
			radio.trigger("change");
		});

		builder_name.on("keypress", function(ev) {
			if (ev.which == 13) find_button.click();
		});

		builder_confirm.on("click", function() {
			if (builder_confirm.is(":disabled")) return;
			$("#builder-select-form").submit();
		});

	});

	</script>
</form>