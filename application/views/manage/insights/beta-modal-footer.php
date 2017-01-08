<button id="beta-continue-button" class="btn btn-primary" type="button">Continue</button>
<script>
	
$(function() {

	var button = $("#beta-continue-button");
	var modal = $("#insights-beta-modal");

	button.on("click", function() {
		modal.modal("hide");
	});

});

</script>