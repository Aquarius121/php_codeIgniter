<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
					<h2>Edit Event</h2>
				<?php else: ?>
					<h2>Add Event</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form has-premium" method="post" action="manage/publish/event/edit/save" id="content-form">
	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">

					<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
					
					<?php if ($vd->m_content && !$vd->duplicate): ?>
					<input type="hidden" name="id" value="<?= $vd->m_content->id ?>" />
					<?php endif ?>
					
					<section class="event-date marbot-20">
						<div class="row form-group">
							<div class="col-lg-5">
								<legend>Event Start Date</legend>
								<div class="input-group in-text-date in-text-add-on marbot-15">
									<input class="form-control required" id="date-start" name="date_start"
										data-date-format="yyyy-mm-dd" type="text" data-required-use-parent="1" 
										data-required-name="Start Date"
										value="<?= $vd->esc(@$vd->m_content->date_start_str) ?>" />
									<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
								</div>
								<div class="input-group in-text-add-on bootstrap-timepicker">
									<input class="form-control in-text" id="time-start" name="time_start" type="text" 
										value="<?= $vd->esc(@$vd->m_content->time_start_str) ?>" />
									<span class="input-group-addon"><i class="fa fa-fw fa-clock-o"></i></span>
								</div>
							</div>
							<div class="col-lg-5">
								<legend>Event End Date</legend>
								<div class="input-group in-text-date in-text-add-on marbot-15">
									<input class="form-control" id="date-finish" name="date_finish"
										data-date-format="yyyy-mm-dd" type="text"
										value="<?= $vd->esc(@$vd->m_content->date_finish_str) ?>" />
									<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
								</div>
								<div class="input-group in-text-add-on bootstrap-timepicker">
									<input class="form-control in-text" id="time-finish" name="time_finish" type="text"
										value="<?= $vd->esc(@$vd->m_content->time_finish_str) ?>" />
									<span class="input-group-addon"><i class="fa fa-fw fa-clock-o"></i></span>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" name="is_all_day" id="is-all-day"
										<?= value_if_test(@$vd->m_content->is_all_day, 'checked') ?> />
									<span class="checkbox"></span>
									Runs All Day
								</label>
							</div>
						</div>
						<script>

						$(function() {
							
							var extra_fields_disabled = false;
							var nowTemp = new Date();
							var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
								nowTemp.getDate(), 0, 0, 0, 0);
							
							var date_start = $("#date-start");
							var date_finish = $("#date-finish");
							var date_start_i = date_start.next("span");
							var date_finish_i = date_finish.next("span");
							
							var time_start = $("#time-start");
							var time_finish = $("#time-finish");
							var time_start_i = time_start.next("span");
							var time_finish_i = time_finish.next("span");
							
							var date_fields = $();
							date_fields = date_fields.add(date_start);
							date_fields = date_fields.add(date_finish);
							
							var date_icons = $();
							date_icons = date_icons.add(date_start_i);
							date_icons = date_icons.add(date_finish_i);
							
							var time_icons = $();
							time_icons = time_icons.add(time_start_i);
							time_icons = time_icons.add(time_finish_i);
							
							$(date_fields).datepicker({
								onRender: function(date) {
									if (date.valueOf() < now.valueOf())
										return 'disabled';
								}
							});
							
							$(time_start).timepicker({
								defaultTime: '09:00 AM',
								minuteStep: 5,
								showMeridian: true,
								showInputs: false,
								showSeconds: true
							});
							
							$(time_finish).timepicker({
								defaultTime: '05:00 PM',
								minuteStep: 5,
								showMeridian: true,
								showInputs: false,
								showSeconds: true
							});
								
							$(date_icons).on("click", function() {
								if (extra_fields_disabled) return;
								$(this).prev("input").datepicker("show");
							});
							
							$(time_icons).on("click", function() {
								if (extra_fields_disabled) return;
								$(this).prev("input").timepicker("showWidget");
							});
							
							var is_all_day = $("#is-all-day");
							is_all_day.on("change", function() {
								extra_fields_disabled = is_all_day.is(":checked");
								date_finish.attr("disabled", extra_fields_disabled);
								time_start.attr("disabled", extra_fields_disabled);
								time_finish.attr("disabled", extra_fields_disabled);
							}).trigger("change");
							
						});

						</script>
					</fieldset>
						
					<fieldset class="basic-information">
						<legend>Basic Information</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 required" type="text" name="title" 
									id="title" placeholder="Enter Title of Event"
									value="<?= $vd->esc(@$vd->m_content->title) ?>" 
									maxlength="<?= $ci->conf('title_max_length') ?>"
									data-required-name="Title" />
							</div>
						</div>							
						<div class="row form-group">
							<div class="col-lg-12">
								<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary"
									data-required-name="Summary" placeholder="Enter Summary of Event" rows="5"
									><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
								<p class="help-block" id="summary_countdown_text">
									<span id="summary_countdown"></span> Characters Left</p>
								<script>
								
								$(function() {									

									$("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
										$("#summary_countdown_text"), 
										$("#summary_countdown")
									);
								})
								
								</script>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12 marbot-20">
								<textarea class="form-control in-text in-content col-lg-12 required" id="content"
									data-required-name="Event Description" name="content" 
									placeholder="Event Description"><?= 
										$ci->load->view('partials/html-content', 
											array('content' => @$vd->m_content->content)) 
								?></textarea>
								<script>
								
								defer(function() {
									window.init_editor($("#content"), { height: 400 });
								})								
								
								</script>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" name="address" 
									placeholder="Enter Address / Location"
									value="<?= $vd->esc(@$vd->m_content->address) ?>"  />
							</div>
						</div>
					</fieldset>
						
					<fieldset>
						<legend>Event Pricing</legend>
						<div class="row form-group">
							<div class="col-lg-6">
								<div class="input-group in-text-price in-text-add-on">
									<span class="input-group-addon">$</span>
									<input class="form-control" id="price" type="text" name="price"
										placeholder="Price" min="0" pattern="^(\d+(\.\d{2})?)?$"
										value="<?= value_if_test(@$vd->m_content->price, 
											sprintf('%.2f', @$vd->m_content->price)) ?>" />
								</div>
							</div>
							<div class="col-lg-6">
								<input class="form-control col-lg-12 in-text" name="discount_code" 
									type="text" placeholder="Discount Code"
									value="<?= $vd->esc(@$vd->m_content->discount_code) ?>"  />
							</div>
						</div>
					</fieldset>

					<?= $ci->load->view('manage/publish/partials/tags') ?>
					<?= $ci->load->view('manage/publish/partials/web-images') ?>						
					<?= $ci->load->view('manage/publish/partials/relevant-resources') ?>
					<?= $ci->load->view('manage/publish/partials/social-media') ?>
						
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default" id="locked_aside">
				<div class="panel-body">
					<fieldset class="ap-block ap-properties nomarbot">

						<?= $this->load->view('manage/publish/partials/status') ?>
						
						<div class="row form-group">
							<div class="col-lg-12" id="select-event-type">
								<select class="form-control selectpicker show-menu-arrow col-lg-12" name="event_type_id" 
									data-required-name="Event Type"
									data-required-use-parent="1">
									<option class="selectpicker-default" title="Select Event Type" value=""
										<?= value_if_test(!@$vd->m_content->event_type_id, 'selected') ?>>None</option>
									<?php foreach ($event_types as $et): ?>
									<option value="<?= $et->id ?>"
										<?= value_if_test((@$vd->m_content->event_type_id == $et->id), 'selected') ?>>
										<?= $vd->esc($et->name) ?>
									</option>
									<?php endforeach ?>
								</select>
								<script>

								$(function() {
									
									var select = $("#select-event-type select")
									select.on_load_select();
										
									$(window).load(function() {
										select.addClass("required");
									});
									
								});
								
								</script>
							</div>
						</div>

						<?php if (!@$vd->m_content->is_published): ?>
							<?= $this->load->view('manage/publish/partials/publish-date') ?>
						<?php endif ?>

						<?= $ci->load->view('manage/publish/partials/save-buttons') ?>

					</fieldset>
				</div>
			</div>
		</div>


		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap-timepicker.css');
			$loader->add('lib/bootstrap-datepicker.css');
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap-timepicker.js');
			$loader->add('lib/bootstrap-datepicker.js');
			$loader->add('js/required.js');
			$loader->add('lib/bootbox.min.js');
			$loader->add('lib/jquery.lockfixed.js');
			$ci->add_eob($loader->render($render_basic));

		?>
		
		<script>
		
		$(function() {

			if (is_desktop())
			{
				var options = { offset: { top: 100 } };
				$.lockfixed("#locked_aside", options);
			}

		});
		
		</script>
	</div>
	</form>
</div>