<?php

$other_items = array();

if (isset($vd->other_items))
{
	foreach ($vd->other_items as $k => $credit)
	{
		if (!$credit) continue;

		if ($k === Store::ITEM_PREMIUM_PLUS) 
		{
			$vd->premium_plus_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_PREMIUM_PLUS_STATE) 
		{
			$vd->premium_plus_state_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_PREMIUM_PLUS_NATIONAL) 
		{
			$vd->premium_plus_national_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_PREMIUM_FINANCIAL) 
		{
			$vd->premium_financial_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_MEDIA_OUTREACH) 
		{
			$vd->media_outreach_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_PITCH_WRITING) 
		{
			$vd->pitch_writing_credit = $credit;
			continue;
		}

		if ($k === Store::ITEM_PR_REVISION_WRITING) 
		{
			$vd->revision_writing_credit = $credit;
			continue;
		}

		$other_items[$k] = $credit;
	}
}

?>

<div class="container-fluid additional-credits">
	<header>
		<div class="row">
			<div class="col-lg-6">
					<h2>Account Upgrades</h2>
			</div>
			<div class="col-lg-6 actions">
			</div>
		</div>
	</header>
	<div class="row">
		<div class="col-lg-8 form-col-1">
			<div class="panel with-nav-tabs panel-default">
				<div class="panel-heading">
					<ul class="nav nav-tabs">
						<li><a href="manage/upgrade/plans">Membership Plans</a></li>
						<li class="active"><a href="manage/upgrade/credits">Additional Credits</a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="tab2default">
						<div class="panel-body">
							<h2 class="title text-center">Need more credits?</h2>
							<p class="text-center">You can add additional credits under your existing membership.</p>
							<hr>

							<?php if ($vd->pr_credit || 
							 	$vd->premium_plus_credit || 
							 	$vd->premium_plus_state_credit ||
							 	$vd->premium_financial): ?>
							<div class="row package">
								<div class="col-md-12">
									<h3>Press Releases</h3>
								</div>
							</div>
							<?php endif ?>

								<?php if ($vd->pr_credit): ?>
								<div class="row package" data-order-url="<?= $vd->pr_credit->order_url(
										$vd->order_url_prefix, $vd->pr_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Premium Press Release
										</h4>
										<p class="marbot">Submit premium press releases for distribution.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->pr_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->premium_plus_credit): ?>
								<div class="row package" data-order-url="<?= $vd->premium_plus_credit->order_url(
										$vd->order_url_prefix, $vd->premium_plus_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Premium Plus
										</h4>
										<p class="marbot">Submit premium plus press releases for distribution.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->premium_plus_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->premium_plus_state_credit): ?>
								<div class="row package" data-order-url="<?= $vd->premium_plus_state_credit->order_url(
										$vd->order_url_prefix, $vd->premium_plus_state_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Premium Plus with State Newsline
										</h4>
										<p class="marbot">Submit premium plus press releases for distribution with state newsline selection.
											Additional fees will apply for releases over 400 words.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->premium_plus_state_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->premium_plus_national_credit): ?>
								<div class="row package" data-order-url="<?= $vd->premium_plus_national_credit->order_url(
										$vd->order_url_prefix, $vd->premium_plus_national_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Premium Plus National
										</h4>
										<p class="marbot">Submit premium plus press releases for distribution with national selection.
											Additional fees will apply for releases over 400 words.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->premium_plus_national_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->premium_financial_credit): ?>
								<div class="row package" data-order-url="<?= $vd->premium_financial_credit->order_url(
										$vd->order_url_prefix, $vd->premium_financial_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Premium Financial
										</h4>
										<p class="marbot">Submit premium financial releases for distribution. 
											This is for publically traded companies.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->premium_financial_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

							<?php if ($vd->email_credit || $vd->media_outreach_credit): ?>
							<hr>
							<div class="row package">
								<div class="col-md-12">
									<h3>Media Outreach</h3>
								</div>
							</div>
							<?php endif ?>

								<?php if ($vd->email_credit): ?>
								<div class="row package" data-order-url="<?= $vd->email_credit->order_url(
										$vd->order_url_prefix, $vd->email_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Media Outreach
										</h4>
										<p class="marbot">Use email credits for email campaigns. 
											Each media outreach credit allows you to send 
											an email to 1 media contact.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="100" value="100" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->email_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->media_outreach_credit): ?>
								<div class="row package" data-order-url="<?= $vd->media_outreach_credit->order_url(
										$vd->order_url_prefix, $vd->media_outreach_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Targeted Media Campaign
										</h4>
										<p class="marbot"><?= $vd->esc($vd->media_outreach_credit->help_text) ?></p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->media_outreach_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

							<?php if ($vd->writing_credit || $vd->pitch_writing_credit): ?>
							<hr>
							<div class="row package">
								<div class="col-md-12">
									<h3>Writing Services</h3>
								</div>
							</div>
							<?php endif ?>

								<?php if ($vd->writing_credit): ?>
								<div class="row package" data-order-url="<?= $vd->writing_credit->order_url(
										$vd->order_url_prefix, $vd->writing_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Press Release Writing
										</h4>
										<p class="marbot">Credits for press release writing.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->writing_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->revision_writing_credit): ?>
								<div class="row package" data-order-url="<?= $vd->revision_writing_credit->order_url(
										$vd->order_url_prefix, $vd->revision_writing_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											PR Revision Writing
										</h4>
										<p class="marbot">Have us professionally revise your press release.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->revision_writing_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php if ($vd->pitch_writing_credit): ?>
								<div class="row package" data-order-url="<?= $vd->pitch_writing_credit->order_url(
										$vd->order_url_prefix, $vd->pitch_writing_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Pitch Writing
										</h4>
										<p class="marbot"><?= $vd->esc($vd->pitch_writing_credit->help_text) ?></p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->pitch_writing_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

							<?php if ($vd->newsroom_credit ||
								count($other_items)): ?>
							<hr>
							<div class="row package">
								<div class="col-md-12">
									<h3>Other Items</h3>
								</div>
							</div>
							<?php endif ?>

								<?php if ($vd->newsroom_credit): ?>
								<div class="row package" data-order-url="<?= $vd->newsroom_credit->order_url(
										$vd->order_url_prefix, $vd->newsroom_credit->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											Newsroom Activation
										</h4>
										<p class="marbot">Newsroom credits automatically renew until cancelled.</p>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button"><i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button"><i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $vd->newsroom_credit->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endif ?>

								<?php foreach ($other_items as $k => $item): ?>
								<div class="row package" data-order-url="<?= $item->order_url(
										$vd->order_url_prefix, $item->generate_secret(1), 0) ?>">
									<div class="col-md-5">
										<h4>
											<?= $vd->esc($item->name) ?>
										</h4>
										<?php if ($item->help_text): ?>
										<p><?= $vd->esc($item->help_text) ?></p>
										<?php else: ?>
										<p>Extra credits for this product.</p>
										<?php endif ?>
									</div>
									<div class="col-md-7">
										<form>
											<ul class="list-inline credit-quantity-box">
												<li>
													<div class="input-group spinner marbot-5">
														<input type="text" name="quantity" class="form-control quantity"
															data-increment="1" value="1" <?= value_if_test(!$item->raw_data_object()->is_quantity_unlocked, 'disabled') ?> />
														<div class="input-group-btn-vertical">
															<button class="btn btn-secondary increase" type="button" <?= 
																value_if_test(!$item->raw_data_object()->is_quantity_unlocked, 'disabled') ?>>
																<i class="fa fa-caret-up"></i></button>
															<button class="btn btn-secondary decrease" type="button" <?= 
																value_if_test(!$item->raw_data_object()->is_quantity_unlocked, 'disabled') ?>>
																<i class="fa fa-caret-down"></i></button>
														</div>
													</div>
												</li>
												<li class="price marbot-5" data-price="<?= $item->price ?>"></li>
												<li><button class="add btn btn-default">Add to Cart</button></li>
											</ul>
										</form>
									</div>
								</div>
								<?php endforeach ?>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 form-col-2">
			<div id="locked_aside">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Order Summary</h3>
					</div>
					<div class="panel-body">
						<div class="aside_cart">
							<div class="tips your-cart manage-25-cart">
								<div class="cart-data marbot-20" id="cart-data">	
									<?= $ci->load->view('shared/partials/cart') ?>		
								</div>
								<a href="manage/order" class="btn btn-primary btn-block">Checkout</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/jquery.lockfixed.js');
	$render_basic = $ci->is_development();
	$ci->add_eob($loader->render($render_basic));

