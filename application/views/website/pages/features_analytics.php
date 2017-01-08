<?php $ci->title = 'Analytics'; ?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Measured Results. Your Metrics.</h1>
					<p>
						<span>Full reporting for your campaigns and content. Understanding your</span>
						<span>efforts provides insights for maximum ROI.</span>
					</p>
					<img class="img-responsive" src="<?= $vd->assets_base ?>im/website/main-img-analytics.png" 
						alt="Measured Results. Your Metrics." />
				</header>
			</div>
			<div class="col-sm-1"></div>
		</div>
	</div>
</main>

<section class="features">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-12">
						<header class="features-header">
							<h2>Track Performance for Your Content</h2>
						</header>
					</div>
				</div>
				<div class="row pf-content">
					<div class="col-sm-4">
						<h3><i class="fa fa-bar-chart-o"></i> Performance Data</h3>
						<p>
							Our Analytics provides you with the tools to understand your content engagement and performance across media channels. Get a real-time snapshot in seconds.
						</p>
					</div>
					<div class="col-sm-4">
						<h3><i class="fa fa-file-text"></i> PDF Reporting</h3>
						<p>
							A customized PDF Report is available for Premium Submissions and can be downloaded directly from your member panel.
							Each PDF contains a detailed listing of the exact URLs where your press release has been published to.
							Share this report with colleagues and stakeholders.
						</p>
					</div>
					<div class="col-sm-4">
						<h3><i class="fa fa-envelope"></i> Email Opens &amp; Clicks</h3>
						<p>
							Email campaigns can be easily tracked using our analytics. You are able to review your open rates and click
							thru rate when implementing our tracking URL. The ability to measure ROI on each campaign will give you
							confidence at every stage of engagement. 
						</p>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
		</div>
	</div>
</section>
 
<div class="separator"></div>

<section class="additional-features">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-6">
				<h3>Social Media Engagement</h3>
				<p>
					Shares, Likes, Tweets and G+1 are part of today’s online conversation. As social media becomes an increasingly important 
					marketing vehicle, it’s vital to understand how it affects your brand, messaging and public perception.
					We help you track your Facebook Likes and Tweets in our Analytics.
				</p>
			</div>
			<div class="col-sm-4">
				<img class="img-responsive" src="<?= $vd->assets_base ?>im/website/370x270-social.png" alt="Social Media Engagement" />
			</div>
		</div>
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-6">
				<h3>Track Location of Your Readers</h3>
				<p>
					See where your readers are and where your story resonates. We help track and locate the views
					of your press release and newsroom. From the Americas to Europe and Asia, reach your targeted audience.
				</p>
			</div>
			<div class="col-sm-4">
				<img class="img-responsive" src="<?= $vd->assets_base ?>im/website/370x270-track.png" alt="Reader Location" />
			</div>
			<div class="col-sm-1">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-6">
				<h3>Add Analytics for Comprehensive Data</h3>
				<p>
					Want to include your Google Analytics? You can add your own Google Analytics code to your member panel and
					gather more comprehensive data for your team. We make it simple and easy. The tracking is directly applied to your content pages. 
				</p>
			</div>
			<div class="col-sm-4">
				<img class="img-responsive" src="<?= $vd->assets_base ?>im/website/370x270-analytics.png" alt="Google Analytics" />
			</div>
			<div class="col-sm-1">
			</div>
		</div>
		</div>
</section>

<?= $ci->load->view('website/partials/register-footer') ?>