<?= $ci->load->view('admin/store/menu-sites') ?>

<section class="top-menu top-menu-selector">
	<div class="container">
		<div class="row-fluid">
			<ul id="nav-menu" class="nav-activate">
				
				<li>
					<a data-on="^admin/store/(order|transaction)" 
						href="admin/store/order<?= $vd->esc(gstring()) ?>">Orders
					</a>
				</li>

				<li>
					<a data-on="^admin/store/coupon" 
						href="admin/store/coupon/active<?= $vd->esc(gstring()) ?>">Coupons
					</a>
				</li>

				<li>
					<a data-on="^admin/store/plan" 
						href="admin/store/plan/active<?= $vd->esc(gstring()) ?>">Plans
					</a>
				</li>

				<li>
					<a data-on="^admin/store/item" 
						href="admin/store/item/active<?= $vd->esc(gstring()) ?>">Items
					</a>
				</li>

				<li>
					<a data-on="^admin/store/renewals" 
						href="admin/store/renewals<?= $vd->esc(gstring()) ?>">Renewals
					</a>
				</li>
				
			</ul>
		</div>
	</div>
</section>
