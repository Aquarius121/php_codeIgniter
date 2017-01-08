<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<main class="main planner-header">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Newswire Press Release Planner</h1>
					<p><strong>Step 7 of 7</strong></p>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<section class="container planner">
	<div>
		<div class="row">
			<div class="col-md-12">
				<h2>Please provide your contact information</h2>
				<p class="sub-headline">A Newswire team member will contact you within one business day to discuss your requirements.</p>
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<?php if ($vd->rdata->company_or_individual == "Company"): ?>
						<input type="text" name="company_name" class="required form-control" placeholder="Company Name">
						<?php endif ?>
						<input type="text" name="contact_name" class="required form-control" placeholder="Contact Name">
						<input type="email" name="email" class="required form-control" placeholder="Email">
						<input type="text" name="phone" class="form-control" placeholder="Phone">
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<button class="btn btn-default btn-lg" type="submit"
					 name="next" value="six"><i class="fa fa-angle-left"></i> Previous</button>
				<button class="btn btn-success btn-lg" type="submit"
					 name="next" value="finish">Continue <i class="fa fa-angle-right"></i></button>
			</div>
		</div>
	</div>
</section>