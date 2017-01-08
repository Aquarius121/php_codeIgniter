<div class="span12">
	<div class="content content-no-tabs">
		<div class="span12 information-panel ta-center">
			<div class="marbot-50"></div>			
			<!--<img src='assets/im/loader-line-large.gif'>-->
			<div id="in-progress">
				<h4 id="in-progress" class="marbot-10">
					Data is being pulled from MyNewsDesk.com <br /><br />
					Please wait, this may take a few minutes depending on the size of data.
				</h4>

				<div id="div_prs" class="marbot-10">
					Pulling Press Releases ... 
						<span id="status_pr" class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>

				<div id="div_news" class="marbot-10">
					Pulling News ... 
						<span id="status_news" class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>

				<div id="div_events" class="marbot-10">
					Pulling Events ... 
						<span id="status_events" class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>

				<div id="div_contacts" class="marbot-10">
					Pulling Contacts ... 
						<span id="status_contacts" class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>

				<div id="div_images" class="marbot-10">
					Pulling Images ... 
						<span id="status_images" class="strong">
							<img src='assets/im/loader-line.gif'>
						</span>
				</div>
			</div>

			<h4 class="hidden marbot-10" id="complete">
				Data pulled successfully. 
				<a href="<?= $vd->newsroom->url() ?>" target="_blank">View Newsroom</a>
			</h4>	

			<div class="marbot-100"></div>
			

		</div>
	</div>
</div>



<script>

$(function() {
	var update_status = function() {
		$.get("admin/nr_builder/mynewsdesk/status_poll/<?= $vd->mynewsdesk_company_id ?>", function(res) {
			console.log(res);
			
			if (res.finished == 1) {
				$('#status_images').html('Completed');
				$('#complete').removeClass('hidden');
				$('#in-progress').addClass('hidden');
			} 

			else {
				setTimeout(update_status, 10000);
			}

			
			if (res.prs == 1)
				$('#status_pr').html('Completed');
			

			if (res.news == 1)
				$('#status_news').html('Completed');
			

			if (res.events == 1)
				$('#status_events').html('Completed');
			

			if (res.contacts == 1)
				$('#status_contacts').html('Completed');
			

			if (res.images == 1)
				$('#status_images').html('Completed');
			

			
		});
	};
	
	setTimeout(update_status, 3000);
	
});
	
</script>
