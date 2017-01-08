<div class="alert alert-error clearfix" id="delete-confirm">
	<form method="post" action="admin/store/plan/delete/<?= $vd->plan_id ?>">
		<button class="pull-left btn btn-danger" 
			type="submit" name="confirm" value="1">
			Confirm Delete
		</button>
		<strong>Caution!</strong> You are about to delete the plan shown below.
		<br />This will prevent the plan from being ordered.
	</form>
</div>

<script>

$(function() {
	
	var fields = $();
	fields = fields.add(".content input");
	fields = fields.add(".content textarea");
	fields = fields.add(".content select");
	fields = fields.add(".content button");
	
	fields.attr("disabled", true);
	fields.addClass("disabled");
	
	$(".container .page-header")
		.parent().parent().remove();
		
	$("#locked_aside button")
		.parents("li").remove();
	
});

	
</script>