<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<ul class="nav nav-tabs nav-activate pw_order_menu pw-admin" id="tabs">
    <li><a data-on="^admin/writing/pitch/pw_order/all" 
        href="admin/writing/pitch/pw_order/all<?= $vd->esc(gstring()) ?>">All</a></li>
    <li><a data-on="^admin/writing/pitch/assign" 
			href="admin/writing/pitch/assign/all<?= $vd->esc(gstring()) ?>">Assign
			<?php if($vd->assign_count > 0): ?>
				<span class="menu-count"><?= $vd->assign_count ?></span>
			<?php endif ?>
		</a></li>
    <li><a data-on="^admin/writing/pitch/pending_writing" 
        	href="admin/writing/pitch/pending_writing/all<?= $vd->esc(gstring()) ?>">Pending
        	<?php if($vd->pending_count > 0): ?>
				<span class="menu-count"><?= $vd->pending_count ?></span>
			<?php endif ?>
        </a></li>

	<li><a data-on="^admin/writing/pitch/review_pitch|^admin/writing/pitch/review" 
		href="admin/writing/pitch/review/all<?= $vd->esc(gstring()) ?>">Review
			<?php if($vd->review_count > 0): ?>
				<span class="menu-count"><?= $vd->review_count ?></span>
			<?php endif ?>
		</a></li>

	<li><a data-on="^admin/writing/pitch/rejected"
			href="admin/writing/pitch/rejected/all<?= $vd->esc(gstring()) ?>">Rejected
				<?php if($vd->rejected_count > 0): ?>
					<span class="menu-count"><?= $vd->rejected_count ?></span>
				<?php endif ?>
		</a></li>

	<li><a data-on="^admin/writing/pitch/customer_review" 
        	href="admin/writing/pitch/customer_review/all<?= $vd->esc(gstring()) ?>">Customer Review        	
        </a></li>       
	
        
	<li><a data-on="^admin/writing/pitch/pw_order/archive" 
        href="admin/writing/pitch/pw_order/archive<?= $vd->esc(gstring()) ?>">Archive</a></li>                    
</ul>