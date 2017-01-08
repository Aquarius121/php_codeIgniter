<?php if (count($vd->lists)): ?>
	<?php foreach ($vd->lists as $list): ?>
		<div class="builder-result row-fluid">
			<div class="span1 builder-radio">
				<label class="radio-container">
					<input type="radio" name="builder_select"
						class="builder-selected"
						value="<?= $list->id ?>" />
					<span class="radio"></span>
				</label>		
			</div>
			<div class="builder-primary span11">
				<?= $vd->esc($list->name) ?>
				<div class="muted">
					<?= $vd->esc($list->date_created) ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
<?php else: ?>
	<div class="status-false">None Found</div>
<?php endif ?>