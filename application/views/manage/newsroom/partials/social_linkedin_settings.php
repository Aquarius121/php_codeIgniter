<div class="row">
	<div class="col-lg-12" id="social-services-list">

		<fieldset>
			<legend>Linkedin Authorization</legend>
			<?php if ($vd->linkedin_auth && $vd->linkedin_auth->is_valid()): ?>
			<div class="row form-group">
				<div class="col-lg-12 social-auth-text">
					Linkedin authorization is enabled for
					<strong><?= $vd->c_profile->soc_linkedin ?></strong>.
				</div>
			</div>

			<div class="row form-group">
				<div class="col-lg-12">

					<div class="alert alert-success alert-dismissible pad-5v marbot-10 feedback dnone" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						Saved successfully</div>

					<div class="col-lg-6 col-md-6 nopad">
					<select id="select-linkedin-company" class="form-control selectpicker show-menu-arrow smaller" name="linkedin_company_id">
						<option value="">Select a Linkedin Company</option>
						<?php foreach ($vd->linkedin_companies as $company): ?>
						<option value="<?= $company->id ?>" 
							<?= value_if_test($company->id == $vd->linkedin_auth->linkedin_company_id, 'selected') ?>>
							<?= $vd->esc($company->name) ?></option>
						<?php endforeach ?>
					</select>
					</div>
					<div class="col-lg-6 col-md-6">
						<button id="set-company" type="button"
							class="btn btn-primary pad-20h btn-set-company">Set</button>
					</div>

					<script>

						$(function() {
							
							var select_linkedin_company = $("#select-linkedin-company");
							var btn_set_company = $(".btn-set-company");
							// select_linkedin_company.on_load_select();

							btn_set_company.on("click", function(){
								var value = select_linkedin_company.val();
								if (!value) return;
								btn_set_company.attr("disabled", true);
								var data = { linkedin_company_id: value };
								$.post("manage/newsroom/social/linkedin_company", 
									data, do_after_save);
							});

							var do_after_save = function(d) {
								$(".feedback").removeClass("dnone");
								btn_set_company.attr("disabled", false);
							};

						});

					</script>
				</div>
			</div>

			
			<div class="row form-group">
				<div class="col-lg-12">
					<button class="btn btn-small btn-danger auth-remove-btn"
						type="button" data-media='<?= Model_PB_Social::TYPE_LINKEDIN ?>'>
						Remove Authorization
					</button>
				</div>
			</div>
			<?php endif ?>
		</fieldset>
	</div>
</div>