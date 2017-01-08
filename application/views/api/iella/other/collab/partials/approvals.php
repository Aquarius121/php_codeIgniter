<?php if (count($vd->diff->approvals)): ?>
<h4 style="color:#1357a8;margin:20px 0 15px 0;">Approvals</h4>
<p>The following users have approved the content (version <?= (int) $vd->m_collab->version ?>) for release.</p>
<code style="padding:10px 20px;display:block;color:#999">	
	<?php foreach ($vd->rdo->approvals as $suid): ?>
		<?php if (in_array($suid, $vd->diff->approvals)): ?>
			<div style="color:#333">
		<?php else: ?>
			<div>
		<?php endif ?>
			<?= $vd->esc($vd->users[$suid]->name) ?>
			<?php $peo = new Passthru_Email_Obfuscator(); ?>
			<?php $email = $peo->obfuscate_parts($vd->users[$suid]->email); ?>
			<span style="color:#7E9EB3">&lt;<span><?= $vd->esc($email->pre) ?></span><span style="display:none">&#8203;</span><span><?= 
				$vd->esc($email->obfuscated) ?></span><span style="display:none">&#8203;</span><span><?= 
				$vd->esc($email->post) ?></span>&gt;</span>
		</div>
	<?php endforeach ?>	
</code>
<?php endif ?>