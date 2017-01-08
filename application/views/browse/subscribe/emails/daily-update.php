<html>
	<head>
		<style>
			body{
				font-family: Arial;
				font-size: 12px;
			}
			h1{
				font-size: 24px;
			}
			h2{
				font-size: 20px;
			}
			h3{
				font-size: 16px;
			}
			table{
				border: 1px solid #CCCCCC; 
				min-width: 650px;
				width: 750px;
				padding: 10px 20px; 
				border-radius: 5px; 
				margin: auto; font-family: Arial;
			}
			span{
				font-size: 10px;
				margin-top: 10px;
			}	
			p{
				color: #888888;
			}
		</style>
	</head>
	<body>
		<center>
			<?php $lo_im = Model_Image::find(@$vd->nr_custom->logo_image_id); ?>
			<?php $lo_height = 0; ?>
			<?php if ($lo_im) $lo_variant = $lo_im->variant('header'); ?>
			<?php if ($lo_im) $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
			<?php if ($lo_im) $lo_height = $lo_variant->height; ?>
			<table style="border: none; padding:0">
				<tr>
					<td>
						<?php if ($lo_im): ?>
							<a href="<?= $vd->newsroom->url() ?>">
								<img src="<?= $ci->website_url($lo_url) ?>" border="0" />
							</a>
						<?php endif ?>
					</td>
					<td>
						<h1>					
							<?= $vd->newsroom->company_name ?> Latest Updates
						</h1>
					</td>
				</tr>
			</table>
			<br>
			<table class="content-table">
				<tr>
					<td>
						<?php if (count($vd->latest_content->prs)): ?>
							<h2>Press Releases: </h2>
							<?php foreach ($vd->latest_content->prs as $pr): ?>
								<h3><?= $vd->esc($vd->cut($pr->title, 80)) ?></h3>
								<?= $vd->esc($pr->summary) ?><br>
								<div style="text-align:right">
									<a href="<?= $pr->url ?>">read more</a>
								</div>
							<?php endforeach ?>
						<?php endif ?>

						<?php if (count($vd->latest_content->news)): ?>
							<h2>News: </h2>
							<?php foreach ($vd->latest_content->news as $news): ?>
								<h3><?= $vd->esc($vd->cut($news->title, 80)) ?></h3>
								<?= $vd->esc($news->summary) ?><br>
								<div style="text-align:right">
									<a href="<?= $news->url ?>">read more</a>
								</div>
							<?php endforeach ?>
						<?php endif ?>

						<?php if (count($vd->latest_content->events)): ?>
							<h2>Events: </h2>
							<?php foreach ($vd->latest_content->events as $event): ?>
								<h3><?= $vd->esc($vd->cut($event->title, 80)) ?></h3>
								<?= $vd->esc($event->summary) ?><br>
								<div style="text-align:right">
									<a href="<?= $event->url ?>">read more</a>
								</div>
							<?php endforeach ?>
						<?php endif ?>

						<?php if (count($vd->latest_content->blog)): ?>
							<h2>Blog Posts: </h2>
							<?php foreach ($vd->latest_content->blog as $post): ?>
								<h3><?= stripslashes($vd->esc($vd->cut($post->title, 80))) ?></h3>
								<?= nl2br(stripslashes($post->summary)) ?><br>
								<div style="text-align:right">
									<a href="<?= $post->url ?>">read more</a>
								</div>
							<?php endforeach ?>
						<?php endif ?>

						<?php if (count($vd->latest_content->fb)): ?>
							<h2>Facebook: </h2>
							<?php foreach ($vd->latest_content->fb as $fb): ?>
								<h3><?= $vd->cut(stripslashes(@$fb->title), 84) ?></h3>
								<?= nl2br(stripslashes($vd->cut($fb->summary, 150))) ?><br>
								<div style="text-align:right">
									<a href="<?= $fb->url ?>">read more</a>
								</div>
							<?php endforeach ?>
						<?php endif ?>

						<?php if (count($vd->latest_content->twitter)): ?>
							<h2>Twitter: </h2>
							<?php foreach ($vd->latest_content->twitter as $tweet): ?>		
								<?= stripslashes(@$tweet->summary) ?><br>
								<div style="text-align:right">
									<a href="<?= $tweet->url ?>">read more</a><br><br>
								</div>
							<?php endforeach ?>
						<?php endif ?>
						
						<p><?= $vd->latest_content_date ?></p>
					</td>
				</tr>
			</table>




			<?php if (count(@$vd->previous_content->content)): ?>
			<br><br>
			<table style="border: none; padding:0">
				<tr>
					<td>
						<h2>Previous Updates</h2>
					</td>
				</tr>
			</table>
			<table style="padding-top: 30px;">
				<tr>
					<td>
						<?php foreach ($vd->previous_content->content as $content): ?>
							<?= $vd->esc($vd->cut($content->title, 80)) ?><br>
							<div style="text-align:right">
								<a href="<?= @$content->url ?>">read more</a><br><br>
							</div>
						<?php endforeach ?>				
					</td>
				</tr>
			</table>
			<?php endif ?>
			<br>
			<a href="<?= $vd->manage_subscr_link ?>">Manage your subscription</a>
			 to <?= $vd->newsroom->company_name ?> Updates<br><br>
			<span><a href="<?= $vd->unsub_link ?>">Click here</a> if you no longer wish to receive ANY updates 
				at all </span>
		</center>
	</body>
</html>