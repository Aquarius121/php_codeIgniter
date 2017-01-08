<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^<?= $vd->store_base ?>/coupon/active" 
				href="<?= $vd->store_base ?>/coupon/active<?= $vd->esc(gstring()) ?>">Active</a></li>
			<li><a data-on="^<?= $vd->store_base ?>/coupon/expired" 
				href="<?= $vd->store_base ?>/coupon/expired<?= $vd->esc(gstring()) ?>">Expired</a></li>
			<li><a data-on="^<?= $vd->store_base ?>/coupon/deleted" 
				href="<?= $vd->store_base ?>/coupon/deleted<?= $vd->esc(gstring()) ?>">Deleted</a></li>
		</ul>
	</div>
</div>