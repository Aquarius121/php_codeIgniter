<div class="main-content">
	
	<?php /*
	<?php if (!empty($this->vd->rejection_data->comments)
	       || !empty($this->vd->rejection_data->canned)): ?>
	<div class="share-side-lower">
	<?php else: ?>
	<div class="share-side-normal">
	<?php endif ?>
		<?= $ci->load->view('browse/view/partials/share-side') ?>
	</div>
	*/ ?>
	
	<?php if (!empty($this->vd->rejection_data->comments)
	       || !empty($this->vd->rejection_data->canned)): ?>
	<div class="rejection-data-container">
		<div class="rejection-data-icon" id="rejection-data-icon">
			<i class="fa fa-comments"></i>
		</div>
		<div class="alert alert-warning rejection-data visible" id="rejection-data">
			<div class="rejection-data-header">
				Editor Comments
				<span class="close">
					<i class="fa fa-remove"></i></span>
			</div>
			<?php $feedback = $this->vd->rejection_data; ?>
			<?php if (!empty($feedback->comments)): ?>
			<div class="rejection-comments">
				<strong>General comments.</strong>
				<br /><?= $feedback->comments ?>
			</div>
			<?php endif ?>
			<?php if (!empty($feedback->canned)): ?>
				<?php foreach ((array) $feedback->canned as $canned): ?>
					<?php $canned = Model_Canned::find($canned); ?>
					<div class="rejection-canned">
						<strong><?= $vd->esc($canned->title) ?></strong>
						<div class="html-content"><?= $canned->content ?></div>
					</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
	<script>
	
	$(function() {
		var rejection_data = $("#rejection-data");
		var rejection_data_icon = $("#rejection-data-icon");
		rejection_data.find(".close").on("click", function() {
			rejection_data.removeClass("visible");
		});
		rejection_data_icon.on("click", function() {
			rejection_data.addClass("visible");
		});
	});
	
	</script>
	<?php endif ?>	
	
	<section class="content-view">

		<div id="cv-container" class="content-type-<?= $vd->m_content->type ?>">
			<?= $ci->load->view("browse/view/{$vd->m_content->type}") ?>
		</div>
		
	</section>
	
</div>