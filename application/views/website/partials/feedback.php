<div id="feedback">
<?php $ci->process_feedback(); ?>
<?php foreach ($ci->feedback as $feedback): ?>
<div class="feedback"><?= $feedback ?></div>
<?php endforeach ?>
</div>