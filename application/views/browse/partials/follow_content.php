<div class="follow-content" id="follow-content">

	<div class="follow-content-info">
		<p class="big-text">Get notified about new releases.</p>
		<p>Sign up for email updates from <?= $vd->esc($ci->newsroom->company_name) ?>.</p>
	</div>

	<form id="follow-form">

		<?php $url = current_url() ?>
		<input type="hidden" name="from_url" value="<?= $ci->newsroom->url($url) ?>">

		<div class="form-group email">
			<label for="subscribe-email" class="sr-only">Enter your email address</label>
			<input type="email" name="email" class="form-control" id="subscribe-email"
				placeholder="Enter your email address"
				value="<?= $vd->esc($vd->follow_email) ?>" />
		</div>

		<div class="form-group frequency">

			<p class="form-title">Notification Frequency <a href="#" class="tl" 
				title="You can unsubscribe at any time"><i class="fa fa-question-circle"></i></a></p>

			<?= $ci->load->view("browse/partials/follow_notification_frequency.php") ?>
			
		</div>

		<button class="btn btn-flat-blue btn-large btn-block subscribe-button"
			type="button" id="follow-submit-button">Subscribe</button>

		<script>
			
		$(function() {

			var url = "browse/subscribe/create_from_new_modal";
			var form = $("#follow-form");
			var container = $("#follow-content");
			var button = $("#follow-submit-button");

			var message_success = "<strong>Your subscription has been added</strong><br>\
				Please check your email account for a confirmation email.";
			var message_failure = "<strong>Your subscription could not be added</strong><br>\
				Make sure that the email address is valid and not subscribed.";

			button.on("click", function() {
				var data = form.serialize();
				container.addClass("has-loader");
				$.post(url, data, function(res) {
					container.removeClass("has-loader");
					container.empty();
					var alert = $.create("div");
					alert.addClass(res 
						? "alert ta-center alert-success" 
						: "alert ta-center alert-danger");
					alert.html(res
						? message_success
						: message_failure);
					container.append(alert);
				});
			});

		});

		</script>

	</form>

</div>