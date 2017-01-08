<div class="aside-social-list clearfix">
	<ul>
		<?php if (@$vd->nr_profile->soc_twitter): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Twitter_Profile::url($vd->nr_profile->soc_twitter))
			?>"><i class="fa fa-twitter-square"></i></a></li>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_facebook): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Facebook_Profile::url($vd->nr_profile->soc_facebook))
			?>"><i class="fa fa-facebook-square"></i></a></li>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_gplus): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_GPlus_Profile::url($vd->nr_profile->soc_gplus))
			?>"><i class="fa fa-google-plus-square"></i></a></li>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_youtube): ?>
			<?php if (@$vd->nr_profile->soc_youtube_is_channel): ?>
				<li><a target="_blank" href="<?= 
					$vd->esc(Social_Youtube_Profile::channel_url($vd->nr_profile->soc_youtube))
					?>"><i class="fa fa-youtube-square"></i></a></li>
			<?php else: ?>
				<li><a target="_blank" href="<?= 
					$vd->esc(Social_Youtube_Profile::url($vd->nr_profile->soc_youtube))
					?>"><i class="fa fa-youtube-square"></i></a></li>
			<?php endif ?>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_linkedin): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Linkedin_Profile::url($vd->nr_profile->soc_linkedin))
			?>"><i class="fa fa-linkedin-square"></i></a></li>
		<?php endif ?>
        <?php if (@$vd->nr_profile->soc_pinterest): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Pinterest_Profile::url($vd->nr_profile->soc_pinterest))
			?>"><i class="fa fa-pinterest-square"></i></a></li>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_vimeo): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Vimeo_Profile::url($vd->nr_profile->soc_vimeo))
			?>"><i class="fa fa-vimeo-square"></i></a></li>
		<?php endif ?>
		<?php if (@$vd->nr_profile->soc_instagram): ?>
		<li><a target="_blank" href="<?= 
			$vd->esc(Social_Instagram_Profile::url($vd->nr_profile->soc_instagram)) ?>"
			><i class="fa fa-instagram"></i></a></li>
		<?php endif ?>
	</ul>
</div>