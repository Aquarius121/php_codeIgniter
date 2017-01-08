<section class="top-menu">
	<div class="container">
		<div class="row-fluid">
			<ul id="nav-menu" class="nav-activate">
				<li><a data-on="^admin/contact/campaign" href="admin/contact/campaign<?= $vd->esc(gstring()) ?>">Campaigns</a></li>
				<li><a data-on="^admin/contact/list/customer" href="admin/contact/list/customer<?= $vd->esc(gstring()) ?>">Lists</a></li>
				<li><a data-on="^admin/contact/contact" href="admin/contact/contact<?= $vd->esc(gstring()) ?>">Contacts</a></li>
				<li><a data-on="^admin/contact/media_database" href="admin/contact/media_database">Media Database</a></li>
				<li><a data-on="^admin/contact/list/builder" href="admin/contact/list/builder<?= $vd->esc(gstring()) ?>">List Builder</a></li>
				<li><a data-on="^admin/contact/pitch_wizard_order/order/all" 
					href="admin/contact/pitch_wizard_order/order/all<?= $vd->esc(gstring()) ?>">Pitch Manager</a></li>
				<li><a data-on="^admin/contact/pitch_wizard_order/[a-z0-9_]+_list" 
					href="admin/contact/pitch_wizard_order/all_list<?= $vd->esc(gstring()) ?>">Pitch Lists</a></li>
			</ul>
		</div>
	</div>
</section>