<li class="radio-container-box marbot-15 combo-radio">
	<div class="row">
		<div class="col-lg-7 col-xs-12">
			<label class="radio-container louder block-visible">
				<?php $__bundle = Model_Content_Distribution_Bundle::instance(Model_Content_Distribution_Bundle::DIST_PREMIUM) ?>
				<input type="radio" name="distribution_bundle" class="is-premium-radio distribution-PREMIUM" 
					<?= value_if_test($vd->m_content && $vd->m_content->is_consume_locked(), 'disabled') ?>
					<?= value_if_test($vd->m_content && $vd->m_content->distribution_bundle()->bundle 
								== Model_Content::DIST_PREMIUM, 'checked') ?>
					value="<?= $vd->esc(Model_Content::DIST_PREMIUM) ?>" 
					data-available="<?= (int) $vd->credits_premium ?>"
					data-includes-prnewswire="<?= (int) $__bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) ?>"
					data-name="<?= $__bundle->name() ?>"
					id="is-premium-switch" data-is-premium="1" />
				<span class="radio"></span>
				<span class="pr-type-title"><strong>Premium</strong></span>
			</label>
		</div>
		<div class="col-lg-5 col-xs-12">
			<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
				<span class="content-credit-info">
				<?php if ($credits = $vd->credits_premium): ?>
				<span class="status-true">
					<?= $credits ?> 
					<?= value_if_test($credits == 1, 'Credit', 'Credits') ?> Available
				</span>
				<?php else: ?>
				<span class="status-cost">
					$<?= number_format($vd->item_premium->price, 2) ?>
				</span>
				<?php endif ?>
				</span>
			<?php endif ?>
		</div>
	</div>
	<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
	<p class="text-muted pr-type-detail">
		Premium Distribution allows you to include images, hyperlinks, embedded video to a network of news and
		media outlets across the United States to sites like Google News, Digital Journal, Press Enterprise and many more.
	</p>
	<?php endif ?>
</li>