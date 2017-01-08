<?php $raw_data = $content->m_pb_social->raw_data_object(); ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-linkedin">
	<div class="inner feed-linkedin">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-linkedin-square"></i></span>
			<a href="https://linkedin.com/<?= $vd->esc($vd->nr_profile->soc_linkedin) ?>" target="_blank" class="no-custom intro-posted">
				POSTED</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if ($raw_data->post_id): ?>			
			<?php if (!empty($raw_data->updateContent->companyStatusUpdate->share->content->submittedImageUrl)): ?>
			<span class="section-thumb">
				<a target="_blank" href="https://linkedin.com/<?= $vd->esc($vd->nr_profile->soc_linkedin) ?>" title="<?= $vd->esc($content->title) ?>">
					<img src="<?= $vd->esc(URL::secure($raw_data->updateContent->companyStatusUpdate->share->content->submittedImageUrl)) ?>">
				</a>
			</span>
			<?php endif ?>
		<?php endif ?>
		
		<?php if ($raw_data->description && !is_object($raw_data->description)): ?>
			<span class="section-text">
				<?= $vd->esc($vd->cut($raw_data->description, 125)) ?>
			</span>
		<?php elseif ($content->title): ?>
			<?= $vd->esc($vd->cut($content->title, 125)) ?>
		<?php endif ?>

	</div>
</div>