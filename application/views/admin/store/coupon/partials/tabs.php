<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/store/coupon/active" 
				href="admin/store/coupon/active<?= $vd->esc(gstring()) ?>">Active</a></li>

			<li><a data-on="^admin/store/coupon/expired" 
				href="admin/store/coupon/expired<?= $vd->esc(gstring()) ?>">Expired</a></li>

			<li><a data-on="^admin/store/coupon/deleted" 
				href="admin/store/coupon/deleted<?= $vd->esc(gstring()) ?>">Deleted</a></li>
		</ul>
	</div>
</div>