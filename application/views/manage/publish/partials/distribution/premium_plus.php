<li class="radio-container-box marbot-15 combo-radio premium-plus-combo-radio">
	<div class="row">
		<div class="col-lg-8 col-md-12 col-sm-9 col-xs-12">
			<label class="radio-container louder block-visible">
				<?php $__bundle = Model_Content_Distribution_Bundle::instance(Model_Content_Distribution_Bundle::DIST_PREMIUM_PLUS) ?>
				<input type="radio" name="distribution_bundle" class="is-premium-radio distribution-PREMIUM-PLUS" 
					<?= value_if_test($vd->m_content && $vd->m_content->is_consume_locked(), 'disabled') ?>
					<?= value_if_test($vd->m_content && $vd->m_content->distribution_bundle()->bundle
								== Model_Content::DIST_PREMIUM_PLUS, 'checked') ?>
					value="<?= $vd->esc(Model_Content::DIST_PREMIUM_PLUS) ?>" 
					data-available="<?= (int) $vd->credits_premium_plus ?>"
					data-includes-prnewswire="<?= (int) $__bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) ?>"
					data-includes-prnewswire-featured="1"
					data-name="<?= $__bundle->name() ?>"
					data-is-premium="1" />
				<span class="radio"></span>
				<span class="pr-type-title">
					<strong>Premium Plus</strong>
				</span>
			</label>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-3 col-xs-12">
			<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
				<span class="content-credit-info">
				<?php if ($credits = $vd->credits_premium_plus): ?>
				<span class="status-true">
					<?= $credits ?>
					<?= value_if_test($credits == 1, 'Credit', 'Credits') ?> Available
				</span>
				<?php else: ?>
				<span class="status-cost">
					$<?= number_format($vd->item_premium_plus->price, 2) ?>
				</span>
				<?php endif ?>
				</span>
			<?php endif ?>
		</div>

	</div>
	<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
	<p class="text-muted pr-type-detail">
		Premium Plus allows you access to both the premium distribution and PR Newswire's online distribution network of over 
		4,500+ outlets including Yahoo News! Yahoo Finance, Business Journals and many more. 
		By selecting this option, you agree to 
		PR Newswire's <a href="<?= $ci->website_url('pr-newswire-terms-and-conditions') ?>"
			class="status-info-text-muted" target="_blank">terms and conditions</a>. 
	</p>
	<?php endif ?>
</li>