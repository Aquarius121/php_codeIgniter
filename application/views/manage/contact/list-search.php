<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Search Results</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<?= $this->load->view('manage/contact/partials/list_listing', array('is_search_result' => 1), true) ?>
			</div>
		</div>
	</div>

	<div id="ax-chunkination">
		<div class="ax-loadable"
			data-ax-elements="#ax-chunkination, #ax-tab-content">
			<?= $vd->chunkination->render() ?>
		</div>

		<p class="pagination-info ta-center">
			Displaying <?= count($vd->results) ?> 
			of <?= $vd->chunkination->total() ?> Lists
		</p>
	</div>
</div>