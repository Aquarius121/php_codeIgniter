<nav class="filter-bar">
	<div class="container-insights">
		<ul class="filter-bar-sections">
			<li class="a-status"><i class="fa fa-filter filter-bar-status"></i></li>
			<li class="a-menu">
				<a href="#" id="categories-button">
					Categories
					<span class="caret"></span>
				</a>
			</li>
			<li class="a-menu">
				<a href="#" id="media-button">
					Media
					<span class="caret"></span>
				</a>
			</li>
			<li class="date-select-li">
				<div class="date-select">
					<span class="date-label">FROM</span>
					<input class="form-control in-text datepicker required" type="text" 
						data-date-format="M d, yyyy" id="date-select-from"
						value="<?= Date::utc(Date::years(-1))->format('M j, Y') ?>" />
					<span class="date-calendar"><i class="fa fa-calendar"></i></span>
				</div>
				<div class="date-select">
					<span class="date-label">TO</span>
					<input class="form-control in-text datepicker required" type="text" 
						data-date-format="M d, yyyy" id="date-select-to"
						value="<?= Date::utc()->format('M j, Y') ?>" />
					<span class="date-calendar"><i class="fa fa-calendar"></i></span>
				</div>
			</li>
			<li class="a-button" id="apply-button">
				<button class="selection-dialog-apply
					btn btn-default btn-sm-padding" type="button">Apply</button>
			</li>
			<li class="pull-right a-button">
				<button id="alert-button" class="btn btn-success btn-slight-border btn-sm-padding">Create Alert</button>
				<button id="reset-button" class="btn btn-grey btn-slight-border btn-sm-padding">Reset</button>
			</li>
		</ul>
	</div>
</nav>

<div id="categories-selection" class="selection-dialog">
	<div class="select-industry-scroll">
		<ul class="distribution-list below-label select-industry" id="select-industry">
			<?php foreach ($vd->beats as $group): ?>
				<li class="select-industry-container">
					<div class="select-industry-group">
						<div class="select-industry-info">
							<div class="select-industry-group-expander"></div>
							<span class="group-name"><?= $vd->esc($group->name) ?></span>
							<span class="group-beat-counter">
								(<span class="group-beat-counter-selected">0</span>/<?= count($group->beats) ?>)
							</span>
						</div>
					</div>
					<ul class="select-industry-group-beats">
						<?php foreach ($group->beats as $beat): ?>
						<li data-index="<?= $vd->esc($beat->id) ?>" class="select-industry-beat">
							<div class="select-industry-cbox">
								<label class="checkbox-container small-checkbox">
									<input class="beat-cbox" type="checkbox" value="<?= $beat->id ?>" />
									<span class="checkbox"></span>		
									<?= $vd->esc($beat->name) ?>
								</label>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</li>
			<?php endforeach ?>
		</ul>
	</div>

	<script>

	$(function() {

		var select_industry = $("#select-industry");
		
		select_industry.on("click", ".select-industry-group", function(ev) {
			var gcontainer = $(this);
			gcontainer.toggleClass("active");
			ev.preventDefault();
			return false;
		});

		select_industry.on("change", "input.beat-cbox", function() {
			
			var _this = $(this);
			var checked = _this.is(":checked");
			var container = _this.parents(".select-industry-beat");
			container.toggleClass("selected", checked);

			// * update the parent cbox
			// * update the parent counter
			var beats_ul = container.parents("ul.select-industry-group-beats");
			var gcontainer = beats_ul.parents(".select-industry-container");
			var gcount_selected = beats_ul.find("input.beat-cbox:checked").length;
			var gcount_total = beats_ul.find("input.beat-cbox").length;
			var gcounter = gcontainer.find(".group-beat-counter");
			var gcounter_count = gcontainer.find(".group-beat-counter-selected");
			gcounter_count.text(gcount_selected);
			gcounter.toggleClass("status-black", gcount_selected > 0);
			gcontainer.toggleClass("selected", gcount_selected > 0);

		});

		select_industry.find("input.beat-cbox:checked").trigger("change");
		select_industry.addClass("computed");

	});

	</script>
</div>

<div id="media-selection" class="selection-dialog">
	<ul>
		<li class="select-media-cbox">
			<label class="checkbox-container small-checkbox">
				<input class="media-cbox" type="checkbox" checked value="<?= Model_Content::TYPE_PR ?>" />
				<span class="checkbox"></span>		
				Press Releases
			</label>
		</li>
		<li class="select-media-cbox">
			<label class="checkbox-container small-checkbox">
				<input class="media-cbox" type="checkbox" value="<?= Model_Content::TYPE_NEWS ?>" />
				<span class="checkbox"></span>		
				News
			</label>
		</li>
	</ul>
</div>

<section class="results-container">
	<div class="container-insights">
		<div class="results clearfix"></div>
		<div class="results-loader"></div>
	</div>
</section>

<script type="text/template" id="result-template">
	<?= $ci->load->view_raw('manage/insights/result.mustache') ?>
</script>