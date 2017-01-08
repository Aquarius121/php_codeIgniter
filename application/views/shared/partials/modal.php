<div class="eob-modal modal fade <?= value_if_test(!empty($footer), 'has-footer') ?>"
	tabindex="-1" role="dialog" aria-hidden="true" id="<?= $id ?>">
	<div class="modal-dialog" role="document"
		style="width: <?= (int) (40 + $width) ?>px; 
		   max-width: <?= (int) (40 + $width) ?>px;">
		<?php if (!empty($header)): ?>
		<div class="modal-header modal-header-custom clearfix">
			<?= $header ?>
		</div>
		<?php else: ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				<i class="fa fa-remove icon-remove"></i>
			</button>
			<!-- the nbsp is required to show this -->
			<h3><?= $vd->esc($title) ?> &nbsp;</h3>
		</div>
		<?php endif ?>
		<div class="modal-body">
			<div class="modal-content"
				style="width: <?= (int) (40 + $width) ?>px;
					   height: <?= (int) (40 + $height) ?>px;
				  max-height: <?= (int) (40 + $height) ?>px">
				<?= $content ?>
				<div class="marbot-15"></div>
			</div>
		</div>
		<?php if (!empty($footer)): ?>
		<div class="modal-footer">
			<div class="modal-footer-content">
				<?= $footer ?>
			</div>			
		</div>
		<?php endif ?>
	</div>
</div>

<script>

$(function() {

	var modal = $("#<?= $id ?>");
	modal.modal({ show: <?= json_encode($as) ?> });

});

</script>