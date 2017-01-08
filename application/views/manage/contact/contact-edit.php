<ul class="breadcrumb">
	<li><a href="manage/contact">Media Outreach</a> <span class="divider">&raquo;</span></li>
	<?php if (@$vd->from_m_contact_list): ?>
	<li><a href="manage/contact/list">Lists</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/contact/list/edit/<?= $vd->from_m_contact_list->id ?>">
		<?= $vd->esc($vd->from_m_contact_list->name) ?></a> 
		<span class="divider">&raquo;</span></li>
	<?php else: ?>
	<li><a href="manage/contact/contact">Contacts</a> <span class="divider">&raquo;</span></li>
	<?php endif ?>
	<?php if (@$vd->contact): ?>
	<li class="active">Edit Contact</li>
	<?php else: ?>
	<li class="active">Add Contact</li>
	<?php endif ?>
</ul>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->contact): ?>
					<h2>Edit Contact</h2>
				<?php else: ?>
					<h2>Add Contact</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form" method="post" action="manage/contact/contact/edit/save">	
	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<input type="hidden" id="contact-id" name="contact_id" value="<?= @$vd->contact->id ?>" />
							<fieldset>
								<legend>Basic Information</legend>
								<div class="row form-group">
									<div class="col-xs-12 col-sm-12 col-md-6 ">
										<input class="form-control in-text required" type="text" 
											name="first_name" placeholder="First Name"
											data-required-name="First Name"
											value="<?= $vd->esc(@$vd->contact->first_name) ?>" />
									</div>
									<div class="col-xs-12 col-sm-12 col-md-6">
										<input class="form-control in-text required" type="text" 
											name="last_name" placeholder="Last Name"
											data-required-name="Last Name"
											value="<?= $vd->esc(@$vd->contact->last_name) ?>" />
									</div>
								</div>
							
								<div class="row form-group" id="email-error">
									<div class="col-md-12">
										<div class="alert alert-danger nomarbot">
											Contact already exists with this email address.
										</div>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-md-12">	
										<input class="required" type="hidden" name="email" 
											id="email" data-required-name="Email Address"
											value="<?= $vd->esc(@$vd->contact->email) ?>" />
										<input class="form-control in-text has-loader" type="email" 
											name="email-visible" id="email-visible" 
											placeholder="Email Address"
											value="<?= $vd->esc(@$vd->contact->email) ?>" />
									</div>
								</div>
								<script>
								
								$(function() {

									var is_default_company = <?= json_encode(!$vd->newsroom->company_id) ?>;
									
									var email = $("#email");
									var email_visible = $("#email-visible");
									var email_error = $("#email-error");
									var contact_id = $("#contact-id");
									
									email_visible.on("change", function() {

										email_to_check = email_visible.val();

										if (is_default_company) {
											email.val(email_to_check);
											return;
										}

										email_visible.addClass("loader");
										email.addClass("loader");
										email_visible.removeClass("error");
										email_error.slideUp();
										email.val("");
										
										var data = {};
										data.contact_id = contact_id.val();
										data.email = email_to_check;
										
										$.post("manage/contact/contact/email_check", data, function(res) {
											
											email_visible.removeClass("loader");
											email.removeClass("loader");
											if (res.available) return email.val(email_to_check);
											email_visible.addClass("error");
											email_error.slideDown();
											
										});
										
									});
									
								});
								
								</script>
							
								<div class="row form-group">
									<div class="col-md-12">
										<input class="form-control in-text" type="text" 
											name="company_name" placeholder="Company Name"
											value="<?= $vd->esc(@$vd->contact->company_name) ?>" />
									</div>
								</div>
							
								<div class="row form-group">
									<div class="col-md-12">
										<input class="form-control in-text" type="text" 
											name="title" placeholder="Title"
											value="<?= $vd->esc(@$vd->contact->title) ?>" />
									</div>
								</div>
							
								<div class="row form-group">
									<div class="col-md-12">
										<input class="form-control in-text" type="text" 
											name="phone" placeholder="Phone"
											value="<?= $vd->esc(@$vd->contact->phone) ?>" />
									</div>
								</div>
							
								<div class="row form-group">
									<div class="col-md-12">
										<input class="form-control in-text" type="text" 
											name="twitter" placeholder="Twitter"
											value="<?= $vd->esc(@$vd->contact->twitter) ?>" />
									</div>
								</div>
							
							</fieldset>
						
							<?= $ci->load->view('manage/contact/partials/contact_lists', null, true) ?>
							
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default cart" id="locked_aside">
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<div class="row form-group">
								<div class="col-md-12">
									<div id="select-country" class="select-right">
										<select class="form-control show-menu-arrow selectpicker" name="country_id">
											<option class="selectpicker-default" title="Select Country" value=""
												<?= value_if_test(!@$vd->contact->country_id, 'selected') ?>>None</option>
											<?php foreach ($common_countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->contact->country_id == $country->id), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
											<option data-divider="true"></option>
											<?php foreach ($countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->contact->country_id == $country->id && 
													!$country->is_common), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
										</select>

										<script>
										$(function() {
											
											$("#select-country select")
												.on_load_select({ size: 10 });
											
										});
										
										</script>
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-12">
									<div class="select-right select-beat">
										<select class="form-control show-menu-arrow selectpicker category" name="beat_1_id"
											data-live-search="true">
											<option class="selectpicker-default" title="Select Beat" value=""
												<?= value_if_test(!@$vd->contact->beat_1_id, 'selected') ?>>None</option>
											<?php foreach ($beats as $group): ?>
											<optgroup label="<?= $vd->esc($group->name) ?>">
												<?php foreach ($group->beats as $beat): ?>
												<option value="<?= $beat->id ?>"
													<?= value_if_test((@$vd->contact->beat_1_id == $beat->id), 'selected') ?>>
													<?= $vd->esc($beat->name) ?>
												</option>
												<?php endforeach ?>
											</optgroup>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-12">
									<div class="select-right select-beat">
										<select class="form-control show-menu-arrow selectpicker category" name="beat_2_id"
											data-live-search="true">
											<option class="selectpicker-default" title="Select Beat" value=""
												<?= value_if_test(!@$vd->contact->beat_2_id, 'selected') ?>>None</option>
											<?php foreach ($beats as $group): ?>
											<optgroup label="<?= $vd->esc($group->name) ?>">
												<?php foreach ($group->beats as $beat): ?>
												<option value="<?= $beat->id ?>"
													<?= value_if_test((@$vd->contact->beat_2_id == $beat->id), 'selected') ?>>
													<?= $vd->esc($beat->name) ?>
												</option>
												<?php endforeach ?>
											</optgroup>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-12">
									<div class="select-right select-beat">
										<select class="form-control show-menu-arrow selectpicker category" name="beat_3_id"
											data-live-search="true">
											<option class="selectpicker-default" title="Select Beat" value=""
												<?= value_if_test(!@$vd->contact->beat_3_id, 'selected') ?>>None</option>
											<?php foreach ($beats as $group): ?>
											<optgroup label="<?= $vd->esc($group->name) ?>">
												<?php foreach ($group->beats as $beat): ?>
												<option value="<?= $beat->id ?>"
													<?= value_if_test((@$vd->contact->beat_3_id == $beat->id), 'selected') ?>>
													<?= $vd->esc($beat->name) ?>
												</option>
												<?php endforeach ?>
											</optgroup>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>

							<script>
							$(function() {
								
								$("div.select-beat select")
									.on_load_select({ size: 10 });
								
							});
							
							</script>
							
							<div class="row form-group">
								<div class="col-md-4 col-md-offset-8">
										<button type="submit" name="publish" value="1" 
											class="btn btn-primary pull-right nomar">Save</button>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>

				<?php 

					$loader = new Assets\JS_Loader(
						$ci->conf('assets_base'), 
						$ci->conf('assets_base_dir'));
					$loader->add('js/required.js');
					$loader->add('lib/jquery.lockfixed.js');
					$render_basic = $ci->is_development();
					$ci->add_eob($loader->render($render_basic));

				?>
				
				<script>
				
				$(function() {

					if (is_desktop()) {
						var options = { offset: { top: 100 } };
						$.lockfixed("#locked_aside", options);
					}
					
				});
				
				</script>
				
			</div>
		</div>
	</div>
	</form>
</div>