<?php if ($ci->is_common_host) return; ?>

<a id="subscribe-link" class="btn btn-outline subscription-link no-custom">Follow +</a>
<script>
	
$(function() {

	var follow_modal_id = <?= json_encode($vd->follow_modal_id) ?>;
	var follow_modal = $(document.getElementById(follow_modal_id));

	$("#subscribe-link").on("click", function() {
		follow_modal.modal("show");
	});

});

</script>