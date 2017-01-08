<li class="unpin-content noborder
	<?= value_if_test(!$result->is_pinned, 'hidden') ?>">
	<a href="#" data-content-id="<?= $result->id ?>" data-content-type="<?= $result->type ?>">Unpin</a></li>

<li class="pin-content 
	<?= value_if_test($result->is_pinned, 'hidden') ?>">
	<a href="#" data-content-id="<?= $result->id ?>" data-content-type="<?= $result->type ?>">Pin to Top</a></li>