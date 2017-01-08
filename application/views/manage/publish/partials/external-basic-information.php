<div class="row">
	<div class="col-lg-8 col-md-7 form-col-1">
		<div class="panel panel-default">
			<div class="panel-body">
		
				<legend>Basic Information</legend>

				<fieldset class="form-section basic-information">
					<div class="row form-group">
						<div class="col-lg-12">
							<input class="form-control in-text col-lg-12 required" type="text" name="title" 
								id="title" placeholder="Enter Title of <?= $vd->content_type ?> Content"
								maxlength="<?= $ci->conf('title_max_length') ?>"
								value="<?= $vd->esc(@$vd->m_content->title) ?>" data-required-name="Title" />
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-12">
							<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary" rows="5" 
								data-required-name="Summary" placeholder="Enter Summary of <?= $vd->content_type ?> Content"
								><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
							<p class="help-block" id="summary_countdown_text">
								<span id="summary_countdown"></span> Characters Left</p>
							<script>
							
							defer(function() {

								$("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
									$("#summary_countdown_text"), 
									$("#summary_countdown"));
							});
							
							</script>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-12">
							<input class="form-control in-text col-lg-12 required url" type="text" name="source_url" 
								id="source_url" placeholder="Enter Source URL of <?= $vd->content_type ?> Content"
								value="<?= $vd->esc(@$vd->m_content->source_url) ?>" data-required-name="Source URL" />
						</div>
					</div>
				</fieldset>
				<?php $vd->image_item_count = 1 ?>
				<?= $ci->load->view('manage/publish/partials/web-images') ?>
			</div>
		</div>
	</div>
		
	<div class="col-lg-4 col-md-5 form-col-2">
		<div  id="locked_aside">
			<div class="panel panel-default">
				<div class="panel-body pad-20v">
					<?= $this->load->view('manage/publish/partials/status') ?>

					<fieldset class="ap-block ap-properties">
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text datepicker required" id="publish-date" type="text" 
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
													
						<div class="row">											
							<div class="col-lg-12 ta-center">
								<button type="submit" name="publish" value="1" 
									class="btn btn-success btn-publish submit-button">
									Publish</button>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<script>
	
	$(function() {

		var options = { offset: { top: 100 } };
		$.lockfixed("#locked_aside", options);

	});
	
	</script>
	
</div>