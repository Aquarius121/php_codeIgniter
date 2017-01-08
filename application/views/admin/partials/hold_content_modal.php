<form action="admin/publish/hold" method="post" class="row-fluid">
	<input type="hidden" name="content" value="" id="hold-content-id" />
	<textarea class="span12" name="comments" style="min-height: 285px"
		id="hold-comments-ta" placeholder="Comments"></textarea>
	<input type="hidden" name="remove" value="1" disabled id="remove-hold" />
</form>

<script>
	
$(function() {

	var confirm_button = $("#confirm-hold-button");
	var comments = $("#hold-comments-ta");

	var remove_button = $("#remove-hold-button");
	var remove = $("#remove-hold");

	var check_for_comments = function() {
		confirm_button.prop("disabled", !comments.val());
	};

	comments.on("change", check_for_comments);
	comments.on("keyup", check_for_comments);
	check_for_comments();

	confirm_button.on("click", function() {
		comments.parents("form").submit();
	});

	remove_button.on("click", function() {
		remove.prop("disabled", false);
		remove.parents("form").submit();
	});

});

</script>