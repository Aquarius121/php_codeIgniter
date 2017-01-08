<div class="span12">
	<div class="content content-no-tabs">
		<div class="span12 information-panel ta-center">
			<div class="marbot-50"></div>			
			<div id="in-progress">
				<h4 id="in-progress" class="marbot-30">
					The newsrooms are being built. <br /><br />
					Please wait, this may take a few minutes.
				</h4>

				<div class="marbot-10">
					<span id="span_counter">0</span>
					of <?= $vd->total_nrs ?> completed .. 
						<span class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>
			</div>

			<h4 class="hidden marbot-10" id="complete">
				Newsrooms built successfully. 
				<a href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_not_exported">
					Auto Built Newsrooms
				</a>
			</h4>

			<div class="marbot-100"></div>
		</div>
	</div>
</div>



<script>

$(function() {

	var update_status = function() {
		$.get("admin/nr_builder/<?= $vd->nr_source ?>/bulk_build_status_poll", function(res) {
			console.log(res);
			
			if (res.is_completed == 1) {
				$('#complete').removeClass('hidden');
				$('#in-progress').addClass('hidden');
			} 

			else {
				setTimeout(update_status, 5000);
			}

			
			if (res.counter >= 0)
				$('#span_counter').html(res.counter);
		
		});
	};
	
	setTimeout(update_status, 3000);
	
});

	
</script>
