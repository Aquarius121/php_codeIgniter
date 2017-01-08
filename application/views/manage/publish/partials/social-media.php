<?php if (Auth::user()->is_free_user()): ?> 
<fieldset class="social-media section-requires-premium">
<?php else: ?>
<fieldset class="social-media">
<?php endif ?>

	<legend>
		Social Media Sharing
		<a data-toggle="tooltip" class="tl" href="#" 
			title="<?= Help::WEB_SOCIAL ?>">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>	
	</legend>

	<?php if (Auth::user()->is_free_user()): ?>
		<?= $ci->load->view('manage/publish/partials/requires-premium') ?>
	<?php endif ?>

	<?php if (@$vd->social->twitter): ?>
	<div class="row form-group">
		<div class="col-lg-12">
			<label class="checkbox-container">
				<input type="checkbox" name="post_to_twitter" value="1" 
					<?= value_if_test(@$vd->m_content->is_social_locked_twitter, 'disabled') ?>
					<?= value_if_test(@$vd->m_content->post_to_twitter || 
						(@$vd->m_content->is_social_locked_twitter && 
						 @$vd->m_content->post_id_twitter), 'checked') ?> /> 
				<span class="checkbox"></span>
				Post this content to Twitter
			</label>
		</div>
	</div>
	<?php endif ?>

	<?php if (@$vd->social->facebook): ?>
	<div class="row form-group">
		<div class="col-lg-12">
			<label class="checkbox-container">
				<input type="checkbox" name="post_to_facebook" value="1" 
					<?= value_if_test(@$vd->m_content->is_social_locked_facebook, 'disabled') ?>
					<?= value_if_test(@$vd->m_content->post_to_facebook ||
						(@$vd->m_content->is_social_locked_facebook && 
						 @$vd->m_content->post_id_facebook), 'checked') ?> /> 
				<span class="checkbox"></span>
				Post this content to Facebook
			</label>
		</div>
	</div>	
	<?php endif ?>

	<div class="row form-group">
		<div class="col-lg-12">
			<div class="configure-social">
				<a href="manage/newsroom/social" target="_blank">Manage Accounts</a>
			</div>
		</div>
	</div>

</fieldset>