<?php /* -------------- facebook -------------- */ ?>

<script>

(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=586234651427775&version=v2.0";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>

<div class="fb-like button button-fb" data-href="https://www.facebook.com/inewswire" data-layout="button_count" 
	data-action="like" data-show-faces="false" data-share="false"></div>
	
<?php /* -------------- twitter -------------- */ ?>

<a href="https://twitter.com/inewswire" class="twitter-follow-button button button-tw"
	data-show-count="true" 
	data-show-screen-name="false"
	data-lang="en">Follow</a>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;
js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<?php /* -------------- google+ -------------- */ ?>

<div class="button button-gp">
	<div class="g-plusone" data-size="medium" data-href="<?= $ci->website_url() ?>"></div>
</div>

<script>

	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/platform.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
  
</script>

