<?php if ($m_content->supporting_quote): ?>
<blockquote class="ei-quote">
	<q><?= $vd->esc($m_content->supporting_quote) ?></q>
	<p class="ei-author" id="sq-author">
		<?php if ($m_content->supporting_quote_title): ?>
		<?= $vd->esc(trim($m_content->supporting_quote_name)) ?>,
		<?= $vd->esc(trim($m_content->supporting_quote_title)) ?>
		<?php else: ?>
		<?= $vd->esc(trim($m_content->supporting_quote_name)) ?>
		<?php endif ?>
	</p>
</blockquote>
<?php endif ?>