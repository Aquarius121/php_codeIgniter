<?php

// rewrite press release urls
$rewrite = new stdClass();
$rewrite->name = 'news_center';
$rewrite->pattern = '#^/newsroom($|/(.*))$#';
$rewrite->replace = '/news-center/$1';
$env['rewrite'][] = $rewrite;

// rewrite press release urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/press-release/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite news urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/news/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite event urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/event/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite audio urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/audio/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite image urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/image/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite video urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/video/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite blog urls
$rewrite = new stdClass();
$rewrite->name = 'view_content';
$rewrite->pattern = '#^/post/(.+)$#';
$rewrite->replace = '/view/content/$1';
$env['rewrite'][] = $rewrite;

// rewrite contact urls
$rewrite = new stdClass();
$rewrite->name = 'view_contact';
$rewrite->pattern = '#^/contact/(.+)$#';
$rewrite->replace = '/view/contact/slug/$1';
$env['rewrite'][] = $rewrite;

// rewrite single product pricing urls
$rewrite = new stdClass();
$rewrite->name = 'pricing-page';
$rewrite->pattern = '#^/pricing-page#';
$rewrite->replace = '/pricing_alternative';
$env['rewrite'][] = $rewrite;

// rewrite single product pricing urls
$rewrite = new stdClass();
$rewrite->name = 'single-pr';
$rewrite->pattern = '#^/single-pr#';
$rewrite->replace = '/pricing_alternative/single_pr';
$env['rewrite'][] = $rewrite;

// rewrite single product pricing urls
$rewrite = new stdClass();
$rewrite->name = 'single-prn';
$rewrite->pattern = '#^/single-prn#';
$rewrite->replace = '/price/single_prn';
$env['rewrite'][] = $rewrite;

// rewrite single product pricing urls
$rewrite = new stdClass();
$rewrite->name = 'membership-plan';
$rewrite->pattern = '#^/membership-plan#';
$rewrite->replace = '/pricing_alternative/membership_plan';
$env['rewrite'][] = $rewrite;

// rewrite claim newsroom
$rewrite = new stdClass();
$rewrite->name = 'claim_nr';
$rewrite->pattern = '#^/c/(.+)$#';
$rewrite->replace = '/browse/claim_nr/rep/$1';
$env['rewrite'][] = $rewrite;

// rewrite in the media
$rewrite = new stdClass();
$rewrite->name = 'in-the-media';
$rewrite->pattern = '#^/browse/in-the-media#';
$rewrite->replace = '/browse/owler_news/$1';
$env['rewrite'][] = $rewrite;

// rewrite FC feed
$rewrite = new stdClass();
$rewrite->name = 'fincontent';
$rewrite->pattern = '#^/fincontent\.xml$#';
$rewrite->replace = '/distribution/fin_content';
$env['rewrite'][] = $rewrite;

// rewrite DJ feed
$rewrite = new stdClass();
$rewrite->name = 'digital_journal';
$rewrite->pattern = '#^/custom_rss/inewswire-premium-pr-news$#';
$rewrite->replace = '/distribution/digital_journal';
$env['rewrite'][] = $rewrite;

// rewrite digital_media_net feed
$rewrite = new stdClass();
$rewrite->name = 'digital_media_net';
$rewrite->pattern = '#^/custom_rss/inewswire-pr-news$#';
$rewrite->replace = '/distribution/digital_media_net';
$env['rewrite'][] = $rewrite;

// rewrite single product pricing urls
$rewrite = new stdClass();
$rewrite->name = 'features-distribution-premium';
$rewrite->pattern = '#^/features/distribution/premium#';
$rewrite->replace = '/features/distribution_premium';
$env['rewrite'][] = $rewrite;

?>