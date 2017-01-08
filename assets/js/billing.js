$(function() {

	var order_url_prefix = $("#order-url-prefix").val();
	var paypal_button_image = $("#paypal-button-image");
	var paypal_button_cancel = $("#paypal-button-cancel");
	var paypal_payment_method = $(".payment-method-paypal");
	var card_payment_method = $(".payment-method-card");
	var payment_method_select = $(".select-payment-method");
	var account_email_field = $("#email");
	var total_cost = $("#total-cost");
	var braintree_client_token = null;
	var braintree_loaded = false;
	
	$(window).on("load", function() {

		$.get(order_url_prefix + "/client_token", function(res) {
			braintree_client_token = res;
			if (braintree_loaded)
				braintree_setup();
		});
		
		$.ajaxSetup({ cache: true });
		$.getScript("//js.braintreegateway.com/v2/braintree.js", function() {
			braintree_loaded = true;
			if (braintree_client_token)
				braintree_setup();
		});

		$.ajaxSetup({ cache: false });

	});
	
	if (paypal_payment_method.hasClass("active")) {
		// detach the card and enable paypal
		card_payment_method.detach();
		payment_method_select.after(paypal_payment_method);
		paypal_payment_method.show();
	} else {
		// detach initially as not default
		paypal_payment_method.detach();
	}

	window.required_js.on_submit = function(ev, fini_cb) {

		var _this = this;

		$("#feedback").hide();
		$(window).scrollTop(0);
		var form = $(".required-form");
		form.addClass("has-loader");
		form.on("mousedown", function() { return false; })
		form.find("input").on("focus", function() { $(this).blur(); });
		var loader = $(".order-loader").show();
		var button = $(".submit-order-button");
		button.prop("disabled", true);
		button.addClass("disabled");

		var cc_nonce = $("#cc_nonce");
		var cc_number = $("#cc_number");
		var cc_expires_month = $("#cc_expires_month");
		var cc_expires_year = $("#cc_expires_year");
		var cc_cvc = $("#cc_cvc");

		if ($.contains(document, cc_number[0]) && cc_number.val()) {

			// create a client so that we can tokenize credit cards
			var braintree_client = new braintree.api.Client({ 
				clientToken: braintree_client_token
			});

			braintree_client.tokenizeCard({
				number: cc_number.val(),
				expirationMonth: cc_expires_month.val(),
				expirationYear: cc_expires_year.val(),
				cvv: cc_cvc.val(),
			}, function (err, nonce) {
				cc_number.prop("disabled", true);
				cc_cvc.prop("disabled", true);
				cc_nonce.val(nonce);
				fini_cb();
			});

			ev.preventDefault();
			return false;

		}

	};
	
	var braintree_setup = function() {
		
		var value_box = paypal_payment_method.find(".paypal-amount-value");
		var email_box = paypal_payment_method.find(".paypal-email");
		var email_field = paypal_payment_method.find("#paypal-email-field");
		var nonce_field = paypal_payment_method.find("#paypal-nonce-field");

		var paypal_braintree;
		var paypal_cancel = function() {

			paypal_button_image.removeClass("disabled");
			paypal_payment_method.fadeOut(600, function() {
				paypal_payment_method.detach();
				payment_method_select.after(card_payment_method);
				card_payment_method.fadeIn(600);
			});
			
			email_field.val("");
			nonce_field.val("");
			return false;

		};

		paypal_button_cancel.on("click", paypal_cancel);
		paypal_button_image.on("click", function() {

			if (!paypal_button_image.hasClass("disabled")) {
				paypal_braintree.paypal.initAuthFlow();
				return false;
			}

		});		

		// setup the braintree paypal integration
		braintree.setup(braintree_client_token, "paypal", {
			
			displayName: "Newswire.com",
			headless: true,
			singleUse: false,			
			onCancelled: paypal_cancel,

			onReady: function(integration) {
				paypal_braintree = integration;
		  	},

			onSuccess: function(nonce, email) {
				
				if (total_cost.length &&
				    value_box.length) {
					var cost_value = total_cost.text();
					cost_value = cost_value.replace(/[^[0-9\.]/, "");
					value_box.text(cost_value);
				}

				email_field.val(email);
				nonce_field.val(nonce);
				email_box.text(email);
				
				if (account_email_field.length &&
				   !account_email_field.val())
					account_email_field.val(email);

				paypal_button_image.addClass("disabled");								
				card_payment_method.fadeOut(600, function() {
					card_payment_method.detach();
					payment_method_select.after(paypal_payment_method);
					paypal_payment_method.fadeIn(600);
				});
				
			},
			
		});
		
	};
	
});
