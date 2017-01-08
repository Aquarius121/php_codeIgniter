<button id="confirm-approve-button" class="btn btn-compact btn-success" type="button">Approve</button>
<button id="cancel-approve-button" class="btn btn-compact" type="button">Cancel</button>

<script>
	
$(function() {

	$("#confirm-approve-button").on("click", function() {
		$("#approve-confirm-form").submit();
	});

	$("#cancel-approve-button").on("click", function() {
		$(this).parents(".modal").modal("hide");
	});

});

</script>