<!-- #cart-data.cart-data -->
<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/cart.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<?php if ($vd->cart->is_clear()): ?>
	<h3 class="cart-empty-header"><span class="cart-empty">YOUR CART IS EMPTY</span></h3>
<?php else: ?>
	<h3 class="cart-title">YOUR CART</h3>
<?php endif ?>

<div class="cart-items-block" id="cart-items-block">
	<?= $ci->load->view('shared/partials/cart-items') ?>
</div>
<div class="coupon-code-block">
	<div id="coupon-code-block-link">
		<a href="#">Apply Coupon Code</a>
	</div>
	<div id="coupon-code-block-input" class="dnone">
		<input type="text" class="form-control nomarbot" name="cart_coupon_code"
			placeholder="Coupon Code" value="<?= @$vd->cart->coupon()->code ?>" />
		<input type="hidden" name="cart_coupon_id" id="cart-coupon-id" 
			value="<?= @$vd->cart->coupon()->id ?>" />
	</div>
	<div id="coupon-code-block-discount" class="dnone">
		Discount (<a href="#">remove</a>) <span class="total-discount">
			<span class="discount"><?= $vd->cart->format($vd->cart->discount()) ?></span>
			<span style="color: black">|</span> 
			<span class="discount-percent"><span><?= 
				number_format($vd->cart->discount_as_percent(), 0) ?></span>%</span>
		</span>
	</div>
</div>
<div class="total">Total <span class="total-value" id="total-cost"><?= 
	$vd->cart->format($vd->cart->total_with_discount()) ?></span></div>