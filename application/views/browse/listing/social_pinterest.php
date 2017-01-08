<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-pinterest">
	<div class="inner feed-pinterest">
		
		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-pinterest-square"></i></span>
			<a href="<?= $vd->esc($raw_data->link) ?>" target="_blank"  class="no-custom intro-posted">
			Pinned</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if ($raw_data->picture): ?>
			<span class="section-thumb">
				<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>">
					<img src="<?= $vd->esc($raw_data->picture) ?>">
				</a>
			</span>
		<?php endif ?>
		
		<?php if ($raw_data->post_description_text && !is_object($raw_data->post_description_text)): ?>
			<span class="section-text">
				<?= $vd->esc($vd->cut($raw_data->post_description_text, 125)) ?>				
			</span>
		<?php endif ?>
		
	</div>
</div>