<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/store/item/active" 
				href="admin/store/item/active<?= $vd->esc(gstring()) ?>">Active</a></li>
			<li><a data-on="^admin/store/item/system" 
				href="admin/store/item/system<?= $vd->esc(gstring()) ?>">System</a></li>
			<li><a data-on="^admin/store/item/deleted" 
				href="admin/store/item/deleted<?= $vd->esc(gstring()) ?>">Deleted</a></li>

		</ul>
	</div>
</div>