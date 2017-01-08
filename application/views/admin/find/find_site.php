<div class="transfer-result find-result row-fluid">
	<div class="span1 transfer-radio find-radio">
		<label class="radio-container">
			<input type="radio" name="transfer_select"
				data-gstring="&amp;filter_site=-1"
				class="transfer-selected find-selected"
				value="-1" />
			<span class="radio"></span>
		</label>		
	</div>
	<div class="transfer-primary span11">
		<div class="source"><?= $vd->esc(Model_Virtual_Source::INTERNAL) ?></div>
		<div class="status-alternative smaller">INTERNAL</div>
	</div>
</div>

<?php foreach ($vd->sites as $site): ?>
<div class="transfer-result find-result row-fluid">
	<div class="span1 transfer-radio find-radio">
		<label class="radio-container">
			<input type="radio" name="transfer_select"
				data-gstring="&amp;filter_site=<?= $site->id ?>"
				class="transfer-selected find-selected"
				value="<?= $site->id ?>" />
			<span class="radio"></span>
		</label>		
	</div>
	<div class="transfer-primary span11">
		<div class="source"><?= $site->name ?></div>
		<div class="status-info smaller">VIRTUAL</div>
	</div>
</div>
<?php endforeach ?>