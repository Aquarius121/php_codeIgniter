<?php if ($vd->show_intro_video_for_ac_nr && $vd->is_auto_built_unclaimed_nr): ?>
<?php $ci->add_eob($ci->load->view('browse/claim_nr/partials/nr_intro')) ?>
<?php endif ?>
	
<div class="main-content">
	<section class="latest-news">

		<?php if ($vd->type && $vd->type == Model_Content::TYPE_SOCIAL): ?>
			<?= $ci->load->view('browse/partials/social-filter-bar') ?>
		<?php endif ?>
		
		<header class="ln-header">			
			<?php if (isset($vd->ln_header)): ?>
			<h2>
				<span class="ln-header-text">
					<?= $vd->esc($vd->ln_header) ?>
				</span>
			</h2>
			<?php elseif (isset($vd->ln_header_html)): ?>
			<h2>
				<span class="ln-header-text">
					<?= $vd->ln_header_html ?>
				</span>
			</h2>
			<?php endif ?>			
		</header>

		<?php if (!count($vd->results)): ?>
			<?php if ($vd->is_auto_built_unclaimed_nr && $vd->type == Model_Content::TYPE_PR): ?>
				<?= $ci->load->view('browse/claim_nr/listing-pr') ?>
			<?php elseif ($vd->is_auto_built_unclaimed_nr && $vd->type == Model_Content::TYPE_NEWS): ?>
				<?= $ci->load->view('browse/claim_nr/listing-news') ?>
			<?php elseif ($vd->is_auto_built_unclaimed_nr && $vd->type == Model_Content::TYPE_EVENT): ?>
				<?= $ci->load->view('browse/claim_nr/listing-event') ?>
			<?php elseif ($vd->is_auto_built_unclaimed_nr && $vd->type == Model_Content::TYPE_IMAGE): ?>
				<?= $ci->load->view('browse/claim_nr/listing-image') ?>
			<?php elseif ($vd->is_auto_built_unclaimed_nr && $vd->type == Model_Content::TYPE_BLOG): ?>
				<?= $ci->load->view('browse/claim_nr/listing-blog') ?>
			<?php endif ?>
		<?php endif ?>

		<?php if ($vd->impressions_uri): ?>
		<img src="<?= $vd->esc($vd->impressions_uri) ?>"
			width="1" height="1" class="stat-pixel" />
		<?php endif ?>

		<?php if ($vd->type && $vd->type == Model_Content::TYPE_SOCIAL): ?>
			<div id="ln-container" class="masonry social-wire">
		<?php else: ?>
			<div id="ln-container" class="columnize">
		<?php endif ?>

			<?php if (count($vd->results)): ?>

				<?php foreach ($vd->results as $result): ?>
				<?= $ci->load->view("browse/listing/{$result->type}", 
					array('content' => $result)); ?>
				<?php endforeach ?>

			<?php elseif ($vd->is_auto_built_unclaimed_nr): ?>
			<?php else: ?>

				<div class="simple-content-area inner-content">
					<h4>No Data</h4><hr />
					<p>No content was found in this section. 
						Why not try another?</p>
				</div>
				
			<?php endif ?>
			
		</div>	
	</section>	
</div>

<?php if ($vd->newsroom_main_page || $vd->type == Model_Content::TYPE_SOCIAL): ?>
<?= $ci->load->view('browse/partials/socialwirejs'); ?>
<?php endif ?>
