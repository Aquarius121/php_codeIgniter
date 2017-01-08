<article class="article">

	<header class="article-header">
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<section class="article-content clearfix">
		<?php $cover_image = Model_Image::find($vd->m_content->cover_image_id); ?>
		<?php if ($cover_image): ?>
			<?php $orig_variant = $cover_image->variant('original'); ?>
			<?php $ci_variant = $cover_image->variant('view-cover'); ?>
			<?php $ci_filename = $ci_variant->filename; ?>
			<?php $meta = json_decode($cover_image->meta_data); ?>
			<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
				class="use-lightbox floated-featured-image"
				data-caption="<?= $vd->esc(@$meta->caption) ?>">
			<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
				alt="<?= $vd->esc($vd->m_content->title) ?>" class="add-border" /></a>
		<?php endif ?>
		<?= $ci->load->view('browse/view/partials/article_info') ?>
		<?php if ($vd->m_content->summary): ?>
		<p class="article-summary"><?= $vd->esc($vd->m_content->summary) ?></p>
		<?php endif ?>
		<?= $ci->load->view('browse/view/partials/supporting_quote',
			array('m_content' => $vd->m_content)) ?>
		<div class="marbot-15 html-content"><?= $ci->load->view('browse/view/html-content') ?></div>
		<?= $ci->load->view('browse/view/partials/share-bottom') ?>
	</section>
	
	<section class="resources-block event-block event-details">	
		<h3>Event <strong>Details</strong></h3>
		<?php if ($vd->m_content->address) : ?>
			<p>
				<i class="fa fa-globe"></i><!-- 
			--><span>Place:</span> <strong><?= $vd->esc($vd->m_content->address) ?></strong>
			</p>
		<?php endif ?>
		<p>
			
			<?php $event_start_date = Date::out($vd->m_content->date_start); ?>
			<?php $event_end_date = Date::out($vd->m_content->date_finish); ?>
			<?php $show_year = $event_start_date < Date::$now || $event_start_date > Date::months(3); ?>
		
			<?php if ($vd->m_content->is_all_day): ?>
			<i class="fa fa-calendar"></i><span>Date:</span>
			<?php else: ?>
			<i class="fa fa-calendar"></i><span>Starts:</span> 
			<?php endif ?>
			
			<strong>
			<?php if ($show_year): ?>
				<?php if ($event_start_date->format("G:i") === "0:00" && $vd->m_content->is_all_day): ?>
					<?= $event_start_date->format("jS F Y") ?>
				<?php else: ?>
					<?= $event_start_date->format("jS F Y g:i A") ?>
				<?php endif; ?>
			<?php else: ?>
				<?php if ($event_start_date->format("G:i") === "0:00" && $vd->m_content->is_all_day): ?>
					<?= $event_start_date->format("jS F") ?>
				<?php else: ?>
					<?= $event_start_date->format("jS F g:i A") ?>
				<?php endif; ?>
			<?php endif ?>
			</strong>
			
			<?php if (!$vd->m_content->is_all_day): ?>
				<br /><i class="fa fa-blank"></i><span>Ends:</span> 
				<strong>
				<?php if ($show_year): ?>
					<?= $event_end_date->format("jS F Y g:i A") ?>
				<?php else: ?>
					<?= $event_end_date->format("jS F g:i A") ?>
				<?php endif ?>
				</strong>
			<?php endif ?>
			
		</p>
		<p>
			<i class="fa fa-shopping-cart"></i><span>Price:</span> 
			<?php if((float) $vd->m_content->price > 0): ?>
				<strong>$<?= $vd->esc($vd->m_content->price) ?></strong>
				<?php if($vd->m_content->discount_code): ?>
					(Discount Code: <i><?= $vd->esc(
						$vd->m_content->discount_code) ?></i>)
				<?php endif; ?>
			<?php else: ?>
				<strong>Free</strong>
			<?php endif ?>
		</p>
		<?php $event_type = Model_Event::find($vd->m_content->event_type_id) ?>
		<?php if ($event_type) : ?>
			<p>
				<i class="fa fa-briefcase"></i><!-- 
			--><span>Type:</span> <strong><?= $vd->esc($event_type->name) ?></strong>
			</p>
		<?php endif; ?>
	</section> 

	<?= $ci->load->view('browse/view/partials/additional_images') ?>
	<?= $ci->load->view('browse/view/partials/links_tags') ?>

</article>