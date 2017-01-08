<div style="width: 600px; margin: 20px auto;">
	
	<div class="row-fluid">
		<div class="span5">
			<img src="<?= $vd->assets_base ?>im/logo.png" alt="Newswire" style="margin:10px 0 35px">
		</div>
	</div>

	<div class="row-fluid">
		<form action="" method="post" class="unsubscribe-form span12">
			<h4><span style="color:#999999">Email Preferences:</span>
				<?= $vd->esc($vd->contact->email) ?></h4>

			<br>
			<p>Please select one of the following options to update your subscription:</p>
			
			<hr>
			<input type="radio" name="unsubscribe" id="unsub_company" value="company" required>
			<label for="unsub_company">Unsubscribe from media outreach campaigns by 
				<strong><?= $vd->esc($vd->newsroom->company_name) ?></strong>.
			</label>
			<br>

			<input type="radio" name="unsubscribe" id="unsub_all" value="all" required>
			<label for="unsub_all">Unsubscribe from <strong>all</strong> media outreach campaigns.</label>
			<hr>

			<input type="submit" class="btn btn-danger pull-left span2" name="confirm" value="Update" />
		</form>
	</div>

</div>