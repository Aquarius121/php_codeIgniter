<script> window.__media_database_url = <?= json_encode($vd->database_url) ?>; </script>

<section class="search-form-panel full-width">
	<form class="md-search" id="md-search-form">
		<input type="search" value="" placeholder="Search Media Database"
			id="md-search-box" class="form-control col-lg-12 search-box span12" autocomplete="off">		
		<div id="md-match-filters" class="md-filters hidden">
			<div class="suggested">Matched Filters</div>
			<div class="results"></div>
		</div>
		<button type="button" id="md-search-button"></button>		
		<div id="md-search-buttons">
			<a id="md-search-submit-button" class="btn btn-primary">Search</a>
			<a id="md-clear-search-button" class="btn btn-default">Clear</a>
			<span class="divider"></span>
			<a id="md-add-filter-button" class="btn btn-success">Add Filter</a>
		</div>
	</form>
</section>

<div id="md-filters-list" class="md-filters hidden">
	<a id="md-clear-filter-button" class="btn btn-default">Remove Filters</a>
	<div class="results"></div>
</div>
	
<div class="row-fluid nomar md-content-row">
	<div class="span12">
		<div class="content listing md-content">
			<div id="md-results-relative" class="relative">
			
			