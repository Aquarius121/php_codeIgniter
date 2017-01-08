<?php if (($vd->m_content && !$vd->m_content->is_premium) ||
			Auth::user()->pr_credits_basic()): ?>
<li class="radio-container-box marbot-15 combo-radio">
	<div class="row">
		<div class="col-lg-7 col-xs-12">
			<label class="radio-container louder">
				<?php $__bundle = Model_Content_Distribution_Bundle::instance(Model_Content_Distribution_Bundle::DIST_BASIC) ?>
				<input type="radio" name="distribution_bundle" value="0" class="is-premium-radio distribution-BASIC"
					<?= value_if_test($vd->m_content &&   $vd->m_content->is_consume_locked(), 'disabled') ?>
					<?= value_if_test($vd->m_content && $vd->m_content->distribution_bundle()->bundle 
								== Model_Content::DIST_BASIC, 'checked') ?>
					value="<?= $vd->esc(Model_Content::DIST_BASIC) ?>" 
					data-available="<?= (int) $vd->credits_basic ?>"
					data-includes-prnewswire="<?= (int) $__bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) ?>"
					data-name="<?= $__bundle->name() ?>"
					data-is-premium="0" />
				<span class="radio"></span>
				<span class="pr-type-title"><strong>Basic</strong> Press Release</span>
			</label>
		</div>
		<div class="col-lg-5 col-xs-12 actions">
		<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
			<span class="content-credit-info">
			<?php if (($credits = (int) $vd->credits_basic)): ?>
			<span class="status-true">
				<?= $credits ?>
				<?= value_if_test($credits == 1, 'Credit', 'Credits') ?> Available
			</span>
			<?php elseif (($next = Auth::user()->pr_credits_basic_stat()->next_available) !== false): ?>
				Next Credit: <?= $next->format('jS F') ?>
			<?php endif ?>
			</span>
		<?php endif ?>
		</div>
	</div>	
	<?php if (!$vd->m_content || !$vd->m_content->is_consume_locked()): ?>
	<p class="text-muted pr-type-detail">
		A basic release with limited features and distribution. 
	</p>
	<?php endif ?>
</li>
<?php endif ?>