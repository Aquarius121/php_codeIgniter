<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<?php if ($vd->store_has_orders): ?>
			<li><a data-on="^<?= $this->vd->store_base ?>/order" 
				href="<?= $this->vd->store_base ?>/order<?= $vd->esc(gstring()) ?>">Orders</a></li>
			<?php endif ?>
			<?php if ($vd->store_has_transactions): ?>
			<li><a data-on="^<?= $this->vd->store_base ?>/transaction" 
				href="<?= $this->vd->store_base ?>/transaction<?= $vd->esc(gstring()) ?>">Transactions</a></li>
			<?php endif ?>
		</ul>
	</div>
</div>