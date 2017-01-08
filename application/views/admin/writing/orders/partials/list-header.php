<script>

$(function() {
	
	var archive_url = <?= json_encode("admin/writing/orders/archive/") ?>;	
	$(".archive-order-button").on("click", function(ev) {
		
		var _this = $(this);
		var message = "Are you sure that you want to archive this order?";
		bootbox.confirm(message, function(res) {
			if (!res) return true;
			var woc_id = _this.parents(".order-data").data("woc-id");
			var url = archive_url + woc_id;
			window.location = url;
		});
		
		ev.preventDefault();
		return false;
		
	});
	
});

</script>

<div class="page-header-spacer"></div>

<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate nav-tabs-compact
			writing-order-tabs" id="tabs">
			<li>
				<a data-on="^admin/writing/orders/all" 
					href="admin/writing/orders/all/<?= $vd->visible_bits ?><?= gstring() ?>">
					All
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/no_details" 
					href="admin/writing/orders/no_details/<?= $vd->visible_bits ?><?= gstring() ?>">
					No Details
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/assign" 
					href="admin/writing/orders/assign/<?= $vd->visible_bits ?><?= gstring() ?>">
					Assign
					<?php if ($vd->tab_count_assign): ?>
					<span class="menu-count"><?= $vd->tab_count_assign ?></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/pending"
					href="admin/writing/orders/pending/<?= $vd->visible_bits ?><?= gstring() ?>">
					Pending   
					<?php if ($vd->tab_count_pending): ?>
					<span class="menu-count"><?= $vd->tab_count_pending ?></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/review"
					href="admin/writing/orders/review/<?= $vd->visible_bits ?><?= gstring() ?>">
					Review 
					<?php if ($vd->tab_count_review): ?>
					<span class="menu-count"><?= $vd->tab_count_review ?></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/rejected" 
					href="admin/writing/orders/rejected/<?= $vd->visible_bits ?><?= gstring() ?>">
					Rejected
					<?php if ($vd->tab_count_rejected): ?>
					<span class="menu-count"><?= $vd->tab_count_rejected ?></span>
					<?php endif ?>
				</a>
			</li>
			<li>
				<a data-on="^admin/writing/orders/customer_review" 
					href="admin/writing/orders/customer_review/<?= $vd->visible_bits ?><?= gstring() ?>">
					Customer Review
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="pad-30h <?= value_if_test($vd->filters, 'marbot-20') ?>">
	<div class="row-fluid">
		<div class="span3">
			<div class="checkbox-container-box">
				<label class="checkbox-container louder">
					<input type="checkbox" class="visible-bits"
						value="<?= $ci->var_to_visible_bit('is_internal_order_visible') ?>" 
						<?= value_if_test($vd->is_internal_order_visible, 'checked') ?> />
					<span class="checkbox"></span>
					Direct Orders
				</label>
				<p class="muted">
					Orders made direct to Newswire without a reseller.
				</p>
			</div>
		</div>
		<div class="span3">
			<div class="checkbox-container-box">
				<label class="checkbox-container louder">
					<input type="checkbox" class="visible-bits"
						value="<?= $ci->var_to_visible_bit('is_admin_editor_visible') ?>" 
						<?= value_if_test($vd->is_admin_editor_visible, 'checked') ?> />
					<span class="checkbox"></span>
					Admin Editor
				</label>
				<p class="muted">
					Admin managed orders created on a reseller account.
				</p>
			</div>
		</div>	
		<div class="span3">
			<div class="checkbox-container-box">
				<label class="checkbox-container louder">
					<input type="checkbox" class="visible-bits"
						value="<?= $ci->var_to_visible_bit('is_reseller_editor_visible') ?>" 
						<?= value_if_test($vd->is_reseller_editor_visible, 'checked') ?> />
					<span class="checkbox"></span>
					Reseller Editor
				</label>
				<p class="muted">
					Reseller managed orders created on a reseller account.
				</p>
			</div>
		</div>
		<div class="span3">
			<div class="checkbox-container-box">
				<label class="checkbox-container louder">
					<input type="checkbox" class="visible-bits"
						value="<?= $ci->var_to_visible_bit('is_archive') ?>" 
						<?= value_if_test($vd->is_archive, 'checked') ?> />
					<span class="checkbox"></span>
					Archived Orders
				</label>
				<p class="muted">
					Show archived that have been archived instead of normal orders.
				</p>
			</div>
		</div>
	</div>
</div>

<script>

$(function() {
	
	var update_url = <?= json_encode(gstring("admin/writing/orders/{$vd->tab}/")) ?>;
	var visible_bits = $(".visible-bits");
	
	var update_visible_bits = function() {
		
		var total_bits = 0;
		visible_bits.each(function() {
			var _this = $(this);
			if (_this.is(":checked"))
				total_bits += parseInt(_this.val());
		});
		
		visible_bits.prop("disabled", true);
		var url = update_url + total_bits;
		window.location = url;
		
	};
	
	visible_bits.on("change", update_visible_bits);
	
});

</script>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>