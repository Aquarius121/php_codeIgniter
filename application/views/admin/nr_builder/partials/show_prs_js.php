<script>
$(function() {

	$("a.show-prs").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		var modal_id = "<?= $vd->prs_modal_id ?>";
		var content_url = "admin/nr_builder/<?= $vd->nr_source ?>/pr_links/"+id;

		var modal = $("#" + modal_id);
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});
	});

})
</script>