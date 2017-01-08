<div class="share-bottom marbot-10">

	<a class="share-facebook share-window no-custom marbot-10"
		<?php if ($vd->m_content->is_published): ?>
		href="http://www.facebook.com/share.php?u=<?= 
		urlencode($ci->website_url($vd->m_content->url())) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>
		target="_blank"
		data-width="640" data-height="400">
		<i class="fa fa-facebook no-custom"></i>
		Share on Facebook
	</a>

	<a class="share-twitter share-window no-custom marbot-10" target="_blank"
		<?php if ($vd->m_content->is_published): ?>
		href="http://twitter.com/intent/tweet?text=<?= 
		urlencode($vd->m_content->title) ?>+<?= 
		urlencode($ci->website_url($vd->m_content->url())) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>
		data-width="640" data-height="440">
		<i class="fa fa-twitter no-custom"></i>
		Share on Twitter
	</a>

	<a class="share-linkedin share-window no-custom marbot-10" 
		<?php if ($vd->m_content->is_published): ?>
		href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?=
		urlencode($ci->website_url($vd->m_content->url())) ?>&amp;title=<?=
		urlencode($vd->m_content->title) ?>&amp;summary=<?=
		urlencode(@$vd->m_content->summary) ?>&amp;source=<?=
		urlencode($ci->newsroom->company_name) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>
		target="_blank" data-width="520" data-height="570">
		<i class="fa fa-linkedin no-custom"></i>
	</a>
	
</div>