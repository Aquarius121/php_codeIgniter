<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>User Details</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post">
	<div class="span12">		
		<div class="content content-no-tabs">
			
			<div class="span8 information-panel">
				
				<section class="form-section user-details">
					<h2 class="marbot-5">Basic Information</h2>
					<div class="row-fluid">
						<div class="span6 relative">							
							<input type="text" required name="first_name"
								class="span12 in-text has-placeholder"
								value="<?= @$vd->writer->first_name ?>"
								placeholder="First Name" />
							<strong class="placeholder">First Name</strong>
						</div>
						<div class="span6 relative">							
							<input type="text" required name="last_name" 
								class="span12 in-text has-placeholder"
								value="<?= @$vd->writer->last_name ?>"
								placeholder="Last Name" />
							<strong class="placeholder">Last Name</strong>
						</div>
					</div>
					<div class="relative">
						<input type="email" name="email" required
							class="span12 in-text has-placeholder" 
							value="<?= @$vd->writer->email ?>"
							placeholder="Email Address" />
						<strong class="placeholder">Email Address</strong>
					</div>
					<div class="relative">
						<textarea name="notes"
							class="span12 in-text has-placeholder user-notes" 
							placeholder="Additional Notes" /><?= 
								@$vd->writer->notes ?></textarea>
						<strong class="placeholder">Additional Notes</strong>
					</div>	
				</section>	
				
			</div>
			
			<aside class="span4 aside aside-fluid">
				<div id="locked_aside">
					
					<div class="aside-properties padding-top marbot-20">
						<section class="ap-block marbot-5">
							<select class="show-menu-arrow span12 selectpicker" name="is_enabled">
								<option <?= value_if_test(!@$vd->writer->id || @$vd->writer->is_enabled, 'selected')
									?> value="1">Account Enabled</option>
								<option <?= value_if_test(@$vd->writer->id && !@$vd->writer->is_enabled, 'selected')
									?> value="0">Account Disabled</option>								
							</select> 
						</section>
						
						<section class="ap-block row-fluid marbot-10">
							<div class="row-fluid marbot-5">
								<div class="span12">
									<a class="span12 ta-center btn" id="reset-password"
										<?= value_if_test(!@$vd->writer->id, 'disabled') ?>
										target="_blank">Reset Password</a>
									<script>
									
									$(function() {
										
										var message = 'This action will reset the new password.';
										$("#reset-password").on("click", function() {
											if ($(this).is(":disabled")) return;
											bootbox.confirm(message, function(confirm) {
												if (!confirm) return;
												var url = "admin/writing/writers/view/reset/<?= @$vd->writer->id ?>";
												$.post(url, { confirm: true }, function(res) {
													var e = $.create("input").addClass("password-text");
													e.val(res.password);
													bootbox.alert({ message: e.get(0) });
													e.focus().select();
												});
											});
										});
										
									});
									
									</script>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span8">
									<button type="submit" name="save" value="1" 
										class="span12 bt-orange pull-right">Save</button>
								</div>
								<div class="span4">
									&nbsp;
								</div>
							</div>
							
						</section>
					</div>
					
				</div>
			</aside>
			
			<script>
			
			$(function() {

				var options = { offset: { top: 20 } };
				$.lockfixed("#locked_aside", options);
				
			});
			
			</script>
					
		</div>
	</div>
</form>

<?php 

	$render_basic = $ci->is_development();

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	echo $loader->render($render_basic);

?>