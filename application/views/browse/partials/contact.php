<section class="al-block aside-press-contact accordian">
	
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		Press Contact
	</h3>

	<div class="aside-content accordian-content">

    	<?php $contact_image = Model_Image::find(@$vd->nr_contact->image_id); ?>
		<?php if ($contact_image): ?>
		<div class="contact-image">
			<a href="<?= $vd->nr_contact->url() ?>">
				<?php $ci_variant = $contact_image->variant('contact'); ?>
				<?php $ci_url = Stored_File::url_from_filename($ci_variant->filename); ?>
				<img src="<?= $ci_url ?>" alt="<?= $vd->esc($vd->nr_contact->name) ?>" />
			</a>
		</div>
		<?php endif ?>

		<div class="aside-content-block">
			<span class="aside-pc-name">
				<?php if ($ci->is_common_host): ?>
					<?= $vd->esc($vd->nr_contact->name) ?>
				<?php else: ?>
					<a href="<?= $vd->nr_contact->url() ?>"><?= 
					$vd->esc($vd->nr_contact->name) ?></a>
				<?php endif ?>
			</span>
			<span class="aside-pc-position">
				<?= $vd->esc($vd->nr_contact->title) ?>
			</span>
			<ul>				
				<?php if ($vd->nr_contact->email): ?>
				<li>
					<a href="mailto:<?= $vd->esc(strrev($vd->nr_contact->email)) ?>" 
						target="_blank" class="email-obfuscated safer-email-html">
						<?= safer_email_html($vd->esc($vd->cut($vd->nr_contact->email, 30))) ?>
					</a>
				</li>
				<?php endif ?>				
				<?php if ($vd->nr_contact->phone): ?>
				<li>
					<?= $vd->nr_contact->phone ?>					
				</li>
				<?php endif ?>
			</ul>
		</div>
		
	</div>
</section>