<?php if (count($ci->uri->segments) == 1): ?>
<main class="main" role="main">	
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
	</div>	
</main>
<?php endif ?>
	
<section class="home-latest-news latest-news">
	<div class="container">
		
		<header class="ln-header">
			
			<?php if (isset($vd->ln_header)): ?>
			<h2><?= $vd->esc($vd->ln_header) ?></h2>
			<?php elseif (isset($vd->ln_header_html)): ?>
			<h2><?= $vd->ln_header_html ?></h2>
			<?php endif ?>
			
		</header>
	
		<div class="row masonry" id="ln-container">
			
			<?php if(count($vd->results) == 0) : ?>
			
				<div class="col-xs-3"></div>
				<div class="col-xs-6 ta-center no-results">
					<h3>No Results</h3><hr />
					<p>No content was found in this section. 
						Why not try another?</p>
				</div>
				<div class="col-xs-3"></div>
				
			<?php else: ?>

				<?php if ($vd->impressions_uri): ?>
				<img src="<?= $vd->esc($vd->impressions_uri) ?>"
					width="1" height="1" class="stat-pixel" />
				<?php endif ?>
			
				<?php foreach ($vd->results as $result): ?>
				<?= $ci->load->view("website/news-center/{$result->type}", 
					array('content' => $result)); ?>
				<?php endforeach ?>
				
			<?php endif; ?>
			
		</div>
		
		<?php if (count($vd->results)): ?>
		<?= $vd->chunkination->render() ?>
		<?php endif ?>
		
	</div>
</section>

<script>

window.__on_nav_callback = window.__on_nav_callback || [];
window.__on_nav_callback.push(function(local_url) {
	
	// the text on the filter drop down
	var filter_label = $("#news-center-filter-label");
	
	if (local_url === "newsroom/pr")
		filter_label.text("Press Releases");
	else if (local_url === "newsroom/audio")
		filter_label.text("Audio");
	else if (local_url === "newsroom/event")
		filter_label.text("Events");
	else if (local_url === "newsroom/image")
		filter_label.text("Images");
	else if (local_url === "newsroom/news")
		filter_label.text("News");
	else if (local_url === "newsroom/video")
		filter_label.text("Videos");
	
});

</script>