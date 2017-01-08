<form action="manage/writing/sales/order" method="post">
	<div class="row nomar">
		<div class="col-lg-12 nopad">
			<section class="buy-box buy-box-writing buy-box-compact buy-box-top">
				<p class="ta-left"><i class="fa fa-fw fa-pencil"></i> Get started today for only 
					<strong>$<?= number_format($vd->writing_credit->price, 0) ?></strong>
					<button type="submit" class="btn btn-primary btn-lg writing-get-started">
						Get Started <i class="fa fa-caret-right"></i></button></p>
			</section>
		</div>
	</div>

	<div class="container-fluid writing-sales-page">
		
		<div class="row">
			<div class="col-lg-1">
			</div>
			<div class="col-lg-10">
				<header class="main-header marbot-20">
					<h1>Press Release Writing Service</h1>
					<p class="pr-writing-service tagline">
						<span>Our seasoned staff writers can create a compelling press release to </span>
						<span>ensure we get to the heart of your story.</span>
					</p>
				</header>

				<div class="ta-center marbot-30 video-lg">
					<?php $video = new Video_Youtube('PkY3JXq6TAg'); ?>
					<?= $video->render(640, 360, array('autoplay' => 1)) ?>
				</div>

				<script>
				
				$(function(){

					var resize_video_frame = function () {

						var document_width = $('#wrapper').width();
						var video_width = 640;
						var video_height = 360;

						if (document_width < 450)
						{
							video_width = 280;
							video_height = 156;
						}

						else if (document_width < 700)
						{
							video_width = 460;
							video_height = 258;
						}

						var video_iframes = document.getElementsByClassName("video-youtube");
						video_iframes[0].width = video_width;
						video_iframes[0].height = video_height;
					};


					resize_video_frame(); 
					$( window ).resize(function() {
						resize_video_frame();
					});

				})
				</script>

			</div>
			<div class="col-lg-1"></div>
		</div>

		
		<div class="row">
			<div class="col-lg-1">
			</div>
			<div class="col-lg-10">
				<div class="row features-content">
					<div class="col-lg-1"></div>
					<div class="col-lg-5 marbot-20">
						<h3><i class="fa fa-fw fa-check-square"></i> No Guesswork</h3>
						<p>
							We take the guesswork out of press release writing. 
							Our experienced staff will create a professionally 
							written press release to engage your audience and 
							express your message. We take care of everything 
							so you can focus on your business.
						</p>
					</div>
					<div class="col-lg-5 marbot-20">
						<h3><i class="fa fa-fw fa-file"></i> AP Writing Style</h3>
						<p>
							Our writing team complies with all AP style rules 
							to properly format your press release. They have 
							each been specifically trained in AP style in order 
							to provide professionally written press releases 
							for your story. 
						</p>
					</div>
				</div>
				<div class="row features-content">
					<div class="col-lg-1"></div>
					<div class="col-lg-5 marbot-20">
						<h3><i class="fa fa-fw fa-refresh"></i> Transparency</h3>
						<p>
							We will send you a draft of your press release before 
							sending it for publishing. This allows you to make any 
							edits or revisions to the press release. This ensures 
							your complete satisfaction before the press release 
							is distributed.
						</p>
					</div>
					<div class="col-lg-5 marbot-20">
						<h3><i class="fa fa-fw fa-clock-o"></i> Quick Turnaround</h3>
						<p>
							After placing your order, we’ll start the writing process 
							in minutes. Fill out a simple company details form and let 
							us do the rest. We complete and send your press release to 
							you within 24-48 hours after receiving your order. 
						</p>
					</div>
				</div>
			</div>
			<div class="col-lg-1">
			</div>
		</div>
		
		
		<div class="row form-group">
			<div class="col-lg-4"></div>
			<div class="col-lg-4 ta-center">
				<button type="submit" class="btn btn-primary writing-get-started">
					Get Started <i class="fa fa-caret-right"></i></button>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-lg-3"></div>
			<div class="col-lg-6 ta-center smaller">
				<label class="checkbox-container">
					<input type="checkbox" name="add_distribution" value="1" checked /> 
					<span class="checkbox"></span>
					<span>Add Premium Distribution</span>
				</label>
			</div>
		</div>
		
	</div>

</form>