<?php

$writing_approve_modal = new Modal();
$writing_approve_modal->set_title('Approve and Schedule');
$modal_view = 'browse/view/partials/writing_approve_modal';
$modal_content = $ci->load->view($modal_view, null, true);
$writing_approve_modal->set_content($modal_content);
$modal_view = 'browse/view/partials/writing_approve_modal_footer';
$modal_content = $ci->load->view($modal_view, null, true);
$writing_approve_modal->set_footer($modal_content);
$ci->add_eob($writing_approve_modal->render(300, 247));
$vd->writing_approve_modal_id = $writing_approve_modal->id;

?>

<div class="alert alert-info with-btn">
	<strong>Review Required!</strong>
	Please review the press release and then approve or reject.
	<span class="pull-right">
		<form action="<?= $ci->newsroom->url() ?>manage/writing/reject/<?= $vd->writing_session->id ?>" method="post">
			<input type="hidden" name="comments" value="" />
			<a class="btn btn-mini btn-success no-custom" id="under-writing-approve"
				href="<?= $ci->newsroom->url() ?>manage/writing/approve/<?= $vd->writing_session->id ?>">Approve</a>
			<a class="btn btn-mini btn-success no-custom" id="under-writing-approve-edit"
				href="<?= $ci->newsroom->url() ?>manage/writing/approve_edit/<?= $vd->writing_session->id ?>">Edit</a>
			<button class="btn btn-mini btn-danger no-custom" id="under-writing-reject" type="button">Reject</button>
		</form>
	</span>
	<?php if ($vd->editor_comments): ?>
	<div class="under-writing-comments">
		<hr /><strong>Editor Comments:</strong>
		<?= $vd->esc($vd->editor_comments) ?>
	</div>
	<?php endif ?>
</div>

<?php

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap/js/bootstrap.min.js');
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>
<script>

$(function() {
	
	var approve_button = $("#under-writing-approve");
	var reject_button = $("#under-writing-reject");

	var approve_modal_id = <?= json_encode($this->vd->writing_approve_modal_id) ?>;
	var approve_modal = $(document.getElementById(approve_modal_id));

	approve_button.on("click", function(ev) {

		ev.preventDefault();
		approve_modal.modal("show");
		
	});
	
	reject_button.on("click", function() {
		
		bootbox.textarea("Rejection Reason", function(res) {
			if (res === null) return;
			var reject_form = reject_button.parents("form");
			reject_form.find("input[type=hidden]").val(res);
			reject_form.submit();
		});
		
	});
	
});

</script>