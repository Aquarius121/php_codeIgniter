<?php if ($result->is_prn_valid_lead && $result->valid_till_now): ?>
	<br />
	<span class="btn-success pad-10h">
		Valid lead 
		<?php if ($result->date_till_lead_valid): ?>
			till <?php $vt = Date::out($result->date_till_lead_valid); ?>
			<?= $vt->format('M j, Y') ?>
		<?php endif ?>
	</span>
<?php elseif ($result->is_prn_valid_lead && $result->lead_expired): ?>
	<br />
	<span class="btn-warning pad-10h">
		Lead expired 
		<?php if ($result->date_till_lead_valid): ?>
			on <?php $vt = Date::out($result->date_till_lead_valid); ?>
			<?= $vt->format('M j, Y') ?>
		<?php endif ?>
	</span>
<?php else: ?>
	<br />
	<span class="btn-danger pad-10h">
		Not valid per PRN SOP
	</span>
<?php endif ?>