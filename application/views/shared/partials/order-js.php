<script>
	
$(function() {

	var apply_coupon_url = "<?= @$vd->order_url_prefix ?>/apply_coupon";
	var remove_item_url = "<?= @$vd->order_url_prefix ?>/remove_token";
	var change_quantity_url = "<?= @$vd->order_url_prefix ?>/change_quantity";
	var full_reload_url = "<?= @$vd->order_url_prefix ?>/reload";

	var update_active = false;
	var update_queue = [];
	var cart_has_coupon = false;

	var cart_data = $("#cart-data");
	var cou_discount = cart_data.find("#coupon-code-block-discount");
	var cou_input = cart_data.find("#coupon-code-block-input");
	var cou_link = cart_data.find("#coupon-code-block-link");
	var cou_id = cart_data.find("#cart-coupon-id");
	var total_cost = cart_data.find("#total-cost");	
	var checkout_total_cost = $(".checkout-total-cost");	
	var cancel_at_any_time = $("#cancel-at-any-time");
	var cart_items_block = $("#cart-items-block");
	
	var apply_coupon_link = cou_link.find("a");
	var apply_coupon_input = cou_input.find("input");
	var apply_coupon_discount = cou_discount.find("span.discount");
	var apply_coupon_percentage = cou_discount.find("span.discount-percent span");
	var apply_coupon_remove = cou_discount.find("a");	

	window.reload_cart = function() {

		update_cart(full_reload_url, null);

	};

	var update_cart = function(url, data, callback) {

		if (update_active) {
			return update_queue.push({
				callback: callback,
				data: data, 
				url: url,
			});
		}

		if (callback === undefined) callback = render_cart;
		$.post(url, data, callback);
		cart_data.addClass("update");
		update_active = true;

	};
	
	var render_cart = function(res) {

		update_active = false;
		has_renewal_distance = false;
		cart_data.removeClass("update");
		total_cost.text(res.total);
		checkout_total_cost.text(res.total);
		cart_data.find(".cart-item-discount").slideUp();
		cou_discount.slideUp();

		if (res._discount || res.coupon || cart_has_coupon) {
			apply_coupon_discount.text(res.discount);
			apply_coupon_percentage.text(Math.round(res.discount_percent));
			cou_discount.slideDown();
		}

		if (res.render !== undefined) {
		
			for (var token in res.items) {
				var item_update = res.items[token];
				if (!item_update) continue;
				if (item_update._renewal_distance)
					has_renewal_distance = true;
			}

			cart_items_block.slideUp(function() {
				cart_items_block.html(res.render);
				cart_items_block.slideDown();
			});

		} else {
		
			for (var token in res.items) {
				
				var item_update = res.items[token];
				if (!item_update) continue;
				
				var line_item = cart_data.find(".cart-item").filter(function() { 
					return $(this).data("token") == token;
				});

				// update pricing
				line_item.children(".cart-item-price")
					.text(item_update.price_total);
				line_item.find(".item-base .cart-item-price")
					.text(item_update.base_price_total);

				// update renewal period information
				line_item.children(".cart-item-renewal")
					.text(new String(item_update.renewal_distance));
				if (item_update._renewal_distance)
					has_renewal_distance = true;

				// update discount information
				if (item_update._discount) {

					var line_item_discount_row = line_item.find(".cart-item-discount");
					line_item_discount_row.find(".discount").text(item_update.discount);
					line_item_discount_row.find(".discount-type").text((function() {
						// item has renewal and but coupon is one-time only
						if (res.is_one_time_discount && item_update._renewal_distance) 
							return "One-Time";
						// item has renewal
						if (item_update._renewal_distance)
							return "Recurring";
						// single purchase item
						return "Line";
					})());

					// dnone classes breaks the slide initially
					if (line_item_discount_row.hasClass("dnone")) 
						line_item_discount_row.removeClass("dnone");
					line_item_discount_row.slideDown();
					line_item_discount_row.removeAttr("style");
				}

				// update the new hash (if changed)
				line_item.children(".hash").val(item_update.hash);

				// update attached items
				for (var child_token in item_update.attached) {

					var child_item_update = item_update.attached[child_token];
					var child_line_item = line_item.find(".cart-item-attached").filter(function() { 
						return $(this).data("token") == child_token;
					});

					// update child pricing
					child_line_item.children(".cart-item-price")
						.text(child_item_update.price_total);

				}
				
			}

		}

		cancel_at_any_time.toggleClass("show",
			has_renewal_distance);

		if (update_queue.length) {

			update_active = false;
			var job = update_queue.shift();
			update_cart(job.url, job.data, job.callback);
			return;

		}
		
	};

	var change_quality_limited = function() {

		update_cart(change_quantity_url, {
			quantity: change_quality_limited.quantity,
			token: change_quality_limited.token
		});

	};

	var change_quantity = function(token, quantity) {
		
		if (change_quality_limited.timer)
			clearTimeout(change_quality_limited.timer);

		cart_data.addClass("update");
		change_quality_limited.token = token;
		change_quality_limited.quantity = quantity;
		var timer = setTimeout(change_quality_limited, 500);
		change_quality_limited.timer = timer;

	};

	var cart_item_qui_update = function(_this, difference) {

		var cart_item = _this.parents(".cart-item");
		var cart_item_quantity = cart_item.find(".cart-item-quantity");
		var quantity = parseInt(cart_item_quantity.text());
		quantity = quantity + difference;
		if (quantity < 0) quantity = 0;
		cart_item_quantity.text(quantity);
		var token = cart_item.data("token");
		change_quantity(token, quantity);

	};

	$(document).on("click", ".cart-item-qui .minus", function() {

		cart_item_qui_update($(this), -1);

	});

	$(document).on("click", ".cart-item-qui .plus", function() {

		cart_item_qui_update($(this), +1);

	});
		
	apply_coupon_link.on("click", function() {
		
		cou_link.hide();
		cou_input.show();
		apply_coupon_input.focus();

		return false;
		
	});
	
	apply_coupon_input.on("change", function() {
		
		if (update_active) return;

		var _this = $(this);
		var value = _this.val();
		if (!value) return;

		cou_id.remove();
		cart_has_coupon = true;
		cou_input.slideUp();
		apply_coupon_percentage.text("");
		update_cart(apply_coupon_url, 
			{ code: value });
		
	});
	
	apply_coupon_input.on("keyup", function(ev) {
		
		if (ev.which != 13) return;
		$(this).trigger("blur");
		return false;
		
	});
	
	$(document).on("keypress", function(ev) {
		
		if (ev.which == 13)
			return false;
		
	});
	
	apply_coupon_remove.on("click", function() {
		
		if (update_active) return;
		cart_has_coupon = false;
		apply_coupon_input.val("");
		update_cart(apply_coupon_url);
		cou_link.slideDown();
		cou_id.remove();
		return false;
		
	});
	
	if (apply_coupon_input.val()) {		
		cou_discount.show();
		cou_link.hide();		
	}
	
	window.order_apply_coupon = function(value) {

		if (update_active) return;
		cou_link.slideUp();
		cou_input.slideUp();
		apply_coupon_input.val(value);
		update_cart(apply_coupon_url, 
			{ code: value });
		
	};
	
	if (<?= json_encode((bool) $vd->cart->is_clear()) ?>) {

		$(".order-form input").prop("disabled", true);
		$(".order-form select").prop("disabled", true);
		
	}
	
	cart_data.on("click", ".remove-item", function() {

		if (update_active) return;
		
		var _this = $(this);
		var cart_item = _this.parents(".cart-item");
		var token = cart_item.data("token");
		cart_item.slideUp(function() {
			$(this).remove();
		});
		
		update_cart(remove_item_url, 
			{ token: token });
				
		return false;
		
	});
	
});
	
</script>