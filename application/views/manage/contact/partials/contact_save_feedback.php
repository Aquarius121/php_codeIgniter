<div class="alert alert-success">
	<strong>Saved!</strong> The contact has been saved.
	<?php if (!$ci->is_default_newsroom_host): ?>
	<a href="manage/contact/contact/edit/<?= $contact->id ?>">Edit</a> the contact. 
	<?php endif ?>
</div>