<?= $ci->load->view('website/partials/track-vwo') ?>

<style>
	
.order-offer .round-ico {
	background:none repeat scroll 0 0 #f1f1f1;
	border:0 none;
	border-radius:50%;
	color:#1357a8;
	font-size:24px;
	height:50px;
	line-height:50px;
	margin-right:20px;
	padding:0;
	text-align:center;
	width:50px;
	float: left;
	margin-left:15px;
}

.order-offer {
	margin-top: 80px;
	margin-bottom: -30px;
}

.order-offer h3 {
	font-size: 18px;
	font-weight: bold;
	line-height: 50px;
}

.order-offer .btn-add-writing {
	padding: 9px 18px;
}

.order-offer .btn-add-writing:hover {
	background: #f1f1f1;
}

.order-offer .add-writing.has-loader button {
	display: none;
}

.order-offer .add-writing.has-loader {
	background: url('<?= $vd->assets_base ?>im/loader-circle-medium.gif')
		center center no-repeat;
	height: 50px;
}

</style>

<div class="row order-offer" id="order-offer-writing">
	<div class="col-sm-10 col-sm-offset-1">
		<div class="row">
			<div class="col-sm-8">
				<h3 class="order-offer-h3">
					<div class="round-ico">
						<i class="fa fa-pencil"></i>
					</div>
					Need a professional press release written? Leave it to us.
				</h3>
			</div>
			<div class="col-sm-4 text-right add-writing" id="ow-add-button-container">
				<button class="btn btn-lg btn-default btn-add-writing" type="button" id="ow-add-button">
					<i class="fa fa-caret-right"></i> 
					Click to add PR writing for $199
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	
$(function() {

	var button_container = $("#ow-add-button-container");
	var offer_container = $("#order-offer-writing");
	var add_button = $("#ow-add-button");

	add_button.on("click", function() {
		button_container.addClass("has-loader");
		$.post("order/offer_writing/add", function() {
			button_container.addClass("hidden");
			offer_container.slideUp();
			window.reload_cart();
		});
	});

});

</script>