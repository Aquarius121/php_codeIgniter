<?php if (Auth::is_admin_online() && $vd->duplicates_found): ?>
<div class="alert alert-info">
	<div><strong class="alert-title">
		Duplicate Content</strong></div>
	<?php $max = 0; foreach ($vd->duplicates_found as $duplicate) ?>
		<?php $max = max($max, strlen($duplicate->id)); ?>
	<?php foreach ($vd->duplicates_found as $duplicate): ?>
		<div><a href="<?= $ci->website_url($duplicate->url_id()) ?>">
			<strong>#<?= sprintf("%0{$max}d", $duplicate->id) ?></strong></a>
			&nbsp;&nbsp;<?= $vd->esc($duplicate->title) ?></div>
	<?php endforeach ?>
</div>
<?php endif ?>

<?php if (Auth::is_admin_online() && $vd->hold_comments): ?>
<div class="alert alert-alternative-2">
	<div><strong class="alert-title">Hold Comments</strong></div>
	<?= $vd->esc($vd->hold_comments) ?>
</div>
<?php endif ?>

<div class="alert alert-error with-btn">
	<?php if ($vd->m_content->is_under_review): ?>
	<strong>Not Published!</strong> 	
	This content is being reviewed by our staff. 
	<?php if (Auth::is_admin_online()): ?>
	<span class="pull-right">
		<a class="btn btn-mini btn-success no-custom" 
			href="<?= $ci->common()->url() ?>admin/publish/approve/<?= $vd->m_content->id ?>/view">Approve</a>
		<a data-id="<?= $vd->m_content->id ?>" class="btn btn-mini btn-alternative-2 no-custom" id="hold-button"
			href="#" data-comments="<?= $vd->esc($vd->hold_comments) ?>">Hold</a>
		<a class="btn btn-mini btn-danger no-custom" 
			href="<?= $ci->common()->url() ?>admin/publish/reject/<?= $vd->m_content->id ?>/view">Reject</a>
	</span>
	<?php endif ?>
	<?php elseif (($vd->m_content->type != Model_Content::TYPE_PR || $vd->m_content->is_approved) 
	              && new DateTime($vd->m_content->date_publish) < Date::$now
	              && !$vd->m_content->is_draft): ?>
	<strong>Scheduled!</strong> 	
	This content will be released within a few seconds.
	<?php elseif (!$vd->m_content->is_draft): ?>
	<strong>Scheduled!</strong> 	
	This content is scheduled for release. 
	<?php elseif ($vd->m_content->is_rejected): ?>
	<strong>Rejected!</strong>
	This content has been rejected by our staff.
	<?php else: ?>
	<strong>Not Published!</strong>
	This content is only visible to the content creator. 
	<?php endif ?>
</div>

<script>
	
$(function() {

	var hold_content_modal_id = <?= json_encode($vd->hold_content_modal_id) ?>;
	if (!hold_content_modal_id) return;

	var hold_content_modal = $(document.getElementById(hold_content_modal_id));
	var hold_content_id = $("#hold-content-id");
	var hold_content_ta = $("#hold-comments-ta");
	var hold_button = $("#hold-button");

	hold_button.on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var content_id = _this.data("id");
		hold_content_ta.val(_this.data("comments"));
		hold_content_id.val(content_id);
		hold_content_modal.modal("show");
		var form = hold_content_modal.find("form");
		form.attr("action", form.attr("action") + "/view");
	});
	
});	

</script>