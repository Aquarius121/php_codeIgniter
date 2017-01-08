<form action="admin/publish/edit_distribution" method="post">

	<input type="hidden" name="id" value="<?= $vd->esc($vd->content->id) ?>" />
	<input type="hidden" name="redirect" value="<?= $vd->esc($vd->redirect) ?>" />

	<div class="alert alert-danger">
		<strong>Attention!</strong>
		Removing a bundled network will lose associated data.
		It's recommended to use the PR form when possible.
	</div>

	<?php foreach ($vd->m_crp_confirmed as $provider => $m_crp): ?>
		<div class="edit-distribution-crp">
			<span class="label-class"><strong class="status-true">ENABLED</strong></span>
			<strong><?= $vd->esc($m_crp->name()) ?></strong><br />
			<button class="btn btn-mini btn-success" name="add" value="<?= $vd->esc($provider) ?>" disabled>Add</button>
			<button class="btn btn-mini btn-danger" name="remove" value="<?= $vd->esc($provider) ?>">Remove</button>
		</div>
	<?php endforeach ?>

	<?php foreach ($vd->m_crp_selected as $provider => $m_crp): ?>
		<div class="edit-distribution-crp">
			<span class="label-class"><strong class="status-muted">SELECTED</strong></span>
			<strong><?= $vd->esc($m_crp->name()) ?></strong><br />
			<button class="btn btn-mini btn-success" name="add" value="<?= $vd->esc($provider) ?>">Add</button>
			<button class="btn btn-mini btn-danger" name="remove" value="<?= $vd->esc($provider) ?>">Remove</button>
		</div>
	<?php endforeach ?>

	<?php foreach ($vd->rp_names as $provider => $name): ?>
		<?php if (isset($vd->m_crp_confirmed[$provider])) continue; ?>
		<?php if (isset($vd->m_crp_selected[$provider])) continue; ?>
		<div class="edit-distribution-crp">
			<span class="label-class"><strong class="status-false">DISABLED</strong></span>
			<strong><?= $vd->esc($name) ?></strong><br />
			<button class="btn btn-mini btn-success" name="add" value="<?= $vd->esc($provider) ?>">Add</button>
			<button class="btn btn-mini btn-danger" name="remove" value="<?= $vd->esc($provider) ?>" disabled>Remove</button>
		</div>
	<?php endforeach ?>

</form>