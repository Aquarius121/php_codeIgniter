<?php if (count($vd->companies)): ?>
	<?php foreach ($vd->companies as $company): ?>
		<?php $virtual_user = null; ?>
		<?php $virtual_source = null; ?>
		<?php if ($company->user->is_virtual()): ?>
		<?php $virtual_user = $company->user->virtual_user(); ?>
		<?php $virtual_source = $virtual_user ? $virtual_user->virtual_source() : null; ?>
		<?php endif ?>
		<div class="transfer-result find-result row-fluid">
			<div class="span1 transfer-radio find-radio">
				<label class="radio-container">
					<input type="radio" name="transfer_select"
						data-gstring="&amp;filter_company=<?= $company->id ?>"
						class="find-selected transfer-selected" value="<?= $company->id ?>" />
					<span class="radio"></span>
				</label>
			</div>
			<div class="transfer-primary span5">
				<?php if ($virtual_user && $virtual_source): ?>
				<div class="virtual-user-source">VIRTUAL</div>
				<?php endif ?>
				<?= $vd->esc($company->name) ?>
				<div class="muted break-word">
					<?= $vd->esc($company->newsroom) ?>
				</div>
			</div>
			<div class="transfer-secondary span6">
				<?php if ($virtual_user && $virtual_source): ?>
				<div class="virtual-user-source"><span class="source"><?= $virtual_source->name ?></span> VIRTUAL</div>
				<?php endif ?>
				<?= $vd->esc($company->user->name()) ?>
				<div class="muted break-word">
					<?php if ($company->user->is_virtual()): ?>
						<?= $vd->esc($company->user->virtual_user()->email) ?>
					<?php else: ?>
						<?= $vd->esc($company->user->email) ?>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
<?php else: ?>
	<div class="status-false">None Found</div>
<?php endif ?>