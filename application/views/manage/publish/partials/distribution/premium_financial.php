<li class="radio-container-box marbot-15 combo-radio combo-radio-last">
	<div class="row">
		<div class="col-lg-8 col-md-12 col-sm-9 col-xs-12">
			<label class="radio-container louder block-visible">
				<?php $__bundle = Model_Content_Distribution_Bundle::instance(Model_Content_Distribution_Bundle::DIST_PREMIUM_FINANCIAL) ?>
				<input type="radio" name="distribution_bundle" class="is-premium-radio distribution-PREMIUM-FINANCIAL" 
					<?= value_if_test($vd->m_content && $vd->m_content->is_consume_locked(), 'disabled') ?>
					<?= value_if_test($vd->m_content && $vd->m_content->distribution_bundle()->bundle 
								== Model_Content::DIST_PREMIUM_FINANCIAL, 'checked') ?>
					value="<?= $vd->esc(Model_Content::DIST_PREMIUM_FINANCIAL) ?>" 
					data-available="<?= (int) $vd->credits_premium_financial ?>"
					data-includes-prnewswire="<?= (int) $__bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) ?>"
					data-name="<?= $__bundle->name() ?>"
					data-is-premium="1" />
				<span class="radio"></span>
				<span class="pr-type-title">
					<strong>Premium Financial</strong>
				</span>
			</label>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-3 col-xs-12">
			<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
				<span class="content-credit-info">
				<?php if ($credits = $vd->credits_premium_financial): ?>
				<span class="status-true">
					<?= $credits ?>
					<?= value_if_test($credits == 1, 'Credit', 'Credits') ?> Available
				</span>
				<?php else: ?>
				<span class="status-cost">
					$<?= number_format($vd->item_premium_financial->price, 2) ?>
				</span>
				<?php endif ?>
				</span>
			<?php endif ?>
		</div>

	</div>
	<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
	<p class="text-muted pr-type-detail">
		Premium Financial meets regulatory disclosure requirements for publicly traded companies while getting your
		news in front of the people that drive your business across the various trading platforms such as TheStreet,
		MarketWatch, Scottrade, MorningStar, Zacks, Investors Hub and more.
	</p>
	<?php endif ?>
</li>