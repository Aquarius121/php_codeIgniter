<div class="add-filter-modal"><?= $vd->admin_add_filter_modal->render(600, 400) ?></div>

<script>

$(function() {

	var modal_id = <?= json_encode($vd->admin_add_filter_modal->id) ?>;
	var modal = $(document.getElementById(modal_id));
	var button = $("#admin-add-filter-button");

	button.on("click", function(ev) {
		modal.modal("show");
	});

});

</script>