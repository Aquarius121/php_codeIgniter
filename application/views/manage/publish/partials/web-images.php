<?php 

$has_cover_image = false;
$image_item_count = 4;
$image_item_per_line = 4;

if ($vd->image_item_count)
	$image_item_count = $vd->image_item_count;
if ($vd->image_item_per_line)
	$image_item_per_line = $vd->image_item_per_line;

?>

<fieldset class="web-images nomarbot">
	<legend>
		Add Related Images 
		<a data-toggle="tooltip" class="tl" href="#" 
			title="<?= Help::WEB_IMAGES ?>">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>	
	</legend>
	<div class="header-help-block">Attach up to <?= $image_item_count ?> images related to the content.</div>
	<?= $ci->load->view('manage/publish/partials/upload-error') ?>
	<?= $ci->load->view('manage/publish/partials/min-size-warning') ?>
	<div class="row form-group">
		<div class="col-lg-12">
			<ul class="nopad related-images images-uploader-list" id="web-images-list">
				<?php $index = 0; ?>
				<?php if ($vd->m_content): ?>
					<?php foreach ($vd->m_content->get_images() as $image): ?>
						<?php if ($vd->m_content->cover_image_id == $image->id): ?>
						<?php $has_cover_image = true; $image_item_count--; ?>
						<?= $ci->load->view('manage/publish/partials/web-images-item.php', 
							array('image' => $image, 'featured' => true, 
								'meta_extension' => $meta_extension, 
								'index' => $index++)); ?>
						<?php endif ?>
					<?php endforeach ?>
					<?php if (!$has_cover_image): ?>
					<?php $image_item_count--; ?>
					<?= $ci->load->view('manage/publish/partials/web-images-item.php', 
						array('image' => null, 'featured' => true, 
								'meta_extension' => $meta_extension, 
								'index' => $index++)); ?>
					<?php endif ?>
					<?php foreach ($vd->m_content->get_images() as $image): ?>
						<?php if ($vd->m_content->cover_image_id != $image->id): ?>
						<?php $image_item_count--; ?>
						<?= $ci->load->view('manage/publish/partials/web-images-item.php', 
							array('image' => $image, 'featured' => false, 
								'meta_extension' => $meta_extension, 
								'index' => $index++)); ?>
						<?php endif ?>
					<?php endforeach ?>
				<?php endif ?>
				<?php for ($i = 0; $i < $image_item_count; $i++): ?>
					<?= $ci->load->view('manage/publish/partials/web-images-item.php', 
						array('image' => null, 'featured' => ($i === 0 && !$vd->m_content), 
								'meta_extension' => $meta_extension, 
								'index' => $index++)); ?>
				<?php endfor ?>
			</ul>
		</div>
	</div>
		
	<script>
			
	defer(function() {
		
		var ci_upload = $("#web-images-list");
		var item_per_line = <?= json_encode($image_item_per_line) ?>;
		var min_size_warning = $(".iul-min-size-warning");
		var upload_error = $(".iul-upload-error");
		
		var update_visible = function() {
			var detached = ci_upload.find("li.s-select").detach();
			var featured = detached.filter(".featured");
			if (featured.size()) {
				featured.show();
				ci_upload.append(featured);
				detached = detached.not(featured);
				detached.slice(0, (item_per_line-1)).show();
				detached.slice((item_per_line-1)).hide();
				ci_upload.append(detached);
			} else {
				detached.slice(0, item_per_line).show();
				detached.slice(item_per_line).hide();
				ci_upload.append(detached);
			}
		};
		
		update_visible();
		
		ci_upload.find(".real-file").on("change", function() {

			var real_file = $(this);
			var container = real_file.parents(".images-list-item");
			var li_container = container.parent();
			var preview_image = container.find(".s-existing img");
			var image_id_input = container.find("input.image_id");
			var meta_input = container.find("input.meta_data");
			var progress_value = container.find(".progress-value");
			var abort_button = container.find(".images-list-item-abort button");
			progress_value.removeClass("status-wait");
			
			var variants = ["finger", "web", "view-web"];
			if (container.hasClass("featured")) {
				variants.push("cover");
				variants.push("view-cover");
				variants.push("cover-website");
				variants.push("cover-feed");
			}
			
			container.removeClass("s-select-error");
			li_container.removeClass("s-select");
			li_container.addClass("s-progress");
			container.removeClass("s-select");						
			container.addClass("s-progress");			
			image_id_input.val("");
			meta_input.val("");

			upload_error.hide();			
			update_visible();
			
			var callback = function(res) {

				if (res && res.status) {
					
					progress_value.addClass("status-wait");
					new_preview_image = $.create("img");
					new_preview_image.on("load", function() {
						preview_image.replaceWith(new_preview_image);
						preview_image = new_preview_image;
						li_container.removeClass("s-progress");
						li_container.addClass("s-existing");
						container.removeClass("s-progress");
						container.addClass("s-existing");
					});
					
					new_preview_image.attr("src", res.files.web);
					image_id_input.val(res.image_id);

					var min_size = <?= json_encode($ci->conf('min-image-size')) ?>;
					if (res.size.height < min_size.height || res.size.width < min_size.width)
						min_size_warning.show();

				} else {

					if (!res || !res.cancelled) {
						upload_error.show();
						container.addClass("s-select-error");
					}
					
					li_container.removeClass("s-progress");
					li_container.addClass("s-select");
					container.removeClass("s-progress");
					container.addClass("s-select");
					real_file.attr("disabled", false);
					update_visible();

				}
				
			};
			
			var xhr = real_file.ajax_upload({
				callback: callback,
				url: "manage/image/upload",
				data: { variants: variants },
				progress: function(ev) {
					var percent = Math.round((ev.loaded / ev.total) * 100);
					progress_value.css("width", percent + "%");
				}
			});

			abort_button.off("click.abort");
			abort_button.on("click.abort", (function(xhr, callback) {

				return function() { 
					if (xhr && xhr.abort)
						xhr.abort();
					callback({ cancelled: true });
				};

			})(xhr, callback));

		});
		
		ci_upload.find(".images-list-item-remove").on("click", function() {
			
			var container = $(this).parents(".images-list-item");
			var li_container = container.parent();
			
			li_container.removeClass("s-existing");
			li_container.addClass("s-select");
			container.removeClass("s-existing");
			container.addClass("s-select");

			container.find("input.image_id").val("");
			li_container.find(".web-image-meta-alt").val("");
			li_container.find(".web-image-meta-caption").val("");

			update_visible();
			
		});
		
	});
	
	</script>

	<?php if (!empty($extension) &&
		is_array($extension) && 
		count($extension)): ?>
		<?php foreach ($extension as $view): ?>
			<?= $this->load->view($view) ?>
		<?php endforeach ?>
	<?php endif ?>
	
</fieldset>