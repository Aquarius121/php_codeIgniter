<td>
	<?php if (!empty($result->user)): ?>
		<?php $virtual_user = null; ?>
		<?php $virtual_source = null; ?>
		<?php if ($result->user->is_virtual()): ?>
		<?php $virtual_user = $result->user->virtual_user(); ?>
		<?php $virtual_source = $virtual_user ? $virtual_user->virtual_source() : null; ?>
		<?php if ($virtual_user && $virtual_source): ?>
		<div class="virtual-user-source"><span class="source"><?= $virtual_source->name ?></span> VIRTUAL</div>
		<?php endif ?>
		<?php endif ?>
		<div>								
			<a data-gstring="&amp;filter_user=<?= $result->user->id ?>"
				href="#" class="add-filter-icon"></a>							
			<a href="admin/users/view/<?= $result->user->id ?>" class="black">
				<?php if ($result->user->first_name && $result->user->last_name): ?>
				<?= $vd->esc($result->user->first_name) ?>
				<?= $vd->esc($result->user->last_name) ?>
				<?php else: ?>
					<?php if ($virtual_user): ?>
					<?= $vd->esc($vd->cut($virtual_user->email, 30)) ?>
					<?php else: ?>
					<?= $vd->esc($vd->cut($result->user->email, 30)) ?>
					<?php endif ?>
				<?php endif ?>
			</a>
		</div>
	<?php endif ?>
	<?php if (!empty($result->o_company_id)): ?>		
	<div>
		<a data-gstring="&amp;filter_company=<?= $result->o_company_id ?>"
			href="#" class="add-filter-icon"></a>
		<a href="admin/companies/view/<?= $result->o_company_id ?>" class="muted">
			<?= $vd->esc($vd->cut($result->o_company_name, 20)) ?>
		</a>
	</div>
	<?php elseif (!empty($result->user)): ?>
	<?php if ($result->user->first_name && $result->user->last_name): ?>
	<div class="muted">
		<?php if ($virtual_user): ?>
		<?= $vd->esc($vd->cut($virtual_user->email, 30)) ?>
		<?php else: ?>
		<?= $vd->esc($vd->cut($result->user->email, 30)) ?>
		<?php endif ?>
	</div>
	<?php endif ?>
	<?php endif ?>	

</td>