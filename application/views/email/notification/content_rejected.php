<?php if (!empty($feedback['comments'])
      || (!empty($feedback['canned']) && count($feedback['canned']))): ?>
	
Unfortunately your <strong style="font-weight:500;"><?= strtolower(Model_Content::full_type($content->type)) ?></strong> titled <strong style="font-weight:500;"><?= $vd->esc($content->title) ?> </strong>
was reviewed and found to be unsuitable for publication for the following reason(s) below:

<br/><br/>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr style="background-color:#616871; height:40px;">
		<td style="width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>
		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; line-height:26px; text-align:left;">
			<?= $vd->esc($content->title) ?>
		</td>
		<td style=" width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>
	</tr>
	
	<?php if (!empty($feedback['comments'])): ?>
	<tr style="background-color:#e0e3e7; height:40px;">
		<td style="background-color:#e0e3e7; width:30px; border-top: 2px solid #fff; padding-top:5px; margin-top:0"><img
		 	src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" style="border:0; 
		 	display:block;" /></td>
		<td style="color:#656565; border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; line-height:26px; text-align:left; padding-top: 5px; padding-bottom: 10px;">
			<?= $feedback['comments'] ?>
		</td>
		<td style="width:30px; border-top: 2px solid #fff; padding-top: 5px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>
	</tr>
	
	<?php endif ?>
	<?php if (!empty($feedback['canned'])): ?>
		<?php foreach ((array) $feedback['canned'] as $i => $canned): ?>
			<tr style="background-color:<?= value_if_test($i%2==0, '#e9edf1', '#e0e3e7') ?>; height:40px;">
				<td style="width:30px; border-top: 2px solid #fff;  padding-top: 5px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" style="border:0; display:block;" />
				</td>
				<td style="color:#656565; border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; line-height:26px; padding-bottom:10px; padding-top:5px; text-align:left;">
					<?php $canned = Model_Canned::find($canned); ?>
					<strong style="font-weight:500;"><?= $vd->esc($canned->title) ?></strong><br/>
					<?= $canned->content ?>
				</td>
				<td style="width:30px; border-top: 2px solid #fff; padding-top: 5px;">
					<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" 
					style="border:0; display:block;" /></td>
			</tr>
		<?php endforeach ?>
	<?php endif ?>
</table>

<?php else: ?>

	Unfortunately your <strong style="font-weight:500;"><?= strtolower(Model_Content::full_type($content->type)) ?></strong> titled <strong style="font-weight:500;"><?= $vd->esc($content->title) ?></strong> was reviewed and found to be unsuitable for publication.
	<br/><br/>
	
	We apologize for not being able to give you the exact reason of the rejection. 
	This is due to the high volume of free releases we currently receive. 
	However, we have listed the possible reasons why your release has been rejected.
	<br/><br/>

	<ul style="margin-top:0;padding-top:0;margin-bottom:0;padding-bottom:0">
		<li style="padding:0px 0px 16px;">
			Your release seemed like a direct advertisement for your company products and/or services. 
			We only publish newsworthy press releases. 
			<div style="padding:15px 0 0">
				<i style="color:#666666">Submissions of ads for companies is the reason the majority of releases get rejected.</i>
			</div>
		</li>
		<li style="padding:0px 0px 16px;">
			Your release was a standard information article. Your release can't simply be a "How To" article or "Tips" article. 
			Although your article may contain quality information, these are not considered newsworthy and don't qualify as a publishable press release.
		</li>
		<li style="padding:0px 0px 16px;">
			Your press release title was not in standard format. The first letter of each word should be capitalized.
			<div style="padding:15px 0 0">
				<i style="color:#666666"><b>WRONG:</b> this is my title with incorrect formatting<br/>
				<b>WRONG:</b> THIS IS MY TITLE WITH INCORRECT FORMATTING<br/>
				<b>CORRECT:</b> This Is My Title With Correct Formatting</i>
			</div>
		</li>
		<li style="padding:0px 0px 16px;">
			Your release was found obscene or adult related. This includes sites that promote sexual and adult products and/or services online or offline.
		</li>
		<li style="padding:0px 0px 16px;">
			Your release was related to a controversial Biz Op program including but not limited to CASH GIFTING, MLM, PAYDAY LOANS, GET RICH QUICK, etc.
		</li>
	</ul>
	
	Thanks for trying Newswire. We welcome you to submit more releases, 
	however, please note our policy as listed above. <br/>

<?php endif ?>

<br /><br />

<h6 style="color:#8c9095; font-size:12px; font-weight:200; margin:0; padding:0; text-align:center;">This is an automated email. Please visit our website to contact us.</h6>

