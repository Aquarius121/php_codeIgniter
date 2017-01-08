<?php $meta = ($image && $image->meta_data
	? Raw_Data::from_object(json_decode($image->meta_data)) 
	: new Raw_Data()) ?>

<li class="<?= value_if_test($image, 's-existing', 's-select') ?>
	        <?= value_if_test($featured, 'featured') ?>
	        web-images-item-li">
	<a class="relative images-list-item related-image
		<?= value_if_test($image, 's-existing', 's-select') ?>
		<?= value_if_test($featured, 'featured') ?>">
		<div class="requires-premium web-image-overlay"></div>
		<input type="hidden" name="image_ids[<?= $index ?>]" 
			class="image_id web-image-input" value="<?= @$image->id ?>" />
		<?php if ($featured): ?>
			<input type="hidden" class="cover_image_id image_id" 
				name="cover_image_id" value="<?= @$image->id ?>" />
			<span class="featured-img-label"></span>
		<?php endif ?>
		<p class="select-image s-select">
			<span class="select-image-content">
				<i class="fa fa-plus"></i>
				Add Image
			</span>
			<input class="real-file required-no-submit" type="file" name="image" />
		</p>
		<span class="select-image s-progress">
			<span class="img-progress-panel">
				<span class="progress-block">
					<span class="progress-value"></span>
				</span>
				<span class="progress-label">Uploading</span>
			</span>			
			<span class="images-list-item-abort">
				<button type="button" class="btn btn-xs">Abort</button>
			</span>
		</span>
		<span class="s-existing">
			<?php if ($image): ?>
			<?php $web_filename = $image->variant('web')->filename; ?>
			<img src="<?= Stored_Image::url_from_filename($web_filename) ?>" />
			<?php else: ?>
			<img />
			<?php endif ?>
			<span class="images-list-item-remove">
				<button type="button" class="btn btn-xs">Remove</button>
			</span>
		</span>
	</a>
	<div class="web-image-meta">
		<div class="meta-line">
			<input type="text" class="form-control web-image-meta-alt"
				name="image_meta_data[alt][<?= $index ?>]" placeholder="Image Title"
				value="<?= $vd->esc($meta->alt) ?>" />
		</div>
		<div class="meta-line">
			<textarea type="text" class="form-control web-image-meta-caption"
				name="image_meta_data[caption][<?= $index ?>]" placeholder="Image Description"
				rows="2"><?= $vd->esc($meta->caption) ?></textarea>
		</div>		
		<?php if (!empty($meta_extension) &&
			is_array($meta_extension) && 
			count($meta_extension)): ?>
			<?php foreach ($meta_extension as $view): ?>
				<?= $this->load->view($view, array(
					'image' => $image, 
					'featured' => $featured,
					'index' => $index)) ?>
			<?php endforeach ?>
		<?php endif ?>
	</div>
</li>