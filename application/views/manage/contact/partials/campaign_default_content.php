<?= $ci->load->view('manage/partials/email-template-css') ?>

<div class="ei-from-content">

	<span class="ei-content-view-pixel">
		<img src="((content-view-pixel))" width="1" height="1" alt="" />
	</span>

	<p class="ei-title">
		<b><?= $vd->esc($m_content->title) ?></b>
	</p>

	<?php if ($m_content->is_premium): ?>
		<?php $cover_image = Model_Image::find($m_content->cover_image_id); ?>
		<?php if ($cover_image): ?>
			<?php $orig_variant = $cover_image->variant('original'); ?>
			<?php $ci_variant = $cover_image->variant('view-cover'); ?>
			<?php $meta = json_decode($cover_image->meta_data); ?>
			<a href="<?= $ci->website_url(Stored_File::url_from_filename($orig_variant->filename)) ?>" 
				title="<?= $vd->esc(@$meta->caption) ?>" class="ei-cover-left">
				<img src="<?= $ci->website_url(Stored_File::url_from_filename($ci_variant->filename)) ?>" 
					width="<?= $ci_variant->width ?>"
					height="<?= $ci_variant->height ?>" />
			</a>
		<?php endif ?>
	<?php endif ?>

	<?php if ($m_content->summary): ?>
	<p class="ei-summary"><b><?= $vd->esc($m_content->summary) ?></b></p>
	<?php endif ?>

	<?php if ($m_content->content): ?>
	<div class="ei-html-content">		
		<?= $ci->load->view('manage/contact/partials/html-content/render', 
			array('m_content' => $m_content)) ?>
	</div>
	<?php endif ?>

	<p class="ei-source">
		For official press release, please <a href="((tracking-link))">click here</a>.
	</p> 

</div>	