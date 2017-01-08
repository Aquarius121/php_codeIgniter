<div class="panel panel-default membership">
	<div class="panel-heading">
		<h3 class="panel-title">Membership Plan:  
			<strong><a href="manage/upgrade/packages">
				<?php if (($plan = Auth::user()->m_plan())): ?>
					<?php if ($plan->connected_item_id && Model_Item::find($plan->connected_item_id)->is_custom): ?>
						Custom
					<?php else: ?>
						<?= $vd->esc($plan->name) ?>
					<?php endif ?>
				<?php else: ?>
					<?= $vd->esc(Auth::user()->package_name()) ?>
				<?php endif ?>
			</a></strong>
			<small><a href="manage/upgrade/credits">Need More Credits?</a></small>
		</h3>
	</div>
	<div class="panel-body">
		<ul>

			<?php $visible = 0; ?>

			<?php if ($vd->pr_credits_basic->available): ?>
			<li><?= $vd->esc(Credit::full_name(Credit::TYPE_BASIC_PR)) ?> 
				<span><?= $vd->pr_credits_basic->available ?>
				<small class="text-muted smaller">credits</small></span></li>
			<?php $visible++; ?>
			<?php endif ?>

			<li><?= $vd->esc(Credit::full_name(Credit::TYPE_PREMIUM_PR)) ?> 
				<span><?= $vd->pr_credits_premium->available ?>
				<small class="text-muted smaller">credits</small></span></li>
			<?php $visible++; ?>

			<?php if ($vd->writing_credits->available): ?>
				<li><?= $vd->esc(Credit::full_name(Credit::TYPE_WRITING)) ?>
					<span><?= $vd->writing_credits->available ?>
					<small class="text-muted smaller">credits</small></span></li>
				<?php $visible++; ?>
			<?php endif ?>

			<?php foreach (Credit::list_common_types() as $type): ?>
				<?php $common_credits = Model_Limit_Common_Held::find_user(Auth::user(), $type); ?>
				<?php if ($common_credits->available()): ?>
					<li class="<?= value_if_test($visible >= 2, 'more-credits') ?>">
						<?= $vd->esc(Credit::full_name($type)) ?> <span><?= $common_credits->available() ?>
							<small class="text-muted smaller">credits</small></span></li>
					<?php $visible++; ?>
				<?php endif ?>
			<?php endforeach ?>

			<li><?= $vd->esc(Credit::full_name(Credit::TYPE_EMAIL)) ?> 
				<span><?= $vd->email_credits->available ?>
				<small class="text-muted smaller">credits</small></span></li>
			<?php $visible++; ?>

			<?php if ($visible > 3): ?>
				<li><span><a href="#" class="view-more-credits">+ View More</a></span></li>
			<?php endif ?>

		</ul>
	</div>
</div>

<script>

$(function() {

	var view_more_credits = $(".view-more-credits");
	var more_credits      = $(".more-credits");

	$(view_more_credits).on("click", function(ev) {
		ev.preventDefault();
		if (view_more_credits.hasClass('stretched-down')) {
			more_credits.slideUp();
			view_more_credits.removeClass('stretched-down');
			view_more_credits.html('+ View More');
		} else {
			more_credits.slideDown();
			view_more_credits.addClass('stretched-down');
			view_more_credits.html('- View Less');
		}
	});

});
	
</script>