<?php if ($vd->is_auto_built_unclaimed_nr): ?>

	<?php if ($ci->session->get('ac_nr_tokened_visit_nr_id')
		&& $ci->session->get('ac_nr_tokened_visit_nr_id') == $ci->newsroom->company_id): ?>

		<section class="top-panel <?= value_if_test($ci->is_common_host, 'marbot') ?>"
			id="locked_aside" style="z-index: 2000">

			<?php

				$loader = new Assets\JS_Loader(
					$ci->conf('assets_base'), 
					$ci->conf('assets_base_dir'));
				$loader->add('lib/jquery.lockfixed.js');
				$render_basic = $ci->is_development();
				$ci->add_eob($loader->render($render_basic));

			?>
			<script>

			$(function() {
				var options = { offset: { top: -1 } };
				$.lockfixed("#locked_aside", options);
			});

			</script>

			<div class="bs3-container">
				<div class="row-fluid">
				<?php if ($ci->is_common_host): ?>

					<?php if ($vd->is_private_preview): ?>
						<div class="span10">
							<div class="private_preview_msg pull-right">
								IMPORTANT: This is your Company Newsroom - It's not yet public.
								<a href="#">Click here</a> to request FREE activation.
							</div>
						</div>
					<?php elseif ($vd->is_auto_built_unclaimed_nr): ?>
						<?php if($ci->session->get('ac_nr_tokened_visit_nr_id')
							&& $ci->session->get('ac_nr_tokened_visit_nr_id') == $ci->newsroom->company_id): ?>
							<div class="span10">
								<div class="private_preview_msg">
									<b><a href="browse/claim_nr" class="strong">THIS NEWSROOM IS NOT ACTIVE | CLICK HERE TO ACTIVATE</a></b>
								</div>
							</div>
						<?php endif ?>
					<?php endif ?>

					<div class="span2">
						<div class="<?= value_if_test($vd->is_private_preview, 'pull-right') ?>">
							<a class="brand brand-logo" href="<?= $ci->conf('website_url') ?>" accesskey="1">
								News<b>wire</b></a>
						</div>
					</div>

				<?php else: ?>
					
					<div class="span10">
						<?php if ($vd->is_private_preview): ?>
							<div class="private_preview_msg pull-right">
								IMPORTANT: This is your Company Newsroom - It's not yet public.
								<a href="#">Click here</a> to request FREE activation.
							</div>
						<?php endif ?>
						<?php if ($vd->is_auto_built_unclaimed_nr): ?>
							<?php if($ci->session->get('ac_nr_tokened_visit_nr_id')
								&& $ci->session->get('ac_nr_tokened_visit_nr_id') == $ci->newsroom->company_id): ?>
							<div class="private_preview_msg">
								<b><a href="browse/claim_nr" class="strong">&gt;&gt; CLICK HERE TO ACTIVATE THIS NEWSROOM &lt;&lt;</a></b>
							</div>
						<?php endif ?>
					<?php endif ?>
					</div>

					<div class="span2">
						<div class="pull-right">
							<a class="brand brand-logo" href="<?= $ci->conf('website_url') ?>" accesskey="1">
								News<b>wire</b></a>
						</div>
					</div>

				<?php endif ?>
				</div>
			</div>
		</section>

	<?php endif ?>

<?php elseif ($ci->is_common_host): ?>

	<section class="top-panel top-panel-website">
		<div class="bs3-container">
			<div class="clearfix">
				<a class="brand brand-logo" href="<?= $ci->conf('website_url') ?>" accesskey="1">
					<img src="<?= $vd->assets_base ?>im/website/logo-inewswire.svg" alt="Newswire Logo" />
				</a>
			</div>
		</div>
	</section>

<?php endif ?>