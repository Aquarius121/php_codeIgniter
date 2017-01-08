<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row">
	<div class="content">
		<section>		
			<div>

				<div>					
					<ul class="summary-list pad-20h">
						<li>
							<dl>
								<dt>Order Number: </dt>
								<dd><?= $vd->m_pitch_order->id ?></dd>
							</dl>
							<dl>
								<dt>Company: </dt> 
								<dd>
									<a target="_blank" href="<?= Model_Newsroom::from_company_model($vd->m_company)->url('manage') ?>"><?= 
										$vd->esc($vd->m_company->name) ?></a>
								</dd>
							</dl>
							<?php if (Auth::is_admin_online()): ?>
							<dl>
								<dt>Customer: </dt>
								<dd>
									<a target="_blank" href="<?= $ci->website_url() ?>admin/users/view/<?= $vd->m_user->id ?>"><?= 
										$vd->esc($vd->m_user->name()) ?></a>
									<br />
									<span class="muted smaller"><?= $vd->esc($vd->m_user->email) ?></span>
								</dd>
							</dl>
							<?php endif ?>
						</li>
						<li>
							<dl>
								<dt>Order Date: </dt> 
								<dd>
									<?php $order = Date::out($vd->m_pitch_order->date_created); ?>
									<?= $order->format('M j, Y') ?>
								</dd>
							</dl>
							<dl>
								<dt>Delivery: </dt>
								<dd><?= $vd->esc(ucwords($vd->m_pitch_order->delivery)) ?></dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt>Selected Content:</dt> 
								<dd><?= Model_Content::full_type(@$vd->m_content->type) ?></dd>
							</dl>
							<dl>
								<dt>Title:</dt> 
								<dd><a href="<?= $vd->m_content->url() ?>" target="_blank">
									<?= $vd->esc(@$vd->m_content->title) ?></a></dd>
							</dl>												
						</li>
						<li>
							<?php if ($vd->m_pitch_order->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
							<dl>
								<dt>Selected Industry:</dt> 
								<dd><?= $vd->esc(@$vd->m_beat_1->name) ?></dd>
							</dl>												
							<?php if (@$vd->m_beat_2): ?>
							<dl>
								<dt>Second Industry:</dt> 
								<dd><?= $vd->esc(@$vd->m_beat_2->name) ?></dd>
							</dl>
							<?php endif ?>
							<?php endif ?>
							<dl>
								<dt>Keyword Describing Product or Service:</dt> 
								<dd><?= $vd->esc($vd->m_pitch_order->keyword) ?></dd>
							</dl>
						</li>
						<?php if ($vd->m_pitch_order->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
						<li>
							<dl>
								<dt>Location of your story:</dt> 
								<dd>
									<?= @$vd->esc(@$vd->m_pitch_order->city) ?>, 
									<?= $vd->esc(@$vd->m_state->name) ?>
								</dd>
							</dl>												
							<dl>
								<dt>Selected distribution:</dt> 
								<dd><?= Model_Pitch_Order::distribution_title($vd->m_pitch_order->distribution) ?>
							</dl>
						</li>
						<?php endif ?>
						<li>
							<dl>                                
								<dd>
									<p class="pitch-highlight">
										<strong>Pitch Highlight:</strong> 
										<?= nl2br($vd->esc(@$vd->m_pitch_order->pitch_highlight)) ?>
									</p>
								</dd>
							</dl>
							<?php if (@$vd->m_pitch_order->additional_comments): ?>
                            <dl>
								<dt>Additional Comments:</dt> 
								<dd>
									<?= nl2br($vd->esc(@$vd->m_pitch_order->additional_comments)) ?>
								</dd>
							</dl>
							<?php endif ?>
						</li>
					</ul>
				</div>			
			</div>
							
		</section>
	</div>
</div>