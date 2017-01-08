<form class="row-fluid" action="admin/companies/delete_newsroom" method="post" id="delete-newsroom-form">
	<input type="hidden" name="company_id" value="<?= $vd->nr->company_id ?>">
	<div class="span12">		
		
		<section class="form-section">

			<div class="marbot-20">
				Delete the newsroom for:  <br />
					<strong><?= $vd->esc($vd->nr->company_name) ?></strong>
			</div>

			<div class="row-fluid">
				<div class="span10 relative">
					<input type="email" name="email"
						class="span11 in-text has-placeholder"
						placeholder="Email Address" />
					<strong class="placeholder">Email Address to Notify (Optional)</strong>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span12">	
					<button type="button" name="cancel-btn" value="1" 
						class="cancel-btn span4 btn">Cancel</button>
					<button type="submit" name="delete_nr" value="1" 
						class="span5 btn btn-danger">Delete Newsroom</button>
				</div>
			</div>

		</section>

		<script>

		$(function() {

			var form = $("#delete-newsroom-form");
			form.find(".cancel-btn").on("click", function(ev) {
				form.parents(".eob-modal").modal("hide");
				ev.preventDefault();
			});

		});
		
		</script>
		
	</div>
</form>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>