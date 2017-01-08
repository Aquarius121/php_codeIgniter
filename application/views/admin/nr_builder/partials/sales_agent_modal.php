<div class="row-fluid">
	<div class="alert alert-danger required-error sales-agent-required-error hidden">
		<strong>Required!</strong> The <strong>Sales Agent</strong> field must have a value.
	</div>
	<div class="span12 placeholder-container">
		<select name="select_sales_agent" id="select-sales-agent"
			class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder"
			data-live-search="true">
			<option value="0" class="status-false" selected>None</option>
			<?php foreach ($vd->sales_agents as $sales_agent): ?>
				<option value="<?= $sales_agent->id ?>"><?= $vd->esc($sales_agent->name()) ?></option>
			<?php endforeach ?>
		</select>
		<strong class="placeholder">Sales Agent</strong>
	</div>
</div>

<script>
$(function(){
	var sales_agent_select = $("#select-sales-agent");
	sales_agent_select.on_load_select({
		size: 5
	});

	sales_agent_select.on('change', function(){
		var sales_agent_error = $(".sales-agent-required-error");
		sales_agent_error.addClass('hidden');
	});


})
</script>