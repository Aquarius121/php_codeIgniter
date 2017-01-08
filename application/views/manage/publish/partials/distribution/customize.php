<div id="distribution-customization">
	<fieldset class="form-section">
		<legend>
			Distribution Options
			<a data-toggle="tooltip" class="tl" href="#" 
				title="Customize your distribution.">
				<i class="fa fa-fw fa-question-circle"></i>
			</a>	
		</legend>
		<div class="distribution-options">			
			<?= $ci->load->view('manage/publish/partials/distribution/customize/state') ?>
			<?= $ci->load->view('manage/publish/partials/distribution/customize/microlists') ?>
		</div>
	</fieldset>
</div>