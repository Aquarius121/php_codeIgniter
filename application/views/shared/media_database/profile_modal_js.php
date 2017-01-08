<?= $ci->load->view('shared/partials/twitter-widget-library') ?>

<script>

$(function() {
	
	var modal = $("#md-profile-modal");

	$(document).on("click", ".md-profile-activator", function(ev) {
		
		ev.preventDefault();
		var contact_id = $(this).parents(".md-contact-result").data("id");
		var content_container = modal.find(".modal-content");
		content_container.addClass("loader");
		content_container.empty();
		modal.modal("show");

		$.get("<?= $ci->uri->segment(1) ?>/contact/media_database/fetch_full_profile", { contact_id: contact_id }, function(res) {
			content_container.removeClass("loader");
			content_container.html(res);
		});

		return false;

	});

});

</script>
