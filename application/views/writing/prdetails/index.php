<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">					
					<h1>PR Writing Details</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
					</div>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<form class="tab-content required-form" method="post" action="writing/prdetails/verify_code" id="prdertails-form">				
				
				<div class="row-fluid">
					<div class="span8 information-panel">
						
						<h2>PR Writing Code</h2>						
						<div class="muted">Please enter your PR code to access press release details form.</div>
												
						<section class="form-section basic-information">
							<ul>
								<li>
									<input class="in-text span12 required" type="text" name="writing_order_code" 
										id="writing_order_code" placeholder="PR Writing Code"
										value="<?= $vd->esc($vd->direct_link_code) ?>"
										data-required-name="PR Writing Code" />
								</li>
								<li class="marbot-30">
									<button type="submit" class="span3 bt-silver"
										value="1">Continue</button>
								</li>
							</ul>
						</section>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>