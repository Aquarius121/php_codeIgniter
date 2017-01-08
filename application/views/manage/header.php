<!doctype html>
<html lang="en" class="manage25 newswire is-user-panel                                   
	<?= value_if_test($ci->is_common_host,                  'is-common-host') ?>
	<?= value_if_test($ci->is_detached_host,                'is-detached-host') ?>
	<?= value_if_test(Auth::user()->has_platinum_access(),  'has-platinum-access') ?>
	<?= value_if_test(Auth::user()->has_gold_access(),      'has-gold-access') ?>
	<?= value_if_test(Auth::user()->has_silver_access(),    'has-silver-access') ?>
	<?= value_if_test(Auth::user()->is_free_user(),         'is-free-user') ?>
	<?= value_if_test(Auth::user()->is_virtual(),           'is-virtual-user') ?>
	<?= value_if_test(Auth::is_admin_mode(),                'is-admin-mode') ?>">
	<head>

		<title>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> | 
			<?php endif ?>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
			<?php if (!$ci->is_common_host): ?>
				<?= $vd->esc($ci->newsroom->company_name) ?> |
			<?php endif ?>
			Newswire
		</title>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<base href="<?= $base = $ci->env['base_url'] ?>" />

		<?php 

		$render_basic = $ci->is_development();

		$loader = new Assets\CSS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap3/css/bootstrap.min.css');
		$loader->add('css/base.css');
		$loader->add('css/manage.css');
		$loader->add('css/tutorial.css');
		echo $loader->render($render_basic);

		?>

		<?= $ci->load->view('partials/defer-before') ?>

		<script>

		CKEDITOR_BASEPATH = <?= json_encode("{$vd->assets_base}lib/ckeditor/") ?>;
		NR_COMPANY_ID = <?= json_encode($ci->newsroom->company_id) ?>;
		RELATIVE_URI = <?= json_encode($ci->uri->uri_string) ?>;
		ASSETS_VERSION = <?= json_encode($vd->version) ?>;
		IS_DEVELOPMENT = <?= json_encode($ci->is_development()) ?>;
		IS_PRODUCTION = <?= json_encode($ci->is_production()) ?>;

		</script>

		<?= $ci->load->view('partials/track-kiss-metrics') ?>

		<?php if ($ci->eoh): ?>
		<?php foreach ($ci->eoh as $eoh) 
			echo $eoh; ?>
		<?php endif ?>

		<?php 

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/json.js');
		$loader->add('lib/html5shiv.js');
		$loader->add('lib/respond.min.js');
		$render_basic = $ci->is_development();

		?>

		<!--[if lt IE 9]>
		<?= $loader->render($render_basic); ?>
		<![endif]-->

		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/formdata.js');

		?>

		<!--[if lt IE 10]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

	</head>

	<body>

		<?= $ci->load->view('shared/partials/header-admo') ?>

		<div id="wrapper">

			<nav class="navbar navbar-default navbar-fixed-top no-print" role="navigation">
				<div class="navbar-header">
					<div class="mob-header">
						<a href="#side-nav-menu"></a>
					</div>
					<a class="navbar-brand" href="manage/overview/dashboard">
						<img src="<?= $vd->assets_base ?>im/panel-logo.svg">
					</a>
				</div>

				<ul class="nav navbar-left top-nav">

					<li class="dropdown">

						<a href="#" class="dropdown-toggle dropdown-companies" data-toggle="dropdown">
							<?php if ($ci->is_common_host): ?>
								<?php if (Auth::user()->is_free_user()): ?>
									Select Company
								<?php else: ?>
									Account Overview
								<?php endif ?>
							<?php else: ?>
								<?= $vd->esc($ci->newsroom->company_name) ?>
							<?php endif ?>
							<b class="caret"></b>
						</a>

						<ul class="dropdown-menu companies-dropdown">
							<li><a href="#<?= $vd->new_company_modal_id ?>" role="button"
								data-toggle="modal" >Add Company</a></li>
							<li><a href="manage/companies">Manage Companies</a>
							<li><a href="manage/overview/dashboard">Account Overview</a>
							<li role="separator" class="divider"></li>
							<li class="dropdown-header">Companies</li>
							<?php foreach ($vd->user_newsrooms as $newsroom): ?>
							<li>
								<?php if ($ci->is_common_host): ?>
								<a href="<?= $newsroom->url('manage/dashboard') ?>">
								<?php else: ?>
								<a href="<?= gstring($newsroom->url($ci->current_method_uri())) ?>">
								<?php endif ?>
									<?php if ($newsroom->color): ?>
										<span class="company-color" style="background: <?= $newsroom->color ?>"></span>
									<?php else: ?>
										<span class="company-color"></span>
									<?php endif ?>
									<?= $vd->esc($newsroom->company_name) ?>
								</a>
							</li>
							<?php endforeach ?>							
						</ul>

					</li>

					<?php if (!$ci->is_common_host): ?>
						<li><a href="browse" target="_blank"><i class="fa fa-newspaper-o"></i>
							<span class="vw-nws">View Newsroom</span></a></li>
					<?php endif ?>
				
				</ul>

				<ul class="nav navbar-right top-nav">
					
					<li class="search-form">
						<?= $this->load->view('manage/partials/search') ?> 
					</li>

					<?php if (!$this->is_common_host): ?>
					<?= $ci->load->view('manage/partials/todo') ?>
					<?php endif ?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle user-menu-toggle" data-toggle="dropdown">
							<i class="fa fa-user fa-icon-user"></i>
							<span class="hidden-sm hidden-xs header-user-name">
								<span class="first-name"><?= $vd->esc(Auth::user()->first_name) ?></span>
								<span class="last-name"><?= $vd->esc(Auth::user()->last_name) ?></span>
							</span>
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<?php if (Auth::is_admin_online()): ?>
							<li>
								<a href="#" class="status-muted">
									S: &nbsp;<span class="status-info"><?= strtoupper(file_get_contents('/etc/machine')) ?></span>
									(<span class="status-info-muted"><?= $ci->conf('ip_address') ?></span>)
								</a>
							</li>
							<li class="divider"></li>
							<?php endif ?>
							<?php if (Auth::is_admin_online()): ?>
							<li><a href="<?= $ci->website_url('admin') ?>">
								<i class="icon-lock"></i> Admin Panel</a></li>
							<?php endif ?>
							<li class="divider"></li>
							<li><a href="manage/account/billing">Billing Information</a></li>
							<li><a href="manage/account/order/history">Order History</a></li>
							<li><a href="manage/account">Account Details</a></li>
							<li class="divider"></li>
							<li><a href="manage/upgrade/credits">Purchase Credits</a></li>
							<li><a href="manage/upgrade/plans">Membership Plans</a></li>
							<li class="divider"></li>
							<li><a href="manage/companies">Manage Companies</a></li></li>
							<li class="divider"></li>
							<li><a href="<?= $ci->conf('website_url') ?>helpdesk/">Helpdesk</a>
							<li><a href="shared/logout">Log Out</a></li>
						</ul>
					</li>

				</ul>

			</nav>

			<nav class="navbar-default no-print" role="navigation" id="side-nav-menu">
				<ul class="nav navbar-nav side-nav nav-activate" data-nav-selector="> li > a" data-nav-class="in">
					<li>
						<a class="top-level-link" href="manage<?= value_if_test($ci->is_common_host, '/overview') ?>/dashboard" 
							data-on="^manage/(overview/)?dashboard"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-distribution"
							data-nav-target="#menu-distribution"
							data-on="^manage/publish/pr"><i class="fa fa-fw fa-gears"></i> Distribution
						 		<i class="fa fa-fw fa-caret-down hidden-xs"></i></a>
						<ul id="menu-distribution" class="collapse nav-activate">
							<li><a data-on="^manage/publish/pr/edit" href="manage/publish/pr/edit">Submit Press Release</a></li>
							<li><a data-on="^manage/publish/pr/all" href="manage/publish/pr">Press Releases</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal" data-section="publish"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Distribution Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-newsroom"
							data-nav-target="#menu-newsroom" data-on="^(manage/publish/(news|event|image|audio|video)|manage/newsroom/customize)">
								<i class="fa fa-fw fa-file-text"></i> Newsroom
						 		<i class="fa fa-fw fa-caret-down hidden-xs"></i>
						 </a>
						<ul id="menu-newsroom" class="collapse nav-activate">
							<li><a data-on="^manage/publish/news/" href="manage/publish/news">News Content</a></li>
							<li><a data-on="^manage/publish/event/" href="manage/publish/event">Events</a></li>
							<li><a data-on="^manage/publish/image/" href="manage/publish/image">Images</a></li>
							<li><a data-on="^manage/publish/audio/" href="manage/publish/audio">Audio</a></li>
							<li><a data-on="^manage/publish/video/" href="manage/publish/video">Videos</a></li>
							<li><a data-on="^manage/newsroom/customize" href="manage/newsroom/customize">Customization</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal" data-section="newsroom"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Newsroom Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-outreach"
							data-nav-target="#menu-outreach" data-on="^manage/contact/"><i class="fa fa-fw fa-rss"></i> Outreach
								<i class="fa fa-fw fa-caret-down  hidden-xs"></i></a>
						<ul id="menu-outreach" class="collapse nav-activate">
							<li><a data-on="^manage/contact/(contact|import|list)" href="manage/contact/list">Contacts Manager</a></li>
							<li><a data-on="^manage/contact/campaign"  href="manage/contact/campaign">Email Campaigns</a></li>
							<li><a data-on="^manage/contact/media_database" href="manage/contact/media_database">Media Database</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal"  data-section="contact"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Media Outreach Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-outreach"
							data-nav-target="#menu-outreach" data-on="^manage/influencers/"><i class="fa fa-fw fa-rss"></i> Social Influencers
								<i class="fa fa-fw fa-caret-down  hidden-xs"></i></a>
						<ul id="menu-outreach" class="collapse nav-activate">
							<li><a data-on="^manage/contact/(contact|import|list)" href="manage/contact/list">Contacts Manager</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal"  data-section="contact"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Media Outreach Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-analytics"
							data-nav-target="#menu-analytics" data-on="^manage/analyze/"><i class="fa fa-fw fa-pie-chart"></i> Analytics
								<i class="fa fa-fw fa-caret-down hidden-xs"></i></a>
						<ul id="menu-analytics" class="collapse nav-activate">
							<li><a data-on="^manage/analyze/content" href="manage/analyze/content/pr/published">Content Stats</a></li>
							<li><a data-on="^manage/analyze/email" href="manage/analyze/email">Email Stats</a></li>
							<li><a data-on="^manage/analyze/overall" href="manage/analyze/overall">Newsroom Stats</a></li>
							<li><a data-on="^manage/analyze/settings" href="manage/analyze/settings">Settings</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal" data-section="analyze"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Analytics Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="#" data-toggle="collapse" data-target="#menu-company" 
							data-nav-target="#menu-company"  data-on="^manage/newsroom/"><i class="fa fa-fw fa-users"></i> Company
								<i class="fa fa-fw fa-caret-down hidden-xs"></i></a>
						<ul id="menu-company" class="collapse nav-activate">
							<li><a data-on="^manage/newsroom/company" href="manage/newsroom/company">Profile</a></li>
							<li><a data-on="^manage/newsroom/contact" href="manage/newsroom/contact">Contacts</a></li>
							<li><a data-on="^manage/newsroom/social" href="manage/newsroom/social">Social Media</a></li>
							<!-- <li class="help video-guide-link">
								<a href="#video_guide" data-toggle="modal" data-section="newsroom"
								data-modal-id="<?= $vd->video_guide_modal_id ?>">Newsroom Help Guide</a></li> -->
						</ul>
					</li>
					<li>
						<a class="top-level-link" href="manage/insights" 
							data-on="^manage/insights"><i class="fa fa-fw fa-bell"></i> Insights
							<span class="status-beta">&nbsp;Beta</span></a>
					</li>
					<li>
						<a class="top-level-link last" href="manage/tutorial" 
							data-on="^manage/tutorial"><i class="fa fa-fw fa-video-camera"></i> Tutorial Videos</a>
					</li>
				</ul>

				<div class="help nav-bar-help">
					<h3>Need Help?</h3>
					<p>Call 800-713-7278 or <a href="mailto:support@newswire.com">Email</a>.</p>
					<small class="verbose">Our normal business hours are between 8AM and 7PM EDT (Monday to Friday).</small>
				</div>

			</nav>

			<script> 
			defer(function(){
				window.nav_activate();
			});
			</script>

			<div id="page-wrapper">

				<div class="search-form no-print">
					<?= $this->load->view('manage/partials/search') ?> 
				</div>
			
				<div id="feedback" class="no-print">
					<?php $ci->process_feedback(); ?>
					<?php foreach ($ci->feedback as $feedback): ?>
					<div class="feedback"><?= $feedback ?></div>
					<?php endforeach ?>
				</div>

