<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Search Results
					<?php if (!empty($vd->contact_list->name)): ?>
					<p class="text-muted search-subtitle">
						Contact List: <span><?= $vd->esc($vd->contact_list->name) ?></span>
					</p>
					<?php endif ?>

				</h2>
			</div>
		</div>
	</header>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<?= $this->load->view('manage/contact/partials/contact_listing', array('is_search_result' => 1)) ?>
			</div>
		</div>
	</div>
</div>