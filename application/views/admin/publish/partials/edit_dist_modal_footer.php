<button id="cancel-set-dist-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-set-dist-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

});

</script>