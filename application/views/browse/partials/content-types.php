<?php if ($vd->is_auto_built_unclaimed_nr || $vd->nr_listed_types || 
			($vd->nr_profile && 
			  ($vd->nr_profile->is_enable_blog_posts && $vd->nr_profile->soc_rss) || 
			  ($vd->nr_profile->is_enable_social_wire && $vd->nr_profile->has_any_valid_social_feed()))): ?>

<section class="al-block accordian aside-newsroom">
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		Newsroom
		<a href="browse/rss" class="pull-right">
			<i class="fa fa-rss"></i> RSS
		</a>
	</h3>
	<ul class="accordian-content links-list <?= value_if_test(!$vd->m_content, 'nav-activate') ?>">
		<?php if ($ci->newsroom->source == Model_Company::SOURCE_MYNEWSDESK): ?>
			<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_PR, 'active') ?>">
				<a data-on="^browse/pr_all" href="browse/pr_all"> 
					<i class="fa fa-hand-right"></i> 
					<?php if (!empty($vd->content_type_labels->pr->plural)): ?>
						<?= $vd->esc($vd->content_type_labels->pr->plural) ?>
					<?php else: ?>
					Press Releases
					<?php endif ?>
			</a></li>
		<?php elseif (@$vd->is_auto_built_unclaimed_nr || @$vd->nr_listed_types->pr): ?>
			<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_PR, 'active') ?>">
				<a data-on="^browse/pr" href="browse/pr">
				<i class="fa fa-hand-right"></i> 
				<?php if (!empty($vd->content_type_labels->pr->plural)): ?>
					<?= $vd->esc($vd->content_type_labels->pr->plural) ?>
				<?php else: ?>
				Press Releases
				<?php endif ?>
				</a></li>
		<?php endif ?>

		<?php if ($vd->is_auto_built_unclaimed_nr || $vd->nr_listed_types->news): ?>
		<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_NEWS, 'active') ?>">
			<a data-on="^browse/news" href="browse/news">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->news->plural)): ?>
				<?= $vd->esc($vd->content_type_labels->news->plural) ?>
			<?php else: ?>
			News
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if ($vd->is_auto_built_unclaimed_nr || $vd->nr_listed_types->event): ?>
		<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_EVENT, 'active') ?>">
			<a data-on="^browse/event" href="browse/event">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->event->plural)): ?>
				<?= $vd->esc($vd->content_type_labels->event->plural) ?>
			<?php else: ?>
			Events
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if ($vd->is_auto_built_unclaimed_nr || $vd->nr_listed_types->image): ?>
		<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_IMAGE, 'active') ?>">
			<a data-on="^browse/image" href="browse/image">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->image->plural)): ?>
				<?= $vd->esc($vd->content_type_labels->image->plural) ?>
			<?php else: ?>
			Images
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if (@$vd->nr_listed_types->audio): ?>
		<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_AUDIO, 'active') ?>">
			<a data-on="^browse/audio" href="browse/audio">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->audio->plural)): ?>
				<?= $vd->esc($vd->content_type_labels->audio->plural) ?>
			<?php else: ?>
			Audio
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if (@$vd->nr_listed_types->video): ?>
		<li class="<?= value_if_test($vd->m_content && $vd->m_content->type == Model_Content::TYPE_VIDEO, 'active') ?>">
			<a data-on="^browse/video" href="browse/video">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->video->plural)): ?>
				<?= $vd->esc($vd->content_type_labels->video->plural) ?>
			<?php else: ?>
			Video
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if ($vd->is_auto_built_unclaimed_nr || $vd->nr_listed_types->contact): ?>
		<li>
			<a data-on="^browse/contact" href="browse/contact">
			<i class="fa fa-hand-right"></i>
			<?php if (!empty($vd->content_type_labels->contact->plural)): ?>
			<?= $vd->esc($vd->content_type_labels->contact->plural) ?>
			<?php else: ?>
			Contacts
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if ($vd->is_auto_built_unclaimed_nr || 
			(@$vd->nr_profile->is_enable_blog_posts && 
				@$vd->nr_profile->soc_rss)): ?>
		<li>
			<a data-on="^browse/blog" href="browse/blog">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->blog->plural)): ?>
			<?= $vd->esc($vd->content_type_labels->blog->plural) ?>
			<?php else: ?>
			Blog Posts
			<?php endif ?>
			</a></li>
		<?php endif ?>
		<?php if ($vd->nr_profile && $vd->nr_profile->has_any_valid_social_feed()): ?>
		<li>
			<a data-on="^browse/social" href="browse/social">
			<i class="fa fa-hand-right"></i> 
			<?php if (!empty($vd->content_type_labels->social->plural)): ?>
			<?= $vd->esc($vd->content_type_labels->social->plural) ?>
			<?php else: ?>
			Social Wire
			<?php endif ?>
			</a></li>
		<?php endif ?>
		
	</ul>
</section>
<?php endif ?>