<h2>Set Filter</h2>

<div class="row-fluid">
	<div class="span12">

		<?php foreach ($vd->sources as $i => $source): ?>
			<?php if ($i > 1 && $i%4 == 0): ?>
				</div></div>
				<div class="row-fluid">
					<div class="span12">
			<?php endif ?>
			<div class="span2">
				<label class="checkbox-container inline">
					<input type="checkbox" name="<?= $source ?>" value="1" 
						<?= value_if_test(in_array($source, $vd->sources_selected), 'checked=checked') ?> />
					<span class="checkbox"></span><?= Model_Company::full_source($source) ?>
				</label>
			</div>
		<?php endforeach ?>
	</div>
</div>
<div class="marbot-20"></div>

<div class="row-fluid">
	<div class="span4 placeholder-container">
		<select name="sales_agent_id" id="sales-agent-id"
			class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder"
			data-live-search="true">
			<option value="0" class="status-false" selected>None</option>
			<?php foreach ($vd->sales_agents as $sales_agent): ?>
				<option value="<?= $sales_agent->id ?>" <?= 
					value_if_test($vd->sales_agent_id == $sales_agent->id, 'selected=selected') ?>>
					<?= $vd->esc($sales_agent->name()) ?>
				</option>
			<?php endforeach ?>
		</select>
		<strong class="placeholder">Sales Agent</strong>
	</div>
</div>

<div class="row-fluid pad-20v">
	<div class="span12">
		<div class="span8">
			<div class="checkbox-container-box">
				<div>
					<div class="btn-group" role="group" aria-label="Basic example">
				  		
				  		<button type="button" class="<?= value_if_test(@$vd->duration == "this_year", 
				  			"btn-success") ?> btn btn-duration" data-id="this_year">This Year</button>

				  		<button type="button" class="<?= value_if_test(!@$vd->is_posted || @$vd->duration == "this_month", 
				  			"btn-success") ?> btn btn-duration" data-id="this_month">This Month</button>
				  		
				  		<button type="button" class="<?= value_if_test(@$vd->duration == "last_month", 
				  			"btn-success") ?> btn btn-duration" data-id="last_month">Last Month</button>
				  		
				  		<button type="button" class="<?= value_if_test(@$vd->duration == "this_week", 
				  			"btn-success") ?> btn btn-duration" data-id="this_week">This Week</button>
				  		
				  		<button type="button" class="<?= value_if_test(@$vd->duration == "today", 
				  			"btn-success") ?> btn btn-duration" data-id="today">Today</button>
				  		
				  		<button type="button" class="<?= value_if_test(@$vd->duration == "custom", 
				  			"btn-success") ?> btn btn-duration" data-id="custom">Custom</button>

				  		<input type="hidden" name="duration" id="duration" 
				  			value="<?= value_if_test(@$vd->duration, $vd->duration, 'this_month') ?>" />
					</div>
					<script>
					defer(function(){
						
						$("button.btn-duration").on("click", function(){
							var _this = $(this);
							var id = _this.data('id');
							var dates = $("#dates");
							var duration = $("#duration");
							var date_start = $("#date-start");
							var date_end = $("#date-end");

							$("button.btn-duration").removeClass('btn-success');
							_this.addClass('btn-success');							

							if (id == "custom")
							{
								dates.slideDown();
								date_start.prop('required', true);
								date_start.prop('required', true);
					
							}
							else
							{
								date_start.prop('required', false);
								date_start.prop('required', false);
								dates.slideUp();
							}

							duration.val(id);

						});

					})
					</script>
				</div>

				<div id="dates" class="<?= value_if_test(!@$vd->is_posted || $vd->duration !== "custom", "hidden") ?>" >
					<div class="marbot-15"></div>
					<div class="input-append relative">
						<label>Start</label>
						<input type="text" name="date_start" class="span10" 
							id="date-start" value="<?= @$vd->date_start ?>"
							<?= value_if_test($vd->duration == "custom", "required='true'") ?> />
						<span class="add-on"><i class="icon-calendar"></i></span>
						
					</div>

					<div class="input-append relative">
						<label>End</label>
						<input type="text" name="date_end" class="" 
							id="date-end" value="<?= @$vd->date_end ?>"	
							<?= value_if_test($vd->duration == "custom", "required='true'") ?> />
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>

				</div>

				<script>
				defer(function() {

					var date_start = $("#date-start");
					var date_end = $("#date-end");
					
					date_start.datetimepicker({
						autoclose: true,
						todayBtn: true,
						minView: 1,
					});

					date_end.datetimepicker({
						autoclose: true,
						todayBtn: true,
						minView: 1,
					});
					
					date_start.on("changeDate", function(ev) {
						ev.date.setMinutes(0);
					});

					date_end.on("changeDate", function(ev) {
						ev.date.setMinutes(0);
					});
					
				});
				</script> 
				
				
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<button class="span6 bt-orange bt-submit" value="1" name="bt_stats_report" type="submit">
				Generate Stats Table
			</button>

			<button class="span6 bt-silver" value="1" name="bt_single_stats" type="submit">
				Generate Single Stats
			</button>
		</div>
	</div>
</div>