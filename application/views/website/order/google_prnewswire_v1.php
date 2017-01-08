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

.radio-container-box, .checkbox-container-box {
    background: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    padding: 15px 25px 15px 15px;
}

.louder {
    color: #555;
}

.muted {
    color: #999;
}

.radio-img {
    background: url("<?= $vd->assets_base ?>im/radio-icons.png") left no-repeat;
    cursor: pointer;
    display: inline-block;
    height: 20px;
    width: 20px;
	vertical-align: middle;
}

label.radio-container {
    color: #777;
    font-size: 15px;
    height: 30px;
    line-height: 20px;
    margin-bottom: 10px;
    padding: 0;
	vertical-align: middle;
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

});

</script>

<div class="dnone">
	<div id="secure-message">
		<div style="vertical-align:middle; margin-bottom:20px;">
		 	<img src="//www.instantssl.com/ssl-certificate-images/support/comodo_secure_100x85_transp.png"
		 		align="middle"><span style="position:relative; top:10px; margin-left:30px;">
		 		<strong>Risk-Free 30-day money back guarantee with all orders!</strong></span>
		 </div>
	</div>
</div>

<div class="container" style="margin-bottom:60px;">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="radio-container-box">
			<label class="radio-container louder"><span class="radio-img"></span> Premium with&nbsp;<a href="http://www.newswire.com/features/distribution/prnewswire" target="_blank">PR Newswire</a>&nbsp;<span style="text-decoration:line-through;">($299.00)</span> ($254.15)</label>
			<p class="muted">A powerful combination of the Newswire distribution network and PR Newswire's online&nbsp;<a href="<?= $vd->assets_base ?>other/prnewswire_distribution.pdf" target="_blank">syndication network</a>&nbsp;combined to  more than 4,500+ Web sites. Your news will be seen on the internet's largest news sites (such as Yahoo!, MSN and AOL), niche and localized web sites and news engines including Yahoo! News and Google News.</p>
			<p class="muted">&nbsp;</p>
			<p class="louder"><span style="margin-top:10px;"><strong>Risk-Free 30-day money back guarantee with all orders!</strong></span></p>
			</div>
		</div>
	</div>
</div>
