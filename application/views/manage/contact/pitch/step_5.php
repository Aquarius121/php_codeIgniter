<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-12">
				<h2>Order Confirmation</h1>
			</div>
		</div>
	</header>
	
	<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
		<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
		<div class="row">
			<div class="col-lg-8 col-md-7">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<ul class="summary-list">
									<li>
										<dl>
											<dt>Content:</dt> 
											<dd><a href="<?= $ci->website_url($vd->m_content->url()) ?>"><?= $vd->esc(@$vd->m_content->title) ?></a></dd>
										</dl>
										<dl>
											<dt>Estimated Delivery Date: </dt> 
											<dd>
												<?php if (@$vd->pw_raw_data->delivery == Model_Pitch_Order::DELIVERY_RUSH): ?>
													<?= $vd->date_after_24_hours->format('F d, Y') ?> (RUSH)
												<?php else: ?>
													<?= $vd->date_after_3_days->format('F d, Y') ?>
												<?php endif ?>
											</dd>
										</dl>
									</li>
									<li>
										<?php if (@$vd->pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
										<dl>
											<dt>Selected Industry:</dt> 
											<dd><?= @$vd->beat_1_name ?></dd>
										</dl>
										<?php if (@$vd->beat_2_name): ?>
										<dl>
											<dt>Second Industry:</dt> 
											<dd><?= @$vd->beat_2_name ?></dd>
										</dl>
										<?php endif ?>
										<?php endif ?>
										<dl>
											<dt>Keyword:</dt> 
											<dd><?= $vd->esc(@$vd->pw_raw_data->keyword) ?></dd>
										</dl>
									</li>
									<?php if (@$vd->pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
									<li>
										<dl>
											<dt>Location of your story:</dt> 
											<dd>
												<?= $vd->esc(@$vd->pw_raw_data->city) ?>, 
												<?= $vd->esc(@$vd->state_name) ?>
											</dd>
										</dl>												
										<dl>
											<dt>Selected distribution:</dt> 
											<dd><?= $vd->esc(@$vd->distribution_title) ?></dd>
										</dl>
									</li>
									<?php endif ?>
									<li>
										<dl>                                
											<dd>
												<p class="pitch-highlight">
													<strong>Pitch Highlight:</strong> 
													<?= nl2br($vd->esc(@$vd->pw_raw_data->pitch_highlight)) ?>
												</p>
											</dd>
										</dl>
										<dl>
											<dt>Additional Comments:</dt> 
											<dd>
												<?= nl2br($vd->esc(@$vd->pw_raw_data->additional_comments)) ?>
											</dd>
										</dl>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-4 col-md-5">
				<aside class="col-lg-12 alert alert-info">
					<div class="marbot-10">
						We have received your order and will start work on it shortly. We will send an email
						when the pitch is ready or if we need any additional details from you.
					</div>
					<div class="row-fluid">
						<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/1"
							class="col-lg-12 ta-center btn btn-sm btn-info span8"><b>Edit Order Details</b></a>
					</div>
				</aside>
			</div>
		</div>
	</form>
</div>