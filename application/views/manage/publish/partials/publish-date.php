<div class="row form-group">
	<div class="col-lg-12">
		<input class="form-control col-lg-12 in-text datepicker" id="publish-date" type="text" 
			data-date-format="yyyy-mm-dd hh:ii" name="date_publish" 
			value="<?= @$vd->m_content->date_publish_str ?>"
			placeholder="Publish Date" />
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
		<?php if ($this->newsroom->timezone): ?>
		<p class="smaller text-muted date-timezone-subtext">
			<?= $vd->esc(TimeZone::common_name($this->newsroom->timezone)) ?>
			(<a target="_blank" href="manage/newsroom/company">edit</a>)
		</p>
		<?php endif ?>
	</div>
</div>