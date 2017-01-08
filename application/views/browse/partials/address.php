<?php if (@$vd->nr_profile->address_street ||
			 @$vd->nr_profile->address_city ||
			 @$vd->nr_profile->address_state ||
			 @$vd->nr_profile->address_zip ||
			 @$vd->nr_profile->address_phone): ?>	
			 
<?php 

$state_converstions = array(
	'ALABAMA' => 'AL', 'ALASKA' => 'AK', 'ARIZONA' => 'AZ', 'ARKANSAS' => 'AR', 'CALIFORNIA' => 'CA', 'COLORADO' => 'CO',
	'CONNECTICUT' => 'CT', 'DELAWARE' => 'DE', 'FLORIDA' => 'FL', 'GEORGIA' => 'GA', 'HAWAII' => 'HI', 'IDAHO' => 'ID',
	'ILLINOIS' => 'IL', 'INDIANA' => 'IN', 'IOWA' => 'IA', 'KANSAS' => 'KS', 'KENTUCKY' => 'KY', 'LOUISIANA' => 'LA',
	'MAINE' => 'ME', 'MARYLAND' => 'MD', 'MASSACHUSETTS' => 'MA', 'MICHIGAN' => 'MI', 'MINNESOTA' => 'MN',
	'MISSISSIPPI' => 'MS', 'MISSOURI' => 'MO', 'MONTANA' => 'MT', 'NEBRASKA' => 'NE', 'NEVADA' => 'NV',
	'NEW HAMPSHIRE' => 'NH', 'NEW JERSEY' => 'NJ', 'NEW MEXICO' => 'NM', 'NEW YORK' => 'NY', 'NORTH CAROLINA' => 'NC',
	'NORTH DAKOTA' => 'ND', 'OHIO' => 'OH', 'OKLAHOMA' => 'OK', 'OREGON' => 'OR', 'PENNSYLVANIA' => 'PA',
	'RHODE ISLAND' => 'RI', 'SOUTH CAROLINA' => 'SC', 'SOUTH DAKOTA' => 'SD', 'TENNESSEE' => 'TN', 'TEXAS' => 'TX',
	'UTAH' => 'UT', 'VERMONT' => 'VT', 'VIRGINIA' => 'VA', 'WASHINGTON' => 'WA', 'WEST VIRGINIA' => 'WV',
	'WISCONSIN' => 'WI', 'WYOMING' => 'WY', 'AMERICAN SAMOA' => 'AS', 'DISTRICT OF COLUMBIA' => 'DC',
	'FEDERATED STATES OF MICRONESIA' => 'FM', 'GUAM' => 'GU', 'MARSHALL ISLANDS' => 'MH',
	'NORTHERN MARIANA ISLANDS' => 'MP', 'PALAU' => 'PW', 'PUERTO RICO' => 'PR', 'VIRGIN ISLANDS' => 'VI'
); 

if (!empty($vd->nr_profile->address_state))
{
	$state = trim(strtoupper(@$vd->nr_profile->address_state));
	$state = preg_replace('#\s+STATE$#s', '', $state);	
	if (isset($state_converstions[$state]))
		$vd->nr_profile->address_state_short
			= $state_converstions[$state];
}

?>

<section class="al-block al-adr accordian">
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		Address
	</h3>
	<address class="accordian-content adr">
		<span class="adr-org"><?= $vd->esc($ci->newsroom->company_name) ?></span>
		<span class="street-address">
			<?= $vd->esc(@$vd->nr_profile->address_street) ?>
			<?php if (@$vd->nr_profile->address_apt_suite): ?>
			<br /><?= $vd->esc(@$vd->nr_profile->address_apt_suite) ?>
			<?php endif ?>
		</span>
		<span class="postal-region">
			<!-- the city combined with short state fits on 1 line -->
			<?php if (@$vd->nr_profile->address_state_short
			   && strlen(@$vd->nr_profile->address_state_short) + 
				   strlen(@$vd->nr_profile->address_city) <= 35): ?> 
				<?php if ($vd->nr_profile->address_city): ?>
				<?= $vd->esc(@$vd->nr_profile->address_city) ?>, 
				<?php endif ?>
				<?= $vd->esc(@$vd->nr_profile->address_state_short) ?>
				<?php if (@$vd->nr_profile->address_state_short ||
							 @$vd->nr_profile->address_city): ?>
					<?php if (strlen(@$vd->nr_profile->address_state_short) + 
					          strlen(@$vd->nr_profile->address_city) > 30): ?>
					<br />
					<?php endif ?>
				<?php endif ?>
				<?= $vd->esc(@$vd->nr_profile->address_zip) ?>
			<!-- the city combined with the long state fits on 1 line -->
			<?php elseif (@$vd->nr_profile->address_state
			   && strlen(@$vd->nr_profile->address_state) + 
				   strlen(@$vd->nr_profile->address_city) <= 35): ?> 
				<?php if ($vd->nr_profile->address_city): ?>
				<?= $vd->esc(@$vd->nr_profile->address_city) ?>, 
				<?php endif ?>
				<?= $vd->esc(@$vd->nr_profile->address_state) ?>
				<?php if (@$vd->nr_profile->address_state ||
							 @$vd->nr_profile->address_city): ?>
					<?php if (strlen(@$vd->nr_profile->address_state) + 
					          strlen(@$vd->nr_profile->address_city) > 30): ?>
					<br />
					<?php endif ?>
				<?php endif ?>
				<?= $vd->esc(@$vd->nr_profile->address_zip) ?>
			<!-- city and long state on separate lines -->
			<?php else: ?>
				<?php if (@$vd->nr_profile->address_city): ?>
					<?= $vd->esc(@$vd->nr_profile->address_city) ?>
					<br ?>
				<?php endif ?>
				<?php if (@$vd->nr_profile->address_state): ?>
					<?= $vd->esc(@$vd->nr_profile->address_state) ?>
					<br />
				<?php endif ?>
				<?= $vd->esc(@$vd->nr_profile->address_zip) ?>
			<?php endif ?>
		</span>
		<span class="adr-tel">
			<?= $vd->esc(@$vd->nr_profile->phone) ?>
		</span>
	</address>
</section>		
				
<?php endif ?>