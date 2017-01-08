<div class="alert alert-success">
	<strong>Saved!</strong> The content has been saved.
</div>

<?php if (@$post['view_now']): ?>
<script> window.open(<?= json_encode($ci->common()->url(
	$m_content->url())) ?>, "_blank"); </script>
<?php endif ?>