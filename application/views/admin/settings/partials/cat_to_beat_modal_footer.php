<button id="confirm-add-button" class="btn btn-compact btn-primary" type="button">Add</button>
<button id="cancel-add-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-add-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

	$("#confirm-add-button").on("click", function() {
		$(this).parents(".modal").find("form").submit();
	});

});

</script>