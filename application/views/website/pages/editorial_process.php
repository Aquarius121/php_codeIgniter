<?php $ci->title = 'Editorial Review Process | Editor team Contact Information'; ?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<header class="main-header">
					<h2>Editorial Review Process</h2>
				</header>
			</div>
		</div>
	</div>
</main>

<section class="editorial-process">
	<div class="container">
		<div class="row article">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">

				<p>
					<strong>Newswire.com follows 3 main principles while reviewing and accepting to publish content:</strong>
				
					<ol class="numbered">
						<li>
							Editorial efforts and reviewing of the press release information must 
							embrace diversity. (At any given time there is at least 3 editors reviewing 
							the content submissions)
						</li>
						<li>
							The submitted content must be high quality, factual and focused and adhere 
							to the strictest editorial policies. (Our editors are trained to follow our 
							strict guidelines. There are no exceptions to this rule)
						</li>
						<li>
							The availability and openess of public scrutiny must be present to allow 
							measures of quality improvement in the review process.
						</li>
					</ol>
				</p>
				
				<p>
					<strong>The Newswire.com editorial review process follows a Two-Tier review process.</strong>
					
				
					<ul>
						<li>Tier 1 - Press release submissions are reviewed by the Executive Editor who performs 
						the initial read and review.</li>
					
						<li>Tier 2 - If necessary the Executive Editor personally forwards the press release 
						submission one of the staff editors. They continue reviewing the varying technical 
						and/or factual matters of the submitted content. </li>
				
				</p>
				<p>
					After these steps have concluded the press release submission proceeds and if no other 
					issues arise the release continues to live publication.
				</p>
				
				<p>
					Newswire.com will work closely with authors regarding any revision or clarifications 
					regarding their press releases.
				</p>

				<h3>Editorial Team Contact Details:</h3>
				
				<strong>Executive Editor:</strong>
				<ul class="marbot-20">
					<li>Irish Abedejos - irish@newswire.com</li>
				</ul>

				<strong>Staff Editors:</strong>
				<ul class="marbot-20">
					<li>Anthony Santiago - anthony@newswire.com </li>
					<li>Erik Rohrmann - erik@newswire.com </li>
				</ul>
				
				<p>Or you may reach us at the following ways below: </p>
				<ul>
					<li><a href='#' class="help-link">Helpdesk</a></li>
					<li>Email: support@newswire.com</li>
					<li>Phone: <strong>(800)713-7278</strong></li>
				</ul>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</div>
</main>

<?= $ci->load->view('website/partials/register-footer') ?>

<script>
$(function(){
	$(".help-link").on("click", function(ev){
		ev.preventDefault();
		$(".cd-eye-catcher").trigger('click');
	});
})
</script>