<ul id="md-add-filter-tabs">
	<li class="active">BEATS</li>
	<li>MEDIA TYPE</li>
	<li>ROLE</li>
	<li>COVERAGE</li>
	<li>LOCATION</li>
</ul>

<ul id="md-add-filter-tab-content">
	<li id="md-aftc-beats" class="active clearfix">
		<input type="text" id="md-beats-search" class="form-control md-filter-search">
		<div class="results"></div>
	</li>
	<li id="md-aftc-media-types" class="clearfix"></li>
	<li id="md-aftc-roles" class="clearfix"></li>
	<li id="md-aftc-coverages" class="clearfix"></li>
	<li id="md-aftc-locations" class="clearfix">
		<div id="md-locations-header">
			<ul class="md-locations-slider">
				<li class="active" data-stable="1" data-list="countries" data-render-callback="render_list_countries">COUNTRY</li>
				<li data-list="regions"  data-render-callback="render_list_regions">REGION</li>
				<li data-list="localities"  data-render-callback="render_list_localities" class="final">CITY</li>
			</ul>
			<a class="md-locations-next btn btn-success">
				NEXT STEP
			</a>
		</div>		
		<div id="md-locations-content" class="marbot-15">
			<div id="md-aftc-countries" class="clearfix results"></div>
			<div id="md-aftc-regions" class="hidden clearfix">
				<input type="text" id="md-region-search" class="form-control md-filter-search">
				<div class="results"></div>
			</div>
			<div id="md-aftc-localities" class="hidden clearfix">
				<input type="text" id="md-locality-search" class="form-control md-filter-search">
				<div class="results"></div>
				</script>
			</div>
		</div>
		<a class="md-locations-next btn btn-success marbot-20">
			NEXT STEP
		</a>
	</li>
</ul>