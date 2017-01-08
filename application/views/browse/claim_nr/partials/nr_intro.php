<?php ob_start(); ?>

<section class="form-section">

	<h3 class="marbot-20 advert-row-300-250">Welcome to your Company Newsroom</h3>

	<div class="row-fluid">
		<div class="span5">
			<img src="assets/im/claim_newsroom_pre_small.png" style="margin-left:18px;">
		</div>
		<div class="span6 html-content">
			<h4 class="marbot-20">Features and Benefits:</h4>
			<div class="">
				<ul class="rb-additional-resources">
					<li class="marbot-15">Increases brand awareness
						with all company news in one place</li>
					<li class="marbot-15">Enables media and public to
						stay up to date on company updates</li>
					<li class="marbot-15">Powers automated curation of
						your social, blog and news content</li>

				</ul>
			</div>
		</div>
		<div class="span8 offset2 relative pad-2v">
			<button class="span12 bt-orange advert-row-300-250" id="continue_button"
				type="button">Preview My Company Newsroom</button>
		</div>
	</div>
	
</section>

<?php 

$content = ob_get_contents();
ob_clean();

?>

<button type="button" class="close" data-dismiss="modal"
	aria-hidden="true">
	<i class="fa fa-remove"></i>
</button>
<h3 id="modalLabel"><?= $vd->esc($ci->newsroom->company_name) ?> Company Newsroom Preview</h3>

<?php 

$header = ob_get_contents();
ob_end_clean();

$modal = new Modal();
$modal->set_id('nr_intro');
$modal->set_content($content);
$modal->set_header($header);
echo $modal->render(600, 500);

?>

<script>

$(function() {
	$("#nr_intro").modal('toggle');
	$("#continue_button").on("click", function(){
		$("#nr_intro").modal('toggle');
	});
});

</script>