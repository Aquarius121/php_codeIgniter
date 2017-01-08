<?= $ci->load->view('website/partials/track-vwo') ?>

<style>
	
.pf-container {
	background: #eee;
	padding: 20px;
	margin-top: 50px;
}

.pf-container h1.order-form-header {
	margin-top: 0px;
}

</style>

<script>
	
$(function() {

	var last_non_payment_field = $(".select-payment-method").prev();
	var payment_fields = last_non_payment_field.nextAll();
	payment_fields.detach();
	var pf_container = $.create("div");
	pf_container.addClass("pf-container");
	last_non_payment_field.after(pf_container);
	var secure_message = $("#secure-message");
	secure_message.detach();
	pf_container.append(secure_message);
	pf_container.append(payment_fields);

	$(".main-header h1").text('Place Your Order & Reach Millions Through 4500+ Premium News Sites');
	$("body > header").replaceWith($("#new-header"));
	$(".secure-seals").remove();
	$(".already-have-account").remove();
	$(".order-form-header.account-details")
		.text("Create a username & password to access your account.")
		.after($("#already-have-account-text"));
	$("label[for=email]").text('Email Address (This will be your username)');
	$("#or-paypal").text("or click to use");
	$(".submit-order-button").text("ORDER & PREPARE PRESS RELEASE")
		.after($("#guarantee"));
	$("#cc_number").attr("style", 'background: rgb(255, 255, 255) url("<?= $vd->assets_base ?>im/ccards.png") no-repeat scroll 97% 50%;');

});

</script>

<div class="dnone">

	<div id="secure-message">
		<div style="vertical-align:middle; margin-bottom:20px;">
		 	<img src="//www.instantssl.com/ssl-certificate-images/support/comodo_secure_100x85_transp.png"
		 		align="middle"><span style="position:relative; top:10px; margin-left:30px;">
		 		<strong>All data is secured with encryption!</strong></span>
		 </div>
	</div>

	<link rel="stylesheet" href="//<?= $ci->conf('website_host') ?>/LPG/landing-page-common/css/icono.min.css" />

	<style>

	.feature-list {
		margin-top:50px; height: 90px;
	}

	</style>

	<header class="header" id="new-header">
		<nav class="navbar">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="">
						<img src="assets/im/website/logo-inewswire.svg" alt="Newswire">
					</a>
				</div>
				<div class="navbar-collapse collapse">
					<div class="header-phone header-phone-inside our-phone-number">(800) 713-7278</div>
					<menu class="nav navbar-nav navbar-right nav-activate" data-nav-selector=">li">
						<li data-on="^login">
							<a href="manage/order" class="signup-btn">
								Already have an account? - Login
							</a>
						</li>
					</menu>
				</div>
				<div class="header-phone header-phone-outside our-phone-number">(800) 713-7278</div>
			</div>
		</nav>
	</header>

	<div class="row" style="padding-top: 40px;" id="guarantee"> <div class="col-sm-4"> <img src="<?= $vd->assets_base ?>im/gbadge.png" alt="" title="" width="150" height="151" border=""> </div> <div class="col-sm-8"><p style="font-size:25px;">Money Back Guarantee</p> <p style="color: #656565; font-size: 14px; line-height: 26px;">We guarantee your press release will appear on at least 175 media sites or we will give you a 100% refund. <i>*Most customers get much better results.</i></p> <p style="color: #656565; font-size: 14px; line-height: 26px;">In comparison, press release services that cost $15,000+ don't guarantee placement.</p> </div></div>

</div>

<div class="row prwire">

	<div align="left" class="col-sm-6" style="font-size:15px; margin-top:50px; height: 90px;	">
	   <div style="width:58px; height:100%; float:left;"><i class="icono-imacBold" style="top:10px;"></i></div>
	   <span style="font-size:18px; font-weight:700;">Premium Publishers</span>
	  <div style="margin-top:10px; line-height:20px;">
	  Get featured on Yahoo News, AOL, ABC, FOX, NBC, CBS, CNN Money, Bloomberg + many more quality publications.
	  </div>     
	</div>

	<div align="left" class="col-sm-6" style="font-size:15px; margin-top:50px; height: 90px;	">
	   <div style="width:58px; height:100%; float:left;"><i class="icono-sitemap" style="top:10px;"></i></div>
	   <span style="font-size:18px; font-weight:700;">Multi-Network Distribution</span>
	   <div style="margin-top:10px; line-height:20px;">
	  Not only are you getting Newswire.com's highly trusted distribution network, but you are also sending to PR Newswire™. The largest, oldest and most trusted distribution network in the world.
	  </div>     
	</div>

	 <div align="left" class="col-sm-6 " style="font-size:15px; margin-top:50px; height: 90px;	">
	   <div style="width:58px; height:100%; float:left;"><i class="icono-checkCircle" style="top:10px;"></i></div>
	   <span style="font-size:18px; font-weight:700;">Full Guarantee</span>
	   <div style="margin-top:10px; line-height:20px;">
	  No Risk 30 Day Money Back Guarantee! We guarantee your press release will appear on at least 175 media sites or we will give you a 100% refund. 
	  </div>      
	</div>

	<div align="left" class="col-sm-6 " style="font-size:15px; margin-top:50px; height: 90px;	">
	   <div style="width:58px; height:100%; float:left;"><i class="icono-clock" style="top:10px;"></i></div>
	   <span style="font-size:18px; font-weight:700;">Fast 24 hour approval</span>
	   <div style="margin-top:10px; line-height:20px;">
	  Our editorial team quickly reviews and notifies you of any corrections that need to be made. Press releases are reviewed in 12 hours or less. Fast turnaround times are our priority.
	  </div>     
	</div>

	<div class="marbot-20" id="already-have-account-text">
		<h3 style="font-size:18px;">Already have an account?</h3>
		<p>You should <a href="manage/order">login to your account</a> to 
			continue the order process.</p>
	</div>

</div>