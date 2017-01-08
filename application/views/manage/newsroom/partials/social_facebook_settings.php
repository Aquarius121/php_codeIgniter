<div class="row">
	<div class="col-lg-12" id="social-services-list">

		<fieldset>
			<legend>Facebook Authorization</legend>
			
			<?php if ($vd->facebook_auth && $vd->facebook_auth->is_valid()): ?>
			<div class="row form-group">
				<div class="col-lg-12 social-auth-text">
					Facebook authorization is enabled for
					<strong><?= $vd->facebook_name ?></strong>.
				</div>
			</div>
			<div class="row form-group">
				<div class="col-lg-12">
					<div class="alert alert-success alert-dismissible pad-5v marbot-10 feedback dnone" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						Saved successfully</div>
					<div class="col-lg-6 col-md-6 nopad">
					<select id="select-facebook-page" class="form-control selectpicker show-menu-arrow smaller" name="page">
						<option value="">Personal Timeline</option>
						<?php foreach ($vd->facebook_pages as $page): ?>
						<option value="<?= $page->id ?>" 
							<?= value_if_test($page->id == $vd->facebook_auth->page, 'selected') ?>>
							<?= $vd->esc($page->name) ?></option>
						<?php endforeach ?>
					</select>
					</div>
					<div class="col-lg-6 col-md-6">
						<button id="set-page" type="button"
							class="btn btn-primary pad-20h btn-set-page">Set</button>
					</div>

					<script>

						$(function() {
							
							var select_facebook_page = $("#select-facebook-page");
							var btn_set_page = $(".btn-set-page");
							// select_facebook_page.on_load_select();

							btn_set_page.on("click", function(){
								var value = select_facebook_page.val();
								if (!value) return;
								btn_set_page.attr("disabled", true);
								var data = { page: value };
								$.post("manage/newsroom/social/facebook_page", 
									data, do_after_save);
							});

							var do_after_save = function() {
								$(".feedback").removeClass("dnone");
								btn_set_page.attr("disabled", false);
							};

						});

					</script>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-lg-12 social-auth-text">
					<button class="btn btn-small btn-danger auth-remove-btn" 
						data-media='<?= Model_PB_Social::TYPE_FACEBOOK ?>' type="button">
						Remove Authorization
					</button>
				</div>
			</div>
			<?php endif ?>
		</fieldset>
	</div>
</div>