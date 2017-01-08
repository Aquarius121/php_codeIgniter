<button type="button" id="list-options-button"
	class="btn btn-small btn-default">
	Options
</button>

<script>

$(function() {

	var modal = $("#md-options-modal");
	var button = $("#list-options-button");

	button.on("click", function() {
		modal.modal("show");
	});

});

</script>