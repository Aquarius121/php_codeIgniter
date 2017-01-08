<?= $ci->load->view('admin/store/menu-sites') ?>

<section class="top-menu top-menu-selector">
	<div class="container">
		<div class="row-fluid">
			<ul id="nav-menu" class="nav-activate">
				
				<?php if ($vd->store_has_orders): ?>
				<li>
					<a data-on="^<?= $vd->store_base ?>/(order|transaction)" 
						href="<?= $vd->store_base ?>/order<?= $vd->esc(gstring()) ?>">Orders
					</a>
				</li>
				<?php endif ?>

				<?php if ($vd->store_has_coupons): ?>
				<li>
					<a data-on="^<?= $vd->store_base ?>/coupon" 
						href="<?= $vd->store_base ?>/coupon/active<?= $vd->esc(gstring()) ?>">Coupons
					</a>
				</li>
				<?php endif ?>

				<?php if ($vd->store_has_plans): ?>
				<li>
					<a data-on="^<?= $vd->store_base ?>/plan" 
						href="<?= $vd->store_base ?>/plan/active<?= $vd->esc(gstring()) ?>">Plans
					</a>
				</li>
				<?php endif ?>

				<?php if ($vd->store_has_items): ?>
				<li>
					<a data-on="^<?= $vd->store_base ?>/item" 
						href="<?= $vd->store_base ?>/item/active<?= $vd->esc(gstring()) ?>">Items
					</a>
				</li>
				<?php endif ?>

				<?php if ($vd->store_has_renewals): ?>
				<li>
					<a data-on="^<?= $vd->store_base ?>/renewals" 
						href="<?= $vd->store_base ?>/renewals<?= $vd->esc(gstring()) ?>">Renewals
					</a>
				</li>
				<?php endif ?>
				
			</ul>
		</div>
	</div>
</section>
