<?php $ci->title = 'How It Works'; ?>

<main class="main why-us-section" role="main">
<div class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-10">
			<header class="main-header">
				<h1 class="marbot-40">How Newswire Works</h1>
				<div class="marbot-25">
					<?php $video = new Video_Youtube('2tADZTNnwfQ'); ?>
					<?= $video->render(854, 480) ?>
				</div>
				<p>Easily share your story across multiple channels for<br>effective and easy press management.</p>
			</header>
		</div>
		<div class="col-sm-1"></div>
	</div>

	<div class="row">
		<div class="col-md-2"></div>

		<div class="col-md-8" id="tabs">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#press-release" data-toggle="tab">Press Release Distribution</a></li>
				<li><a href="#company-newsroom" data-toggle="tab">Company Newsroom</a></li>
				<li><a href="#media-outreach" data-toggle="tab">Media Outreach</a></li>
			</ul>

			<div class="pad-10v"></div>
			<div class="tab-content">
				<div class="tab-pane active" id="press-release">
					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-user"></i></div>
						<div class="col-sm-8">
							<h2>Sign Up for an Account</h2>
							<p>Getting started is easy. Register for free then select the type of distribution you need. Our Premium Featured offers wide distribution to various new and media outlets.
								You can see a full listing of <a href="features/distribution">our distribution</a>.</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-share"></i></div>
						<div class="col-sm-8">
							<h2>Submit your Press Release</h2>
							<p>
								We've streamlined the process of submitting your press release. Through a single page submission form you are
								able to input all of your press release information. Our editorial process is fast and you will receive notification
								within a few hours. If you are looking for additional distribution, you can try out the <a href="features/pitching">media outreach</a> feature.
							</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-bar-chart-o"></i></div>
						<div class="col-sm-8">
							<h2>Track and Analyze Your Campaign</h2>
							<p>
								We make it easy to track and view your press release’s exposure. View all your statistcs under the <a href="features/analytics">Analytics</a> tool.
								From social media engagement to the location of your viewers, all of your stats are available in the member panel. You can also collect
								additional data by adding your Google Analytics code for comprehensive data.
							</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="tab-pane" id="company-newsroom">
					<div class="col-sm-1"></div>
					<div class="tab-content-data">
						<div class="col-sm-3 count-icon"><i class="fa fa-list-ul"></i></div>
						<div class="col-sm-8">
							<h2>Included in Subscription</h2>
							<p>
								We make <a href="features/newsrooms">Newsrooms</a> easy for everyone. Leverage Newswire's authority as a News provider to help make your newsroom a valuable web asset.
								Each subscription includes a custom newsroom where you can assign a unique web address for your newsroom.
								Your newsroom can be customized to your brand and look. Get professional results with the simplicity of a point and click interface.
							</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-coffee"></i></div>
						<div class="col-sm-8">
							<h2>Press Management Made Easy</h2>
							<p>Press Management made easy. Easily customize your newsroom for your company’s look and feel.
								The evolution of the digital press kit is here - where you can manage all of your content in one place.
								From News, Events, Images and Media assets, your newsroom houses all of your content for media to view and search.
							</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-clipboard"></i></div>
						<div class="col-sm-8">
							<h2>Web Asset and Analytics</h2>
							<p>Your Newsroom includes performance metrics to help provide a clearer picture of your visitor engagement as well as which of your content is currently trending.</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="tab-pane" id="media-outreach">
					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-users"></i></div>
						<div class="col-sm-8">
							<h2>Easy Engagement</h2>
							<p>All Premium Features press releases include pitch credits that can be used to <a href="features/pitching">send pitches</a> to various media contacts. Use the provided template to easily import your email list to update them on the latest happenings at your company. We provide a seamless approach to press release distribution and press management.</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-envelope-o"></i></div>
						<div class="col-sm-8">
							<h2>Track Open Rates</h2>
							<p>After submitting your email pitch, track your email open rates through the Analytics screen. You will be able to track who opened your email and your links clicked. Your email analytics can help you identify your successful campaigns.</p>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="tab-content-data">
						<div class="col-sm-1"></div>
						<div class="col-sm-3 count-icon"><i class="fa fa-files-o"></i></div>
						<div class="col-sm-8">
							<h2>Upcoming Database</h2>
							<p>
								Connect with the hundreds of thousands of journalists that are hungry for the next story.
								Search our database of journalists and media contacts specific to your industry and location.
							</p>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-2"></div>
	</div>
</div>
</main>

<?= $ci->load->view('website/partials/planner-footer') ?>