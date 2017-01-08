<li class="radio-container-box marbot-15 combo-radio premium-plus-combo-radio">
	<div class="row">
		<div class="col-lg-8 col-md-12 col-sm-9 col-xs-12">
			<label class="radio-container louder block-visible">
				<?php $__bundle = Model_Content_Distribution_Bundle::instance(Model_Content_Distribution_Bundle::DIST_PREMIUM_PLUS_STATE) ?>
				<input type="radio" name="distribution_bundle" class="is-premium-radio distribution-PREMIUM-PLUS-STATE" 
					<?= value_if_test($vd->m_content && $vd->m_content->is_consume_locked(), 'disabled') ?>
					<?= value_if_test($vd->m_content && $vd->m_content->distribution_bundle()->bundle 
								== Model_Content::DIST_PREMIUM_PLUS_STATE, 'checked') ?>
					value="<?= $vd->esc(Model_Content::DIST_PREMIUM_PLUS_STATE) ?>" 
					data-available="<?= (int) $vd->credits_premium_plus_state ?>"
					data-includes-prnewswire="<?= (int) $__bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) ?>"
					data-prnewswire-words="<?= PRNewswire_Distribution::included_words(PRNewswire_Distribution::DIST_STATELINE) ?>"
					data-prnewswire-words-notification="<?= $vd->esc($ci->load->view('manage/publish/partials/distribution/premium_plus_stateline_words_notification')) ?>"
					data-name="<?= $__bundle->name() ?>"
					data-requires-press-contact="1"
					data-is-premium="1" />
				<span class="radio"></span>
				<span class="pr-type-title">
					<strong>Premium Plus</strong> with State Newsline
				</span>
			</label>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-3 col-xs-12">
			<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
				<span class="content-credit-info">
				<?php if ($credits = $vd->credits_premium_plus_state): ?>
				<span class="status-true">
					<?= $credits ?>
					<?= value_if_test($credits == 1, 'Credit', 'Credits') ?> Available
				</span>
				<?php else: ?>
				<span class="status-cost">
					$<?= number_format($vd->item_premium_plus_state->price, 2) ?>
				</span>
				<?php endif ?>
				</span>
			<?php endif ?>
		</div>

	</div>
	<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
	<p class="text-muted pr-type-detail">		
		Premium Plus with State Newsline helps connect you with the audiences to all of the major media groups across 
		your State or Region from Newspapers &amp; TV to Radio &amp; Digital publications including Reuters and Associated Press. 
		An additional fee is due if the word count exceeds 400 words, $<?= number_format($vd->item_pps_extra_100_words->price) ?> every 100 additional words.
		By selecting this option, you agree to PR Newswire's <a href="<?= $ci->website_url('pr-newswire-terms-and-conditions') ?>"
			class="status-info-text-muted" target="_blank">terms and conditions</a>. 
	</p>
	<?php endif ?>
</li>