<div class="row-fluid marbot-20">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Distribution Sites</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid fin-services-grid">
				<thead>
					
					<tr>
						<th class="left">Site</th>
						<th></th>
						<th>Image</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result <?= value_if_test($result->logo_image_id, 'has-im') ?>" 
						data-hash="<?= $result->hash ?>"
						data-name="<?= $vd->esc($result->name) ?>"
						data-url="<?= $vd->esc($result->url) ?>">
						<td class="left">							
							<h3 class="nopadbot">	
								<span class="label-class"><?= strtoupper(substr($result->hash, 24)) ?></span>							
								<span class="ds-name">
									<?php if ($result->name): ?>
										<?= $vd->esc($result->name) ?>
									<?php else: ?>
										Unknown
									<?php endif ?>
								</span>
							</h3>
							<?php if ($result->url): ?>
								<span class="status-info-muted ds-url">
								<?= $vd->esc(URL::nice($result->url)) ?>								
								</span>
							<?php else: ?>
								<span class="status-false ds-url">
								website unavailable
								</span>
							<?php endif ?>
						</td>	
						<td>
							<a class="a-edit-name" href="#">Edit Name</a> |
							<a class="a-edit-url" href="#">Update URL</a>
						</td>	
						<td>
							<a class="a-upload" href="#">Upload</a>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Sites
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
			
			<script>
			
			$(function() {
			
				$(document).on("click", ".a-upload", function(ev) {
					var result = $(this).parents(".result");
					var fake_in = $.create("input");
					fake_in.attr("name", "file");
					fake_in.attr("type", "file");
					fake_in.on("change", function() {
						var hash = result.data("hash");
						result.addClass("loader");
						fake_in.ajax_upload({
							callback: function() { 
								result.removeClass("loader");
								result.addClass("has-im");
							},
							url: "admin/settings/distribution_sites/upload",
							data: { hash: hash }
						});
					});
					fake_in.click();
					return false;
				});

				$(document).on("click", ".a-edit-name", function(ev) {
					
					ev.preventDefault();
					var result = $(this).parents(".result");
					var hash = result.data("hash");
					var name = result.data("name");
					result.addClass("loader");

					var title = "Enter the distribution site name:";
					
					var callback = function(value) {
						if (!value) return;
						var name = "admin/settings/distribution_sites/set_name";
						$.post(name, { name: value, hash: hash }, function() {
							result.removeClass("loader");
							result.find(".ds-name").text(value);
						});
					};

					bootbox.prompt({
						title : title,
						value : name,
						callback : callback
					});

				});

				$(document).on("click", ".a-edit-url", function(ev) {

					ev.preventDefault();
					var result = $(this).parents(".result");
					var hash = result.data("hash");
					var url = result.data("url");
					result.addClass("loader");

					var title = "Enter the distribution site url:";
					var callback = function(value) {
						if (!value) return;
						var url = "admin/settings/distribution_sites/set_url";
						$.post(url, { url: value, hash: hash }, function() {
							result.removeClass("loader");
							var nice = value;
							nice = nice.replace(/^https?:\/\//i, "");
							nice = nice.replace(/(\.[a-z0-9]+)\/$/i, "$1");
							result.find(".ds-url").text(nice)
								.removeClass("status-false")
								.addClass("status-info-muted");
						});
					};

					//bootbox.prompt(title, 'Cancel', 'Confirm', 
					//	callback, url);

					bootbox.prompt({
						title : title,
						value : url,
						callback : callback
					});

				});
				
			});
			
			</script>
		
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>