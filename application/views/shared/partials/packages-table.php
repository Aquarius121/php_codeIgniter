<?php $ci->load->view('website/partials/packages-table-loader') ?>

<section class="pricing-packages m<?= $vd->period ?>">
	<div class="row">
		<?php if ($vd->free_column_enabled): ?>
		<div class="col-sm-12 col-pp-gold">
		<?php else: ?>
		<div class="col-sm-1"></div>
		<div class="col-sm-10">
		<?php endif ?>
			<div class="row">

				<?php if ($vd->free_column_enabled): ?>
				<div class="col-sm-3 col-pp-free">
					<section class="pp-content pp-free">
						<header class="pp-header">
							<h2>Free Account</h2>
							<p>Recommended for Beginners</p>
						</header>
						<section class="pp-price">
							<section class="pp-price-block">
								<span class="pp-price-value">0</span>
							</section>
						</section>
						<a href="register" class="btn btn-success">Register Now</a>
						<section class="pp-features">
							<h3>Features</h3>
							<ul class="pp-list-features">
								<li><strong>1</strong> Basic PR <em>free</em></li>
							</ul>
							<ul class="pp-list-details">
								<li>Simple way to get your news online</li>
								<li>Published on Newswire's News Center</li>
								<li>Inclusion in Newswire's RSS feed</li>
							</ul>
						</section>
						
					</section>
				</div>
				<?php endif ?>

				<?php if ($vd->free_column_enabled): ?>
				<div class="col-sm-3 col-pp-silver">
				<?php else: ?>
				<div class="col-sm-4 col-pp-silver">
				<?php endif ?>
					<section class="pp-content pp-silver">
						<header class="pp-header">
							<h2>Professional</h2>
						</header>
						<section class="pp-price">
							<section class="pp-price-block">
								<span class="pp-price-value">$<?= number_format($vd->silver->price, 0) ?></span>
							</section>
							<?php if ($vd->is_user_panel): ?>
							<a href="manage/order/item/<?= $vd->silver->id ?>" class="btn btn-primary">Subscribe Now</a>
							<?php else: ?>
							<a href="order/item/<?= $vd->silver->id ?>" class="btn btn-success">Subscribe Now</a>
							<?php endif ?>
						</section>
						<section class="pp-features">
							<h3>Includes</h3>
							<ul class="pp-list-features">
								<li><p><a data-toggle="collapse" data-target=".def-pr"><?= $vd->silver->premium_pr_credits ?> Premium PRs per month <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-pr">
										<li>Distribution to Newswire’s Premium network of 250+ Media Outlets</li>
										<li>Feature rich format with Links, Images, and Video Embed</li>
										<li>One-Click Auto Campaign to Industry Specific Publications</li>
										<li>Inclusion in Newswire’s Industry RSS Feeds</li>
										<li>Published in Newswire News Center Page</li>
										<li>Detailed Analytics Report</li>
									</ul>
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-newsroom"><?= $vd->silver->newsroom_credits ?> <?=
									$vd->silver->newsroom_credits == 1 ? 'Newsroom' : 'Newsrooms' ?> <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-newsroom">
										<li>Showcase your company’s news, information and content efforts in one centralized location</li>
										<li>SEO optimized URL address that ranks in search</li>
										<li>Clip and add external content to your custom newsroom</li>
									</ul>	
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-credits"><?= $vd->silver->email_credits ?> Media Outreach Credits <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-credits">
										<li>Select targeted media by filtering our Media Outreach Database of over 400K media contacts to reach your intended audience</li>
										<li>Gain insights on email open rates</li>
									</ul>
								</li>
							</ul>
							<h3>Member Benefits</h3>
							<ul class="pp-list-details">
								<li>10% Member Discount on all Newswire Services such as Writing, Editing and Media Outreach Campaigns</li>
								<li>Discounts on extended distribution channels such as PR Newswire and other partner networks</li>
							</ul>
						</section>
					</section>
				</div>

				<?php if ($vd->free_column_enabled): ?>
				<div class="col-sm-3 col-pp-gold">
				<?php else: ?>
				<div class="col-sm-4 col-pp-gold">
				<?php endif ?>
					<section class="pp-content pp-gold">
						<header class="pp-header">
							<span class="label label-default">Most Popular</span>
							<h2>Small Business</h2>
						</header>
						<section class="pp-price">
							<section class="pp-price-block">
								<span class="pp-price-value">$<?= number_format($vd->gold->price, 0) ?></span>
							</section>
							<?php if ($vd->is_user_panel): ?>
							<a href="manage/order/item/<?= $vd->gold->id ?>" class="btn btn-primary">Subscribe Now</a>
							<?php else: ?>
							<a href="order/item/<?= $vd->gold->id ?>" class="btn btn-success">Subscribe Now</a>
							<?php endif ?>
						</section>
						<section class="pp-features">
							<h3>Includes</h3>
							<ul class="pp-list-features">
								<li><p><a data-toggle="collapse" data-target=".def-pr"><?= $vd->gold->premium_pr_credits ?> Premium PRs per month   <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-pr">
										<li>Distribution to Newswire’s Premium network of 250+ Media Outlets</li>
										<li>Feature rich format with Links, Images, and Video Embed</li>
										<li>One-Click Auto Campaign to Industry Specific Publications</li>
										<li>Inclusion in Newswire’s Industry RSS Feeds</li>
										<li>Published in Newswire News Center Page</li>
										<li>Detailed Analytics Report</li>
									</ul>		
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-newsroom"><?= $vd->gold->newsroom_credits ?> <?=
									$vd->gold->newsroom_credits == 1 ? 'Newsroom' : 'Newsrooms' ?> <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-newsroom">
										<li>Showcase your company’s news, information and content efforts in one centralized location</li>
										<li>SEO optimized URL address that ranks in search</li>
										<li>Clip and add external content to your custom newsroom</li>
									</ul>	
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-credits"><?= $vd->gold->email_credits ?> Media Outreach Credits <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-credits">
										<li>Select targeted media by filtering our Media Outreach Database of over 400K media contacts to reach your intended audience</li>
										<li>Gain insights on email open rates</li>
									</ul>
								</li>
							</ul>
							<h3>Member Benefits</h3>
							<ul class="pp-list-details">
								<li>15% Member Discount on all Newswire Services such as Writing, Editing and Media Outreach Campaigns</li>
								<li>Discounts on extended distribution channels such as PR Newswire and other partner networks</li>
							</ul>
						</section>
					</section>
				</div>

				<?php if ($vd->free_column_enabled): ?>
				<div class="col-sm-3 col-pp-platinum">
				<?php else: ?>
				<div class="col-sm-4 col-pp-platinum">
				<?php endif ?>
					<section class="pp-content pp-platinum">
						<header class="pp-header">
							<h2>Enterprise</h2>
						</header>
						<section class="pp-price">
							<section class="pp-price-block">
								<span class="pp-price-value">$<?= number_format($vd->platinum->price, 0) ?></span>
							</section>
							<?php if ($vd->is_user_panel): ?>
							<a href="manage/order/item/<?= $vd->platinum->id ?>" class="btn btn-primary">Subscribe Now</a>
							<?php else: ?>
							<a href="order/item/<?= $vd->platinum->id ?>" class="btn btn-success">Subscribe Now</a>
							<?php endif ?>
						</section>
						<section class="pp-features">
							<h3>Includes</h3>
							<ul class="pp-list-features">
								<li><p><a data-toggle="collapse" data-target=".def-pr"><?= $vd->platinum->premium_pr_credits ?> Premium PRs per month  <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-pr">
										<li>Distribution to Newswire’s Premium network of 250+ Media Outlets</li>
										<li>Feature rich format with Links, Images, and Video Embed</li>
										<li>One-Click Auto Campaign to Industry Specific Publications</li>
										<li>Inclusion in Newswire’s Industry RSS Feeds</li>
										<li>Published in Newswire News Center Page</li>
										<li>Detailed Analytics Report</li>
									</ul>		
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-newsroom"><?= $vd->platinum->newsroom_credits ?> <?=
									$vd->platinum->newsroom_credits == 1 ? 'Newsroom' : 'Newsrooms' ?> <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-newsroom">
										<li>Showcase your company’s news, information and content efforts in one centralized location</li>
										<li>SEO optimized URL address that ranks in search</li>
										<li>Clip and add external content to your custom newsroom</li>
									</ul>	
								</li>
								<li><p><a data-toggle="collapse" data-target=".def-credits"><?= $vd->platinum->email_credits ?> Media Outreach Credits <i class="fa fa-info-circle"></i></a></p>
									<ul class="collapse def-credits">
										<li>Select targeted media by filtering our Media Outreach Database of over 400K media contacts to reach your intended audience</li>
										<li>Gain insights on email open rates</li>
									</ul>
								</li>
							</ul>
							<h3>Member Benefits</h3>
							<ul class="pp-list-details">
								<li>25% Member Discount on all Newswire Services such as Writing, Editing and Media Outreach Campaigns</li>
								<li>Discounts on extended distribution channels such as PR Newswire and other partner networks</li>
								<li>Full access to our media database and press contacts.</li>
							</ul>
						</section>
					</section>
				</div>

			</div>
		</div>

	</div>
</section>