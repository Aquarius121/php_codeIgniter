<form action="browse/subscribe/unsubscribe" method="post">
	<input type="hidden" name="sub" value="<?= $vd->sub_hash ?>">
	<div class="main-content">
	<section class="latest-news">
		<header class="ln-header marbot-20">
			<h2>Manage Subscription</h2>
		</header>
		<div id="container">
			<div class="content content-no-tabs">
				<ul>
					<li class="marbot-20">
						<div class="row-fluid">
							<div class="span12">
								<label class="checkbox-container inline">
									<input type="checkbox" value="1" name="confirm_unsubscribe" 
										class="selectable" id="confirm_unsubscribe">
									<span class="checkbox"></span>
									Yes, I no longer want to receive ANY updates at all for 
									<?= $vd->newsroom_name ?>
								</label>
							</div>		
						</div>
					</li>

					<li>
						<div class="row-fluid">
							<div class="span3">
								<button class="span10 btn bt-orange disabled" type="sumbit"
									name="update_subscription" id="update_subscription"
									value="1" disabled=true>Save</button>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>	
	</section>	
</div>

<script>
$(function(){	
	$("#confirm_unsubscribe").on("change", function(){
		var state = $(this).is(":checked");
		var button = $("#update_subscription");
		if (state)
		{
			button.removeClass("disabled");
			button.attr("disabled", false);
		}
		else
		{
			button.addClass("disabled");
			button.attr("disabled", true);
		}
			
	});
});
</script>