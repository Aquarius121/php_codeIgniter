<button id="confirm-export-button" class="btn btn-compact btn-primary" type="submit">Export</button>
<button id="cancel-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

	$("#confirm-export-button").on("click", function() {
		var sales_agent_id = $("#sales-agent-id");
		var sales_agent_select = $("#select-sales-agent");
		var sales_agent_error = $(".sales-agent-required-error");
		var sales_agent_select_val = sales_agent_select.val();

		sales_agent_error.addClass('hidden');

		if (sales_agent_select_val == "0")
		{
			sales_agent_error.removeClass('hidden');
			return false;
		}

		sales_agent_id.val(sales_agent_select_val);
		$("#selectable-form").submit();
	});

});

</script>