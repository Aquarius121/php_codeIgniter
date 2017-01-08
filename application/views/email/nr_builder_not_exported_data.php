Newsroom builder has "Not Yet Exported" data for more than 48 working hours for the following sources: <br>

<?php foreach($vd->results as $result): ?>
	<br><strong><?= Model_Company::full_source($result->source) ?></strong> <br>

	<?php if ($result->ab_not_exported_count): ?>
		&nbsp; &nbsp; <a href="<?= $ci->website_url("admin/nr_builder/{$result->source}/auto_built_nrs_not_exported") 
			?>" target="_blank">Auto Built NRs</a> <br>
	<?php endif ?>

	<?php if ($result->claim_count): ?>
		&nbsp; &nbsp; <a href="<?= $ci->website_url("admin/nr_builder/{$result->source}/claim_submissions") 
			?>" target="_blank">Claim Submissions</a> <br>
	<?php endif ?>

	<?php if ($result->verified_count): ?>
		&nbsp; &nbsp; <a href="<?= $ci->website_url("admin/nr_builder/{$result->source}/verified_submissions_not_exported") 
			?>" target="_blank">Verified Submissions</a> <br>
	<?php endif ?>
		
	<?php endforeach ?>

<br><br>
Please take corrective action.