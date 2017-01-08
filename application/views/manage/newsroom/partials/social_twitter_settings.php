<div class="row">
	<div class="col-lg-12" id="social-services-list">

		<fieldset>
			<?php if ($vd->twitter_auth && $vd->twitter_auth->is_valid()): ?>
			<div class="row form-group">
				<div class="col-lg-12 social-auth-text">
					Twitter authorization is enabled for
					<strong><?= $vd->twitter_name ?></strong>.
				</div>
			</div>
			<div class="row form-group">
				<div class="col-lg-12">
					<button class="btn btn-small btn-danger auth-remove-btn"
						type="button" data-media='<?= Model_PB_Social::TYPE_TWITTER ?>'>
						Remove Authorization
					</button>
				</div>
			</div>
			<?php endif ?>
		</fieldset>
	</div>
</div>