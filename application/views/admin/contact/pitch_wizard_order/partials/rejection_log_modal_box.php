<div class="modal-body">
	<?php if (count($vd->rejections) > 0) : ?>   
		<ul>
		<?php foreach ($vd->rejections as $rej): ?>
			<?php if ($rej->process == Model_Pitch_List_Process::PROCESS_ADMIN_REJECTED): ?>
				<li class="pad-top-bot-10">
					<section class = "mb-writer-panel">
						<p class = "mb-author mb-writer">
							<strong>Admin to List Builder</strong>
							<span class="mb-date">
								Posted 					
								<?php $pr_process = Date::out($rej->process_date); ?>
								<?= $pr_process->format('m/j');	?>
							</span>
						</p>
						<p>
							<span class="status-false">
								<?= nl2br($vd->esc($rej->comments)) ?>
							</span>
						</p>
					</section>
				</li>
			<?php elseif ($rej->process == Model_Pitch_List_Process::PROCESS_SENT_TO_ADMIN): ?>
				<li class="pad-top-bot-10">
					<section class="mb-customer-panel">
						<p class="mb-author  mb-customer">
							List Builder uploaded list
							<span class="mb-date">
								on  
								<?php $pr_process=Date::out($rej->process_date); ?>
								<?= $pr_process->format('m/j');	?>
							</span>
						</p>
						<p>
							<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
						</p>
					</section>
				</li>
			<?php endif;?>            
		<?php endforeach ?>
		</ul>
	<?php endif;?>
</div>

<div class="modal-footer">
	<form action="" method="post">
		<input type="hidden" name="pitch_list_id" id="pitch_list_id" value="<?= $vd->pitch_list_id ?>" />  
		<ul class="row-fluid">
			<li>
				<label for="reply_msg">Comments</label>
			</li>
			<li>
				<textarea name="comments" id="comments" class="span12"></textarea>
			</li>
		</ul>
		<div class="row-fluid">
			<div class="span4">
			</div>
			<div class="span8">
				<button class="bt-silver btn-modal bt-orange" name="bt_send_to_list_builder" 
					value="1">Send to List Builder</button>
			</div>
		</div>
	</form>
</div>
