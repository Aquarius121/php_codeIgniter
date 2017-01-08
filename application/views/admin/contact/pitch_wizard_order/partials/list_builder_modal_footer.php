<button id="confirm-builder-button" class="btn btn-compact btn-primary" type="button" disabled>Select</button>
<button id="cancel-builder-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-builder-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

});

</script>