<?= $ci->load->view('admin/companies/menu') ?>

<section class="top-menu top-menu-additional">
	<div class="container">
		<div class="row-fluid">
			<ul id="nav-menu" class="nav-activate">
				<li><a data-on="^admin/nr_builder/export_stats" 
						href="admin/nr_builder/export_stats">Export Stats</a></li>
				<li><a data-on="^admin/nr_builder/crunchbase" 
						href="admin/nr_builder/crunchbase/all">Crunch Base</a></li>
				<li><a data-on="^admin/nr_builder/prweb" 
						href="admin/nr_builder/prweb/all?filter_search=CHECK_LOGO">PRWeb</a></li>
				<li><a data-on="^admin/nr_builder/marketwired" 
						href="admin/nr_builder/marketwired/all?filter_search=CHECK_LOGO">MarketWired</a></li>
				<li><a data-on="^admin/nr_builder/businesswire" 
						href="admin/nr_builder/businesswire/all?filter_search=CHECK_LOGO">BusinessWire</a></li>
				<li><a data-on="^admin/nr_builder/owler" 
						href="admin/nr_builder/owler/all">Owler</a></li>
				<li><a data-on="^admin/nr_builder/mynewsdesk" 
						href="admin/nr_builder/mynewsdesk/all">MyNewsDesk</a></li>
				<li><a data-on="^admin/nr_builder/pr_co" 
						href="admin/nr_builder/pr_co/all">PR.Co</a></li>
				<li><a data-on="^admin/nr_builder/topseos" 
						href="admin/nr_builder/topseos/all">TopSeos</a></li>
			</ul>
		</div>
	</div>
</section>