<code>
	<strong>VERSION: <?= $vd->versions[$vd->version] ?><br /><br /></strong>
	<div>This will set a cookie to use a specific site version.</div>
	<div>Clear your cookies to revert to normal behaviour.</div>
</code>

<br />
<form action="/common/switch_version" method="get">
<?php foreach ($vd->versions as $version => $label): ?>
<button type="submit" name="switch" value="<?= $version ?>"><?= $label ?></button>
<?php endforeach ?>
</form>