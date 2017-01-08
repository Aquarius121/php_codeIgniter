<input type="hidden" name="sales_agent_id" id="sales-agent-id" value="0">
<input type="hidden" name="export_selected" id="export-selected" value="0">
<input type="hidden" name="export_not_exported" id="export-not-exported" value="0">
<input type="hidden" name="export_exported" id="export-exported" value="0">

<div id="select-agant-modal-span"></div>
<div class="pull-right">
	<strong>Export: </strong>
	<button type="button" class="btn btn-success btn-export" 
		data-id="#export-selected">Selected</button>

	<button type="button" class="btn btn-success btn-export" 
		data-id="#export-not-exported">All Not Yet Exported </button>

	<button type="button" class="btn btn-success btn-export" 
		data-id="#export-exported">All Already Exported</button>
</div>



<script>
$(function(){
	var sales_agent_modal_id = <?= json_encode($vd->sales_agent_modal_id) ?>;
	var export_btn = $(".btn-export");
	export_btn.on("click", function() {		
		var sales_agent_modal = $(document.getElementById(sales_agent_modal_id));
		var data_id = $(this).data('id');
		$("#export-selected").val(0);
		$("#export-not-exported").val(0);
		$("#export-exported").val(0);
		$(data_id).val('1');
		sales_agent_modal.modal("show");
		return false;
	});
});
</script>