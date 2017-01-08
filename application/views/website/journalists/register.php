<?php 
	
	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/wireupdate.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Journalist Registration</h1>
				</header>
			</div>
			<div class="col-sm-1"></div>
		</div>
	</div>
</main>

<article class="article">
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<form class="col-sm-8 required-form" method="post" action="journalists/register/save">

				<section class="marbot-25 ta-center">
					<strong>Step 1</strong>: Register &#10132;
					<span class="muted">
						Step 2: Activate
					</span>
				</section>

				<div class="wu-form-block-container">
					<section class="wu-form-block blue marbot-30">
						<strong class="form-block-label">Personal Information</strong>
						<div class="row">
							<div class="col-sm-6">
								<strong class="field-label star">Email</strong>
								<input type="email" class="form-control required" 
									data-required-name="Email Address" name="email" />
								<div class="help-block">
									Please use a company address if you have one.
								</div>
							</div>
							<div class="col-sm-6">
								<strong class="field-label">Phone Number</strong>
								<input type="text" class="form-control" name="phone" />
								<div class="help-block">
									Your phone number is kept confidential and will not be provided to any 3rd party.
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<strong class="field-label star">First Name</strong>
								<input type="text" class="form-control required" 
									data-required-name="First Name" name="first_name" />
							</div>
							<div class="col-sm-6">
								<strong class="field-label star">Last Name</strong>
								<input type="text" class="form-control required" 
									data-required-name="Last Name" name="last_name" />
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<strong class="field-label star">Country You Are In</strong>
								<select class="form-control nomarbot required" id="select-country"
									name="country" data-required-name="Country">
								</select>
							</div>
							<div class="col-sm-6">
								<strong class="field-label" id="select-region-label">State</strong>
								<select class="form-control nomarbot"
									name="region" id="select-region">
								</select>
							</div>
							<script>

							$(function() {

								var countries = <?= json_encode($vd->countries) ?>;
								var country_data = <?= json_encode($vd->country_data) ?>;

								var default_country_id = <?= json_encode(Model_Country::ID_UNITED_STATES) ?>;
								var default_state_id = <?= json_encode(Model_State::ID_NY) ?>;

								var country_select = $("#select-country");
								var region_select = $("#select-region");

								var create_option = function(value, text, selected) {
									var option = $.create("option");
									option.prop("selected", selected);
									option.attr("value", value);
									option.text(text);
									return option;
								};

								var render_country = function(country_id) {
									var country = country_data[country_id];
									var option = create_option(country.id, country.name,
										default_country_id == country_id);
									country_select.append(option);
								};

								var render_region = function(region) {
									var option = create_option(region.id, region.name,
										default_state_id == region.id);
									region_select.append(option);
								};

								var render_region_select = function(country_id) {
									region_select.empty();
									region_select.prop("disabled", true);
									var country = country_data[country_id];
									var regions = country.regions;
									if (!regions) return;
									for (var i = 0; i < regions.length; i++) 
										render_region(regions[i]);
									region_select.prop("disabled", false);
								};

								for (var i = 0; i < countries.length; i++)
									render_country(countries[i]);

								country_select.on("change", function() {
									var country_id = country_select.val();
									render_region_select(country_id);
									var region_label = $("#select-region-label");
									region_label.text(country_id == default_country_id
										? "State" : "Region");
								});

								render_region_select(default_country_id);

							});

							</script>
						</div>
					</section>
				</div>

				<div class="wu-form-block-container">
					<section class="wu-form-block yellow marbot-30">
						<strong class="form-block-label">You are a ...</strong>
						<div class="row">
							<div class="col-sm-6">
								<select class="form-control nomarbot required" id="contact-role"
									name="contact_role" data-required-name="Role">
									<option value="" disabled selected>Please Select</option>
									<?php foreach ($vd->contact_roles as $contact_role): ?>
										<option value="<?= $contact_role->id ?>" 
											data-has-job-title="<?= (int) $contact_role->has_job_title ?>">
											<?= $vd->esc($contact_role->role) ?>
										</option>
									<?php endforeach ?>
								</select>
								</select>
							</div>
							<div class="col-sm-1">
								<strong class="field-label star"></strong>
							</div>
						</div>
					</section>
				</div>

				<script>

				$(function() {

					var section_org = $("#organization-information");
					var section_other = $("#other-information");
					var section_beats = $("#select-beats");
					var contact_role = $("#contact-role");

					contact_role.on("change", function() {
						section_other.removeClass("hidden");
						section_org.removeClass("hidden");
						section_beats.removeClass("hidden");
						var selected = contact_role.children(":selected");
						var has_job_title = !! parseInt(selected.data("has-job-title"));
						if (has_job_title) {
							section_org.find(".job-title input")
								.addClass("required")
								.val($.trim(selected.text()));
							section_org.find(".job-title .field-label")
								.addClass("star");
						} else {
							section_org.find(".job-title input")
								.removeClass("required")
								.val("");
							section_org.find(".job-title .field-label")
								.removeClass("star");
						}
					});

				});

				</script>

				<div class="wu-form-block-container hidden" id="organization-information">
					<section class="wu-form-block yellow marbot-30">
						<strong class="form-block-label">Organization Information</strong>
						<div class="row">
							<div class="col-sm-6">
								<strong class="field-label star">Organization You Work For</strong>
								<input type="text" class="form-control required" 
									data-required-name="Organization" name="company_name" />
							</div>
							<div class="col-sm-6 job-title">
								<strong class="field-label">Job Title</strong>
								<input type="text" class="form-control" 
									data-required-name="Job Title" name="title" />
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8">
								<strong class="field-label star">Organization Type</strong>
								<select name="contact_media_type" class="form-control required nomarbot"
									data-required-name="Organization Type">
									<option value="" disabled selected>Select Organization Type</option>
									<?php foreach ($vd->contact_media_types as $contact_media_type): ?>
										<option value="<?= $contact_media_type->id ?>">
											<?= $vd->esc($contact_media_type->media_type) ?>
										</option>
									<?php endforeach ?>
								</select>
							</div>
						</div>						
					</section>
				</div>

				<div class="wu-form-block-container hidden" id="select-beats">
					<section class="wu-form-block yellow marbot-30">
						<strong class="form-block-label">Industry of Interest</strong>
						<div class="marbot-20"><strong class="field-label star">Select Your Industries of Interest</strong></div>
						<div class="marbot-20">
							<a href="#" id="beats-plus-all" style="margin-right: 15px"><strong>Expand All</strong></a>
							<a href="#" id="beats-minus-all"><strong>Collapse All</strong></a>
						</div>
						<input type="hidden" data-required-name="Industry of Interest"
							class="required" name="beats" value="" id="beats-list" />
						<div class="row">
							<?php foreach ($vd->beats as $group): ?>
							<div class="col-sm-6 beat-group">
								<div class="beat-group-header">
									<span class="plus">+</span>
									<span class="minus">-</span>
									<label>
										<input type="checkbox" class="beat-group-cb" />
										<?= $vd->esc($group->name) ?>
									</label>
								</div>
								<div class="beats">
									<?php foreach ($group->beats as $beat): ?>
										<label class="block">
											<input type="checkbox" class="beat-cb"
												value="<?= $beat->id ?>" />
											<?= $vd->esc($beat->name) ?>
										</label>
									<?php endforeach ?>
								</div>
							</div>
							<?php endforeach ?>
						</div>				
					</section>
				</div>

				<script>

				$(function() {

					var select_beats = $("#select-beats");
					var beats_list = $("#beats-list");
					var expand_all = $("#beats-plus-all");
					var collapse_all = $("#beats-minus-all");

					select_beats.on("click", ".plus", function() {
						$(this).parents(".beat-group")
							.addClass("expanded")
							.addClass("col-sm-12")
							.removeClass("col-sm-6");
					});

					select_beats.on("click", ".minus", function() {
						$(this).parents(".beat-group")
							.removeClass("expanded")
							.removeClass("col-sm-12")
							.addClass("col-sm-6");
					});

					select_beats.on("change", ".beat-group-cb", function() {
						var _this = $(this);
						var beats_cb = _this.parents(".beat-group").find(".beat-cb");
						beats_cb.prop("checked", _this.is(":checked"));
						beats_cb.eq(0).trigger("change");
					});

					select_beats.on("change", ".beat-cb", function() {
						var checked = [];
						select_beats.find(".beat-cb").each(function() {
							var _this = $(this);
							if (_this.is(":checked")) 
								checked.push(_this.val());
						});
						if (checked.length)
						     beats_list.val(JSON.stringify(checked));
						else beats_list.val("");
					});

					expand_all.on("click", function() {
						select_beats.find(".beat-group")
							.addClass("expanded")
							.addClass("col-sm-12")
							.removeClass("col-sm-6");
						return false;
					});

					collapse_all.on("click", function() {
						select_beats.find(".beat-group")
							.removeClass("expanded")
							.removeClass("col-sm-12")
							.addClass("col-sm-6");
						return false;
					});

				});

				</script>

				<div class="wu-form-block-container hidden" id="other-information">
					<section class="wu-form-block blue marbot-30">
						<strong class="form-block-label">Other Information</strong>
						<div class="row">
							<div class="col-sm-6">
								<strong class="field-label">Website URL</strong>
								<input type="text" class="form-control nomarbot" name="website_url" />
							</div>
							<div class="col-sm-6">
								<strong class="field-label">Blog URL</strong>
								<input type="text" class="form-control nomarbot" name="blog_url" />
							</div>
						</div>
					</section>
				</div>

				<section class="marbot-25">
					<button type="submit" class="signup-btn">Register &#9654;</button>
				</section>

			</form>
			<div class="col-sm-2"></div>
		</div>
	</div>
</article>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/json.js');
	$render_basic = $ci->is_development();

?>

<!--[if lt IE 9]><?= $loader->render($render_basic); ?></script><![endif]-->
<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	echo $loader->render($render_basic);

?>