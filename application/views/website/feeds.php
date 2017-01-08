<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<header class="main-header">
					<h1>RSS Feeds</h1>
				</header>
			</div>
		</div>
	</div>
</main>

<article class="article">
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">			
				<section class="accordion" id="accordion">					
					
					<div class="panel-group">
						<div class="panel panel-default">							
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#inewswire_feeds">
										Newswire Feeds
									</a>
								</h4>
							</div>							
							<div id="inewswire_feeds" class="panel-collapse collapse in">
								<div class="panel-body">
									<dl class="accordion-row">
										<dt>
											Latest Press Releases (Premium)
										</dt>
										<dd>
											<ul class="rss-types-list">
												<li><a href="newsroom/rss/custom/premium-press-releases"><i class="fa fa-rss"></i> RSS</a></li>												
												<li><a href="http://add.my.yahoo.com/rss?url=<?= $ci->website_url() 
													?>newsroom/rss/custom/premium-press-releases"><i class="fa fa-plus-square-o"></i> Yahoo</a></li>
												<li><a href="http://my.msn.com/addtomymsn.armx?id=rss&amp;tt=CENTRALDIRECTORY&amp;ru=http://rss.msn.com&amp;ut=<?= 
													$ci->website_url() ?>newsroom/rss/custom/premium-press-releases"><i class="fa fa-plus-square-o"></i> MSN</a></li>
											</ul>
										</dd>
									</dl>
									<dl class="accordion-row">
										<dt>
											Latest Press Releases (All)
										</dt>
										<dd>
											<ul class="rss-types-list">
												<li><a href="newsroom/rss/custom/all-press-releases"><i class="fa fa-rss"></i> RSS</a></li>
												<li><a href="http://add.my.yahoo.com/rss?url=<?= $ci->website_url() 
													?>newsroom/rss/custom/all-press-releases"><i class="fa fa-plus-square-o"></i> Yahoo</a></li>
												<li><a href="http://my.msn.com/addtomymsn.armx?id=rss&amp;tt=CENTRALDIRECTORY&amp;ru=http://rss.msn.com&amp;ut=<?= 
													$ci->website_url() ?>newsroom/rss/custom/all-press-releases"><i class="fa fa-plus-square-o"></i> MSN</a></li>
											</ul>
										</dd>
									</dl>
								</div>
							</div>
						</div>
						<?php foreach ($vd->beat_groups as $group): ?>
							<?php if (!$group->is_listed) continue; ?>	
							<div class="">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#<?= $vd->esc($group->slug) ?>">
												<?= $vd->esc($group->name) ?>
											</a>
										</h4>
									</div>
									<div id="<?= $vd->esc($group->slug) ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<?php foreach ($group->beats as $beat): ?>
												<?php if (!$beat->is_listed) continue; ?>
												<dl class="accordion-row">
													<dt><?= $vd->esc($beat->name) ?></dt>
													<dd>
														<ul class="rss-types-list">
															<li><a href="newsroom/rss/beat/<?= $beat->slug ?>"><i class="fa fa-rss"></i> RSS</a></li>
															<li><a href="http://add.my.yahoo.com/rss?url=<?= $ci->website_url() 
																?>newsroom/rss/beat/<?= $beat->slug ?>"><i class="fa fa-plus-square-o"></i> Yahoo</a></li>
															<li><a href="http://my.msn.com/addtomymsn.armx?id=rss&amp;tt=CENTRALDIRECTORY&amp;ru=http://rss.msn.com&amp;ut=<?= 
																$ci->website_url() ?>newsroom/rss/beat/<?= $beat->slug ?>"><i class="fa fa-plus-square-o"></i> MSN</a></li>
														</ul>
													</dd>
												</dl>
											<?php endforeach ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach ?>
					</div>
					
					
						
				</section>
			</div>
		</div>
	</div>
</article>
	
<script>

$(function() {
	
	var collapse = $(".collapse");
	if (!collapse.length) return;
		
	collapse.collapse({ toggle: false });
	
	collapse.on("shown.bs.collapse", function () {
		var obj = $(this),
			parent_obj = $(this).closest(".panel"),
			icon = parent_obj.find(".accordion-arrow");					
		icon.removeClass("fa-caret-down").addClass("fa-caret-up");
	});
	
	collapse.on("hidden.bs.collapse", function () {
		var obj = $(this),
			parent_obj = $(this).closest(".panel"),
			icon = parent_obj.find(".accordion-arrow");
		icon.removeClass("fa-caret-up").addClass("fa-caret-down");
	});

});
	
</script>