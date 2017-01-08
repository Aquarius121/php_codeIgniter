<?= $ci->load->view('website/company/submenu') ?>

<div style="padding-top: 60px;"></div>

<?php if (count($ci->uri->segments) == 1): ?>
<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<header class="main-header marbot-50">
					<h1>Company Directory</h1>
				</header>
			</div>
		</div>
	</div>	
</main>
<?php endif ?>
	
<section class="home-latest-news latest-news">
	<div class="container">
			
		<div class="row masonry" id="ln-container">
			
			<?php if(count($vd->results) == 0) : ?>
			
				<div class="col-xs-3"></div>
				<div class="col-xs-6 ta-center no-results">
					<h3>No Results</h3><hr />
					<p>No content was found in this section. 
						Why not try another?</p>
				</div>
				<div class="col-xs-3"></div>
				
			<?php else : ?>
			
				<?php foreach ($vd->results as $result): ?>
				<?= $ci->load->view("website/company/snippet", 
					array('company' => $result)); ?>
				<?php endforeach ?>
				
			<?php endif; ?>
			
		</div>
		
		<?php if (count($vd->results)): ?>
		<?= $vd->chunkination->render_bigger('website/company/chunkination', 15) ?>
		<?php endif ?>
		
	</div>
</section>