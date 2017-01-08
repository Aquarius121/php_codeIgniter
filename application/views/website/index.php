<main class="master-section" role="main">
	<div class="container">
		<div class="row text-center">
			<div class="col-sm-12">
				<header class="master-section-header">
					<h2>Press Release Distribution Simplified</h2>
				</header>
				<div class="master-section-content">
					<p>Distribute your news to traditional and digital media outlets using our integrated platform making it easy to launch and track effective press release campaigns.</p>
					<a href="pricing" class="btn btn-success">View Pricing</a>
					<a href="planner" class="btn btn-default">Find the Right Strategy for You</a>
					<img class="ms-img active visible" src="<?= $vd->assets_base ?>im/website/planner-screenshots.svg" />
				</div>
			</div>
		</div>
	</div>
</main>

<section class="home-features">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				
				<div class="col-md-4">
					<section class="hf-item">
						<h3><i class="fa fa-pencil"></i> Custom Newsroom</h3>
						<p>
							Journalists, media and customers are always looking for the latest happenings in your industry. Your custom newsroom helps centralize your content into one convenient place.  Press, News, Events and Company information can be listed on your newsroom.
						</p>
						<a href="features/newsrooms" class="btn btn-default">Learn More</a>
					</section>
				</div>

				<div class="col-md-4">
					<section class="hf-item">
						<h3><i class="fa fa-sitemap"></i> Distribution</h3>
						<p>
							Get your press release published on 7000+ <a href="features/distribution">News and Media Outlets</a>. Your story is syndicated to a wide range of business, 
							financial and news outlets to increase your presence online and in search. From our Basic Press Release to our Premium Distribution, 
							you can  select from a wide range of plans.
						</p>
						<a href="features/distribution" class="btn btn-default">Learn More</a>
					</section>
				</div>

				<div class="col-md-4">
					<section class="hf-item">
						<h3><i class="fa fa-bar-chart-o"></i> Measured Results</h3>
						<p>
							Our easy to use analytics puts the data at your fingertips. Sort and select specific dates, view locations and open rates in one convenient place.
							We also provide a detailed PDF report for Premium Submissions.				
						</p>
						<a href="features/analytics" class="btn btn-default">Learn More</a>
					</section>
				</div>
				
			</div>
		</div>
	</div>
</section>

<section class="brands-list-panel">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h3><span>We reach more than 100,000 journalists</span></h3>
				<ul class="brands-list">
					<li><img src="<?= $vd->assets_base ?>im/website/logo-prnewswire.png" alt=""></li>
					<li><img src="<?= $vd->assets_base ?>im/website/logo-cnnmoney.png" alt=""></li>
					<li><img src="<?= $vd->assets_base ?>im/website/logo-nationaljournal.png" alt=""></li>
					<li><img src="<?= $vd->assets_base ?>im/website/logo-bloomberg.png" alt=""></li>
					<li><img src="<?= $vd->assets_base ?>im/website/logo-wsj.png" alt=""></li>
				</ul>
			</div>
		</div>
	</div>
</section>
 
<div class="separator"></div>

<section class="home-latest-news front-latest-news">
	<div class="container">

		<div class="row">
			<div class="col-sm-12">
				<header class="main-header">
					<h1>Latest News Around the World</h1>
					<p>
						<span>24 hours a day, 7 days a week, 365 days a year. Find out about the latest</span>
						<span>information, news and announcements.</span>
					</p>
				</header>
			</div>
		</div>
		
		<div class="row" id="latest-news-holder"></div>
		
		<script>
		
		$(function() {
			
			var latest_news_holder = $("#latest-news-holder");			
			var home_fader = $(".home-fader");

			$.get("newsroom/front_cached", function(res) {

				latest_news_holder.html(res.data);
				latest_news_holder.addClass("masonry");
				latest_news_holder.imagesLoaded(function() {
					latest_news_holder.find("img").addClass("loaded");
					latest_news_holder.masonry({
						itemSelector: ".news-item",
					});
				});

				if (res.pixel) {
					var pixel = $.create("img");
					pixel.addClass("stat-pixel");
					pixel.attr("src", res.pixel);
					$(document.body).append(pixel);
				}

			});

			setInterval(function() {				
				var images = home_fader.children("img");
				var active = images.filter(".active");
				var next = active.next("img");
				if (!next.size())
					next = images.eq(0);
				active.removeClass("active");
				next.addClass("active");
				active.fadeOut(750);
				next.fadeIn(750);				
			}, 5000);
			
		});
		
		</script>
		
	</div>
</section>