?>

<?= $ci->load->view('shared/partials/order-js') ?>

<script>
	
$(function() {

	if (is_desktop()) {
		var options = { offset: { top: 100 } };
		$.lockfixed("#locked_aside", options);
	}
	
	var change_quantity = function(direction) {
		var quantity = $(this);
		var increment = quantity.data("increment");
		var quantity_val = quantity.val();
		quantity_val.replace(/[^0-9]/, "");
		quantity_val = parseInt(quantity_val);
		quantity_val += (direction * increment);
		if (quantity_val < 0) quantity_val = 0;
		quantity.val(quantity_val);
		quantity.trigger("change");
	};

	$(".credit-quantity-box .quantity").on("change keyup", function(ev) {
		var quantity = $(this);
		var parent = quantity.parents(".credit-quantity-box");
		var price = parent.find(".price");
		var button = parent.find(".add");
		var quantity_val = quantity.val();
		quantity_val.replace(/[^0-9]/, "");
		quantity_val = parseInt(quantity_val);
		if (isNaN(quantity_val)) quantity_val = 0;
		var price_val = parseFloat(price.data("price"));
		price_val = quantity_val * price_val;
		price_val = "$ " + price_val.toFixed(2);
		price.text(price_val);
	}).on("focus", function() {
		var quantity = $(this);
		var parent = quantity.parents(".credit-quantity-box");
		var button = parent.find(".add");
		button.text("Add to Cart");
		button.prop("disabled", false);
		button.removeClass("disabled");
	}).each(function() {
		var _this = $(this);
		_this.val(_this.data("increment"));
		_this.trigger("change");
	});
	
	$(".credit-quantity-box .increase").on("click", function() {
		var parent = $(this).parents(".credit-quantity-box");
		var quantity = parent.find(".quantity").get(0);
		change_quantity.call(quantity, 1);
	});
	
	$(".credit-quantity-box .decrease").on("click", function() {
		var parent = $(this).parents(".credit-quantity-box");
		var quantity = parent.find(".quantity").get(0);
		change_quantity.call(quantity, -1);
	});

	$(".credit-quantity-box .add").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		_this.prop("disabled", true);
		_this.addClass("disabled");
		var package = _this.parents(".package");
		var quantity = package.find(".quantity").val();
		var order_url = package.data("order-url");
		order_url = order_url.replace(/0$/, quantity);
		$.get(order_url, function() {
			_this.text("Added");
			window.reload_cart();
		});
	});
		
});
	
	
</script>