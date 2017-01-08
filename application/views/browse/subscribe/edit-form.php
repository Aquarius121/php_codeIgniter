<div class="follow-content" id="follow-content">

	<div class="follow-content-info">
		<p class="big-text">Get notified about new releases.</p>
		<p>Manage notifications for <?= $vd->contact->email ?>.</p>
	</div>

	<form  id="follow-form" action="browse/subscribe/edit_save" method="post">
		<input type="hidden" name="sub" value="<?= $vd->sub_hash ?>">

		<div class="form-group frequency">
			<p class="form-title">Notification Frequency <a href="#" class="tl" 
				title="You can unsubscribe at any time"><i class="fa fa-question-circle"></i></a></p>
			<?= $ci->load->view("browse/partials/follow_notification_frequency.php") ?>

			
			<button class="btn btn-flat-blue btn-large btn-block subscribe-button update-button marbot-50"
				type="submit" name="update_subscription" value="1">Update Preferences</button>

			
		</div>

		<div class="ta-center">
			<a href="browse/subscribe/unsubscribe_all/<?= $vd->sub_hash ?>" class="status-info smaller">
				unsubscribe all updates from <?= $vd->esc($ci->newsroom->company_name) ?></a>
		</div>
	</form>
</div>

