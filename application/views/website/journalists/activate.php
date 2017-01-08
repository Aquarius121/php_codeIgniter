<main class="main" role="main"></main>

<?php if ($ci->feedback): ?>
<div class="row marbot-20">
	<div class="col-sm-3"></div>
	<div class="col-sm-6 ta-center">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<?= $ci->load->view('website/partials/feedback') ?>
			</div>
		</div>
	</div>
</div>
<?php endif ?>

<div>
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<?php if (@$this->vd->first_time_activation): ?>
					<h1>Journalists Registration</h1>
					<?php else: ?>
					<h2>Journalist Preferences</h2>
					<?php endif ?>
				</header>
			</div>
			<div class="col-sm-1"></div>
		</div>
	</div>
</div>

<article class="article">
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<form class="col-sm-8" method="post" action="<?= current_url() ?>">

				<? /* must be present otherwise no post values */ ?>
				<input type="hidden" name="save" value="1" />

				<?php if (@$this->vd->first_time_activation): ?>
				<section class="marbot-50 ta-center">
					<span class="muted">Step 1: Register &#10132;</span>
					<strong>Step 2</strong>: Activate
				</section>
				<?php endif ?>

				<div class="row marbot-20">
					<div class="col-sm-2"></div>
					<div class="col-sm-4 status-alternative"><strong>Content</strong></div>
					<div class="col-sm-4 ta-center status-alternative"><strong>Update Frequency</strong></div>
				</div>

				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-4 content-large">Press Releases</div>
					<div class="col-sm-4 ta-center">
						<div class="rocker marbot-15">
							<input type="checkbox" class="default" data-label="Off" />
							<input type="checkbox" name="has_daily_pr_update" value="1" data-label="Daily" 
								<?= value_if_test($vd->subscriber->has_daily_pr_update, 'checked') ?> />
							<input type="checkbox" name="has_realtime_pr_update" value="1" data-label="Real-time" 
								<?= value_if_test($vd->subscriber->has_realtime_pr_update, 'checked') ?> />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-4 content-large">News Content</div>
					<div class="col-sm-4 ta-center">
						<div class="rocker marbot-15">
							<input type="checkbox" class="default" data-label="Off" />
							<input type="checkbox" name="has_daily_news_update" value="1" data-label="Daily" 
								<?= value_if_test($vd->subscriber->has_daily_news_update, 'checked') ?> />
							<input type="checkbox" name="has_realtime_news_update" value="1" data-label="Real-time" 
								<?= value_if_test($vd->subscriber->has_realtime_news_update, 'checked') ?> />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-4 content-large">Events</div>
					<div class="col-sm-4 ta-center">
						<div class="rocker marbot-15">
							<input type="checkbox" class="default" data-label="Off" />
							<input type="checkbox" name="has_daily_event_update" value="1" data-label="Daily" 
								<?= value_if_test($vd->subscriber->has_daily_event_update, 'checked') ?> />
							<input type="checkbox" name="has_realtime_event_update" value="1" data-label="Real-time" 
								<?= value_if_test($vd->subscriber->has_realtime_event_update, 'checked') ?> />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-2"></div>
					<div class="col-sm-4 content-large">Blog Posts</div>
					<div class="col-sm-4 ta-center">
						<div class="rocker marbot-15">
							<input type="checkbox" class="default" data-label="Off" />
							<input type="checkbox" name="has_daily_blog_update" value="1" data-label="Daily" 
								<?= value_if_test($vd->subscriber->has_daily_blog_update, 'checked') ?> />
							<input type="checkbox" name="has_realtime_blog_update" value="1" data-label="Real-time" 
								<?= value_if_test($vd->subscriber->has_realtime_blog_update, 'checked') ?> />
						</div>
					</div>
				</div>

				<div class="marbot-30"></div>
				<div class="ta-center">
					<?php if (@$this->vd->first_time_activation): ?>
					<input type="hidden" name="first_time_activation" value="1" />
					<button type="submit" class="btn btn-orange">Activate Subscription</button>
					<?php else: ?>
					<button type="submit" class="btn btn-orange">Update Preferences</button>
					<?php endif ?>
				</div>

			</form>
			<div class="col-sm-2"></div>
		</div>
	</div>
</article>

<?php 

	$render_basic = $ci->is_development();

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/wireupdate.css');
	$loader->add('css/rocker.css');
	echo $loader->render($render_basic);

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/rocker.js');
	echo $loader->render($render_basic);

?>