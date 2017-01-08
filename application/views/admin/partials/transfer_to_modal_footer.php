<button id="confirm-transfer-button" class="btn btn-compact btn-primary" type="button" disabled>Transfer</button>
<button id="cancel-transfer-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#cancel-transfer-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

});

</script>