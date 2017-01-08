<?php if ($m_content->supporting_quote): ?>
<blockquote id="sq-block">
	<q><?= $vd->esc($m_content->supporting_quote) ?></q>
	<p class="author" id="sq-author">
		<?php if ($m_content->supporting_quote_title): ?>
		<?= $vd->esc(trim($m_content->supporting_quote_name)) ?>,
		<?= $vd->esc(trim($m_content->supporting_quote_title)) ?>
		<?php else: ?>
		<?= $vd->esc(trim($m_content->supporting_quote_name)) ?>
		<?php endif ?>
		<?php if (!isset($raw) || !$raw): ?>
		<script>
		
		$(function() {
			var offset_block_bottom = 16;
			var offset_author_bottom = -25;
			var sq_block = $("#sq-block");
			var sq_author = $("#sq-author");
			var author_height = sq_author.height();
			// the case where there is no author
			if (author_height == 0) author_height = 12;
			var new_block_bottom = offset_block_bottom + author_height;
			var new_author_bottom = offset_author_bottom - author_height;
			sq_author.css('bottom', new_author_bottom);
			sq_block.css('margin-bottom', new_block_bottom);
		});
		
		</script>
		<?php endif ?>
	</p>
	<span class="arrow"></span>
</blockquote>
<?php endif ?>