<form action="browse/subscribe/manage" method="post">
	<?php $url = current_url() ?>
	<?php $url = (strlen($url) > 1) ? substr($url, 1) : '' ?>	

	<input type="hidden" name="from_url" value="<?= $ci->newsroom->url($url) ?>">
	<div class="row-fluid">
		<ul>
			<li class="marbot-10">
				<input type="email" required class="span12 in-text nomarbot" name="email" 
					placeholder="Email Address" value="<?= $ci->session->get('subscribe_email') ?>" />
			</li>
			<li>
				<span class="span2"></span>
				<button type="submit" class="span8 btn btn-info nomarbot" value="1"
					 style="color: #fff !important" />
					Subscribe via Email
				</button>
				<span class="span2"></span>
			</li>
		</ul>
	</div>
</form>