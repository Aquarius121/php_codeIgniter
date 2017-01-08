<!-- added to head section instead of inline because people -->
<!-- only for order tracking from non-registered customers -->
<?php $ci->add_eoh($ci->load->view('website/partials/track-vwo')); ?>

<?= $ci->load->view('partials/track-order') ?>
<?= $ci->load->view('partials/track-register') ?>

<main class="main checkout-section form-page" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Thank You</h1>
				</header>
			</div>
		</div>
		<div class="separator"></div>
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					Your order is <strong>confirmed</strong>. A new account has been created for you. 
					<br />We've sent you an email with all the details. <br /><br />
					You will now be redirected to your control panel.
					<br /><br />
					<?php if ($vd->callback): ?>
					<a class="btn btn-success marbot-30" href="<?= $vd->esc($vd->callback) ?>" id="continue">Continue</a>
					<?php else: ?>
					<a class="btn btn-success marbot-30" href="default" id="continue">Continue to Dashboard</a>
					<?php endif ?>
					<div class="redirect-loader" style="width: 100px"><span></span></div>
				</header>
			</div>
			<div class="col-sm-1"></div>
		</div>
	</div>	
</main>

<script>

$(function() {
	
	var interval = 75;
	var percentage = 0;
	var loader_span = $(".redirect-loader span");
	var render = function() {
		var percent_css = percentage + '%';
		loader_span.css("width", percent_css)
		percentage += 1;
		if (percentage > 100)
			window.location = $("#continue").attr("href");
		else setTimeout(render, interval);
	};
	
	setTimeout(render, interval);
	
});

</script>