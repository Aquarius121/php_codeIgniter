<?php if (count($vd->users)): ?>
	<?php foreach ($vd->users as $user): ?>
		<?php $virtual_user = null; ?>
		<?php $virtual_source = null; ?>
		<?php if ($user->is_virtual()): ?>
		<?php $virtual_user = $user->virtual_user(); ?>
		<?php $virtual_source = $virtual_user ? $virtual_user->virtual_source() : null; ?>
		<?php endif ?>
		<div class="transfer-result find-result row-fluid">
			<div class="span1 transfer-radio find-radio">
				<label class="radio-container">
					<input type="radio" name="transfer_select"
						data-gstring="&amp;filter_user=<?= $user->id ?>"
						class="transfer-selected find-selected"
						value="<?= $user->id ?>" />
					<span class="radio"></span>
				</label>		
			</div>
			<div class="transfer-primary span11">
				<?php if ($virtual_user && $virtual_source): ?>
				<div class="virtual-user-source"><span class="source"><?= $virtual_source->name ?></span> VIRTUAL</div>
				<?php endif ?>
				<?= $vd->esc($user->name()) ?>
				<div class="muted break-word">
					<?php if ($user->is_virtual()): ?>
						<?= $vd->esc($user->virtual_user()->email) ?>
					<?php else: ?>
						<?= $vd->esc($user->email) ?>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
<?php else: ?>
	<div class="status-false">None Found</div>
<?php endif ?>