<?php if ($feedback->enable_inline): ?>
	<div class="alert alert-<?= $feedback->status ?>">
		<?php if ($feedback->title): ?>
			<strong><?= $vd->esc($feedback->title) ?></strong> 
		<?php endif ?>
		<?= $feedback->html ?>
	</div>
<?php endif ?>

<?php if ($feedback->enable_alert): ?>
	<?php ob_start(); ?>
	<div class="feedback-alert feedback-<?= $feedback->status ?>">
		<?php if ($feedback->title): ?>
			<h3><?= $vd->esc($feedback->title) ?></h3> 
		<?php endif ?>
		<?= $feedback->html ?>
	</div>
	<?php $message = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
	<script>

	(function() {
		if (window.bootbox !== undefined) return;
		var element = document.createElement("script");
		var src = <?= json_encode(concat($vd->assets_base, 'lib/bootbox.min.js')) ?>;
		element.setAttribute("src", src);
		document.body.appendChild(element);
	})();

	$(function() {
		var message = <?= json_encode($message) ?>;
		bootbox.alert({
			className: <?= json_encode(sprintf('bootbox-%s', $feedback->status)) ?>,
			message: message,
		});
	});

	</script>
<?php endif ?>