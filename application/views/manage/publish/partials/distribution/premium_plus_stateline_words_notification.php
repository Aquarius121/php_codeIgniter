<p><strong>Bundled word limit reached!</strong></p>

<p class="nomarbot">
	Please be aware that your distribution pricing is based on <strong><?= 
	PRNewswire_Distribution::included_words(PRNewswire_Distribution::DIST_STATELINE) ?></strong> 
	words release total. Your content (including title and summary) exceeds <?= 
	PRNewswire_Distribution::included_words(PRNewswire_Distribution::DIST_STATELINE) ?> 
	words, so, for each <strong>additional</strong> 100 words you will be charged 
	<strong>$<?= number_format($vd->item_pps_extra_100_words->price) ?></strong>.
</p>