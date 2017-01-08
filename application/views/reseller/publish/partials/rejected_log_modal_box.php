<?php if (count($vd->pr->rejections) > 0): ?>   
	<ul class="conversation">
		<?php foreach ($vd->pr->rejections as $rej): ?>
		<?php if ($rej->process == "reseller_rejected" || $rej->process == "sent_back_to_writer"): ?>
			<li>
				<section class = "mb-editor-panel">
				<p class = "mb-author mb-editor">
					<?php if ($rej->actor == 'reseller'): ?> 
						Reseller 
					<?php elseif ($rej->actor == 'admin'): ?>
						Admin
					<?php endif ?>
					to Writer 
					<span class="mb-date">
						<?php $pr_process = Date::out($rej->process_date); ?>
						<?= $pr_process->format('M j, Y H:i')	?>
					</span>
				</p>
				<p>
					<?= nl2br($vd->esc($rej->comments)) ?>
				</p>
				</section>
			</li>
		<?php endif ?>
		<?php if ($rej->process == "customer_rejected"): ?>   
			<li>
				<section class="mb-customer-panel">
					<p class="mb-author  mb-customer">
						Customer 
						<span class="mb-date">
							<?php $pr_process=Date::out($rej->process_date); ?>
							<?= $pr_process->format('M j, Y H:i')	?>
						</span>
					</p>
					<p>
						<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
					</p>
				</section>
			</li>
		<?php endif ?>
		
		<?php if ($rej->process == "writer_request_details_revision"): ?>
			<li>
				<section class="mb-writer-panel">
					<p class="mb-author mb-writer">
						Writer 
						<span class="mb-date">
							<?php $pr_process=Date::out($rej->process_date); ?>
							<?= $pr_process->format('M j, Y H:i')	?>
						</span>
					</p>
					<p>
						<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
					</p>
				</section>
			</li> 
		<?php endif ?> 
		
		<?php if ($rej->process == "sent_to_customer_for_detail_change"): ?>
			<li>
				<section class="mb-editor-panel">
				<p class="mb-author mb-editor">
					<?php if ($rej->actor == 'reseller'): ?> 
						Reseller 
					<?php elseif ($rej->actor == 'admin'): ?>
						Admin
					<?php endif ?>
					 to Customer 
					<span class="mb-date">
						<?php $pr_process=Date::out($rej->process_date); ?>
						<?= $pr_process->format('M j, Y H:i')	?>
					</span>
				</p>
				<p>
					<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
				</p>
				</section>
			</li>
		<?php endif ?>
		
		<?php if ($rej->process == "written_sent_to_reseller"): ?>
			<li>
				<p class="mb-author">
					Writer submitted content - 
					<?php $p_date = Date::out($rej->process_date); ?>
					<?= $p_date->format('M j, Y') ?>
					<span class="muted"><?= $p_date->format('H:i') ?></span>
				</p>
			</li>
		<?php endif ?>
		
		<?php if ($rej->process == "customer_revise_details"): ?>
			<li>                        
				<p class="mb-author">
					Customer revised details -
					<?= $p_date->format('M j, Y') ?>
					<span class="muted"><?= $p_date->format('H:i') ?></span>
				</p>
			</li>
		<?php endif ?>
			
		<?php if ($rej->process == "sent_to_customer"): ?>
				<li>                                    
					<?php if ($rej->comments != ""): ?>
						<section class="mb-editor-panel">
							<p class="mb-author mb-editor">
								<?php if ($rej->actor == 'reseller'): ?> 
									Reseller 
								<?php elseif ($rej->actor == 'admin'): ?>
									Admin
								<?php endif ?>
								 to customer
								<span class="mb-date">
									<?php $pr_process = Date::out($rej->process_date); ?>
									<?= $pr_process->format('M j, Y H:i')	?>
								</span>
							</p>
							<p>
								<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
							</p>
						</section>
					 <?php else : ?>
						<p class="mb-author mb-editor">
							Sent to customer on 		
							<?php $pr_process = Date::out($rej->process_date); ?>
							<?= $pr_process->format('M j, Y H:i')	?>
						</p>
					 <?php endif ?>					 
				</li>
			<?php endif ?>
		<?php endforeach ?>
	</ul>
<?php endif ?>
