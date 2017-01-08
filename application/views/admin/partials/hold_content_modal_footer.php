<button id="confirm-hold-button" class="btn btn-compact btn-primary" type="button" disabled>Hold</button>
<button id="remove-hold-button" class="btn btn-compact btn-secondary" type="button">Remove</button>
<button id="cancel-hold-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-hold-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

});

</script>