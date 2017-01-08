<div class="alert alert-danger clearfix" id="delete-confirm">
	<form method="post" action="manage/newsroom/contact/delete/<?= $vd->contact_id ?>">
		<button class="pull-left btn btn-danger" 
			type="submit" name="confirm" value="1">
			Confirm Delete
		</button>
		<strong>Caution!</strong> You are about to delete the contact shown below.
		<br />Be aware that this action cannot be reversed.
	</form>
</div>

<script>
	
$(function() {
	
	var fields = $();
	fields = fields.add(".container-fluid input");
	fields = fields.add(".container-fluid textarea");
	fields = fields.add(".container-fluid select");
	fields = fields.add(".container-fluid button");
	
	fields.attr("disabled", true);
	fields.addClass("disabled");
	
	$(".container .page-header")
		.parent().parent().remove();
		
	$("#locked_aside button")
		.parents("li").remove();
	
});
	
</script>