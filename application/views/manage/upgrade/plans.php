<div class="container-fluid membership-plans">
	<header>
		<div class="row">
			<div class="col-lg-6">
				<h2>Account Upgrades</h2>
			</div>
			<div class="col-lg-6 actions">
			</div>
		</div>
	</header>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<div class="panel-heading">
					<ul class="nav nav-tabs">
						<li class="active"><a href="manage/upgrade/plans">Membership Plans</a></li>
						<li><a href="manage/upgrade/credits">Additional Credits</a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="">
						<div class="panel-body">
							<div class="row">								
								<div class="col-lg-2"></div>
								<div class="col-lg-8">
									<h2 class="title text-center">Flexible Pricing Made Simple</h2>
									<p class="text-center">Select the best plan that suits your exact needs. We can help in all areas of your content marketing campaigns. Our team is here to help you.</p>
									<div class="plan-selection marbot-60">
										<div class="btn-group nav-activate" role="group">
											<?php if ($vd->period === 1): ?>
											<a class="btn btn-primary" href="manage/upgrade/plans">Monthly</a>
											<a class="btn btn-default" href="manage/upgrade/plans/annual">Annual <span class="text-muted">10% Off</span></a>
											<?php else: ?>
											<a class="btn btn-default" href="manage/upgrade/plans">Monthly</a>
											<a class="btn btn-primary" href="manage/upgrade/plans/annual">Annual <span class="text-muted">10% Off</span></a>
											<?php endif ?>
										</div>
									</div>
								</div>
							</div>
							<?= $ci->load->view('shared/partials/packages-table'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>