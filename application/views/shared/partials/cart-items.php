<?php foreach($vd->cart->items() as $cart_item): ?>
	<?php if ($cart_item->hidden) continue; ?>
	<?php $cart_item_token = $cart_item->token() ?>
	<?php $distance = $cart_item->item()->renewal_distance(); ?>
	<?php $vd->has_renewal_distance = (bool) $distance;  ?>
	<p class="cart-item clearfix" data-token="<?= $cart_item_token ?>">
		<input type="hidden" class="hash" name="cart_item_hash[]" value="<?= $cart_item->hash() ?>" />
		<span class="cart-item-quantity"><?= (int) $cart_item->quantity ?></span>
		<span class="cart-item-name"><?= $vd->esc($cart_item->name) ?></span>
		<span class="cart-item-price"><?= $vd->cart->format($cart_item->price_total()) ?></span>
		<a class="remove-item" href="#"><i class="fa fa-trash icon-trash"></i></a>
		<?php if ($cart_item->is_quantity_unlocked): ?>
		<span class="cart-item-qui">
			<span class="minus"><i class="fa fa-chevron-down icon-chevron-down"></i></span>
			<span class="plus"><i class="fa fa-chevron-up icon-chevron-up"></i></span>
		</span>
		<?php endif ?>
		<?php if ($cart_item->has_visible_attached()): ?>
		<span class="cart-item-attached item-base">
			<span class="cart-item-name status-muted">
				<?= $vd->esc($cart_item->name) ?>
			</span>
			<span class="cart-item-price status-muted">
				<?= $vd->cart->format($cart_item->base_price_total()) ?>
			</span>
		</span>
		<?php endif ?>
		<?php foreach ($cart_item->attached as $atd): ?>
		<?php if ($atd->hidden) continue; ?>
		<span class="cart-item-attached child-cart-item cart-item-last-spacer" data-token="<?= $atd->token() ?>">
			<span class="cart-item-name">
				<i class="fa fa-plus icon-plus"></i>
				<?= $vd->esc($atd->name) ?>
				<?php if ($atd->quantity > 1): ?>
					(<?= $atd->quantity ?>)
				<?php endif ?>
			</span>
			<span class="cart-item-price">
				<?= $vd->cart->format($atd->price_total() * 
					$cart_item->quantity) ?>
			</span>
		</span>
		<?php endforeach ?>	
		<?php if ($cart_item->attached_discount()): ?>
		<span class="cart-item-attached cart-item-attached-discount cart-item-last-spacer">
			<span class="cart-item-name">
				<i class="fa fa-minus icon-minus"></i> Bundle Saver
			</span>
			<span class="cart-item-price">
				<?= $vd->cart->format($cart_item->attached_discount()) ?>
			</span>
		</span>
		<?php endif ?>		
		<?php $line_discount = $vd->cart->line_discount($cart_item_token) ?>
		<span class="cart-item-discount clearfix cart-item-last-spacer
				<?= value_if_test(!$line_discount, 'dnone') ?>">
			<span class="cart-item-name">
				<i class="fa fa-minus icon-minus"></i> 
				<?php if ($vd->cart->is_one_time_discount() && $vd->has_renewal_distance): ?>
					<span class="discount-type">One-Time</span> Discount
				<?php elseif ($vd->has_renewal_distance): ?>
					<span class="discount-type">Recurring</span> Discount
				<?php else: ?>
					<span class="discount-type">Line</span> Discount
				<?php endif ?>
			</span>	
			<span class="cart-item-price">
				<span class="discount"><?= $vd->cart->format($line_discount) ?></span>
			</span>
		</span>
		<?php if ($vd->has_renewal_distance): ?>
		<span class="cart-item-renewal cart-item-last-spacer">
			<?= $ci->renewal_distance_text($distance) ?>
		</span>
		<?php endif ?>
	</p>
<?php endforeach ?>