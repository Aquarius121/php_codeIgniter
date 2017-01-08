<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  page-title">
				<h2>Contacts Manager</h2>
			</div>

			<div class="ax-action-top col-lg-6">
				<div class="col-lg-12 actions nopad">
					<ul class="list-inline actions">
						<li><a href="manage/contact/import" class="btn btn-default">Import</a></li>
						<li><a href="manage/contact/contact/download" class="btn btn-default">Export All</a></li>
						<li><a href="manage/contact/contact/edit" class="btn btn-primary">Add Contact</a></li>
					</ul>
				</div>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<div class="panel-heading">
					<ul class="nav nav-tabs nav-activate tab-links ax-loadable" 
						data-ax-elements=".ax-search-form, .ax-action-top,
							#ax-chunkination, #ax-tab-content" id="tabs">
						<li><a data-on="^manage/contact/list" data-toggle="link"
							href="manage/contact/list">Lists</a></li>
						<li><a data-on="^manage/contact/contact" data-toggle="link" 
							href="manage/contact/contact">Contacts</a></li>
					</ul>
				</div>
				
				<?= $ci->load->view('manage/contact/partials/contact_listing') ?>

			</div>
		</div>
	</div>
	
</div>