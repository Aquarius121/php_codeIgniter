<div class="relative">
	<div class="share-side">

	<a class="share-facebook share-window" target="_blank"
		<?php if ($vd->m_content->is_published): ?>
		href="http://www.facebook.com/share.php?u=<?= 
		urlencode($ci->website_url($vd->m_content->url())) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>		
		data-width="640" data-height="400"></a>

	<a class="share-twitter share-window" target="_blank"
		<?php if ($vd->m_content->is_published): ?>
		href="http://twitter.com/intent/tweet?text=<?= 
		urlencode($vd->m_content->title) ?>+<?= 
		urlencode($ci->website_url($vd->m_content->url())) ?>" 
		<?php else: ?>
		disabled
		<?php endif ?>
		data-width="640" data-height="440"></a>
		
	<a class="share-google-plus share-window" target="_blank"
		<?php if ($vd->m_content->is_published): ?>
		href="https://plus.google.com/share?url=<?=
		urlencode($ci->website_url($vd->m_content->url())) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>
		data-width="500" data-height="600"></a>

	<a class="share-linkedin share-window" target="_blank"
		<?php if ($vd->m_content->is_published): ?>
		href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?=
		urlencode($ci->website_url($vd->m_content->url())) ?>&amp;title=<?=
		urlencode($vd->m_content->title) ?>&amp;summary=<?=
		urlencode(@$vd->m_content->summary) ?>&amp;source=<?=
		urlencode($ci->newsroom->company_name) ?>"
		<?php else: ?>
		disabled
		<?php endif ?>
		data-width="520" data-height="570"></a>
		
	</div>
</div>