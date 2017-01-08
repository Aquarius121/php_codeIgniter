<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^<?= $vd->store_base ?>/item/active" 
				href="<?= $vd->store_base ?>/item/active<?= $vd->esc(gstring()) ?>">Active</a></li>
			<li><a data-on="^<?= $vd->store_base ?>/item/system" 
				href="<?= $vd->store_base ?>/item/system<?= $vd->esc(gstring()) ?>">System</a></li>
			<li><a data-on="^<?= $vd->store_base ?>/item/deleted" 
				href="<?= $vd->store_base ?>/item/deleted<?= $vd->esc(gstring()) ?>">Deleted</a></li>
		</ul>
	</div>
</div>