<div class="alert alert-danger clearfix" id="delete-confirm">
	<form method="post" action="manage/contact/list/delete/<?= $vd->contact_list_id ?>">
		<button class="pull-left btn btn-danger" 
			type="submit" name="confirm" value="1">
			Confirm Delete
		</button>
		<strong>Caution!</strong> You are about to delete the contact list shown below.
		<br />Be aware that this action cannot be reversed. 
	</form>
</div>

<script>
	
$(function() {
	
	var fields = $();	
	fields = fields.add(".container-fluid input:not([type=checkbox])");
	fields = fields.add(".container-fluid select");
	fields = fields.add(".container-fluid button:not(.checkbox)");
	fields = fields.add(".container-fluid a");
	fields.remove();
	
	$(".container-fluid input[type=checkbox]")
		.attr("disabled", true)
		.addClass("disabled");
		
	$("#selectable-results th .checkbox-container").remove();
	$("#selectable-results th:first-child").remove();
	$("#selectable-results td:first-child").remove();
	$(".container .page-header").parent().parent().remove();
	$("#list-name-editable a").remove();
	$("#selectable-controls").remove();	
	$(".chunkination").empty();
	
});
	
</script>