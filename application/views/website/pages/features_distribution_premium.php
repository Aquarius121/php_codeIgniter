<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Premium Distribution</h1>
					<p>
						<span>Our Premium Distribution Sites</span>
					</p>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<section class="features">
	<div class="container">
		<div class="row pf-content">
			<div class="col-sm-1"></div>
			<div class="col-sm-12">
				
				<?php foreach ($vd->sources as $k => $source): ?>

					<?php if ($k % 3 == 0 && $k > 1): ?>
								</div>
							</div>
						</div>
					<?php endif ?>

					<?php if ($k == 0 || $k % 3 == 0): ?>
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
					<?php endif ?>
					
					<div class="col-md-4 col-sm-6 col-xs-12 marbot-20 dist-site-item">
						<div class="dist-site-logo">
							<?php if (!empty($source->logo)): ?>
								<img src="<?= $source->logo ?>" alt="logo">
							<?php endif ?>&nbsp;
						</div>					
						<div class="fl-left">
							<div class="name">
								<?= $vd->esc($vd->cut($source->name, 30)) ?>
							</div>
							<a href="<?= $vd->esc($source->url_site) ?>">
								<?= $vd->esc($vd->cut($source->url_site, 30)) ?>
							</a>
						</div>
					</div>

				<?php endforeach ?>
				
			</div>
		</div>
	</div>
</section>

<?= $ci->load->view('website/partials/register-footer') ?>