<?php 

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datetimepicker.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<form action="<?= $ci->newsroom->url() ?>manage/writing/approve/<?= $vd->writing_session->id ?>"
	id="approve-confirm-form" method="post">
	
	<ul>
		<li class="radio-container-box marbot pad-15h">
			<label class="radio-container louder">
				<input type="radio" name="schedule" class="schedule-radio" 
					value="0" checked />
				<span class="radio"></span>
				For Immediate Release.
			</label>
			<p class="muted">
				The press release will be scheduled for immediate release. 
			</p>
		</li>
		<li class="radio-container-box marbot pad-15h">
			<label class="radio-container louder marbot-15">
				<input type="radio" name="schedule" class="schedule-radio" value="1"
					id="schedule-radio-on" />
				<span class="radio"></span>
				Choose Release Date. 
			</label>
			<div class="row-fluid">
				<div class="span12">
					<input class="span12 in-text datepicker marbot-10" id="publish-date" type="text" 
						data-date-format="yyyy-mm-dd hh:ii" name="date_publish" 
						placeholder="Release Date" />
					<script>
					
					$(function() {
						
						var nowTemp = new Date();
						var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
							nowTemp.getDate(), 0, 0, 0, 0);
						
						var publish_date = $("#publish-date")
						
						publish_date.datetimepicker({
							startDate: now,
							autoclose: true,
							todayBtn: true,
							minView: 1,
						});
						
						publish_date.on("changeDate", function(ev) {
							ev.date.setMinutes(0);
						});
						
					});
					
					</script>
					<?php if ($ci->newsroom->timezone): ?>
					<p class="smaller muted date-timezone-subtext">
						<?= $vd->esc(TimeZone::common_name($ci->newsroom->timezone)) ?>
						(<a target="_blank" href="manage/newsroom/company">edit</a>)
					</p>
					<?php endif ?>
				</div>
			</div>
		</li>
	</ul>

	<script>

	$(function() {

		var radio = $("#schedule-radio-on");
		var date = $("#publish-date");

		radio.on("change", function() {
			if (radio.is(":checked"))
				date.focus();
		});

	});

	</script>

</form>

<?php

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datetimepicker.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>