<?php 

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>
<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">
		<i class="icon-remove"></i>
	</button>
	<h3>Rejection Log &nbsp;</h3>
</div>

<div class="modal-body">
	<?php if (count($vd->rejections) > 0) : ?>   
		<ul>
		<?php foreach ($vd->rejections as $rej): ?>
			<?php if ($rej->process == Model_Pitch_Writing_Process::PROCESS_ADMIN_REJECTED): ?>
				<li class="pad-top-bot-10">
					<section class = "mb-editor-panel">
						<p class = "mb-author mb-editor">
							<strong>Admin to Writer</strong>
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

			<?php elseif ($rej->process == Model_Pitch_Writing_Process::PROCESS_CUSTOMER_REJECTED): ?>
				<li class="pad-top-bot-10">
					<section class="mb-customer-panel">
						<p class="mb-author mb-customer">
							<strong>Customer </strong>
							<span class="mb-date">
								Posted 					
								<?php $pr_process = Date::out($rej->process_date); ?>
								<?= $pr_process->format('m/j');	?>
							</span>
						</p>
						<p>
							<span>
								<?= nl2br($vd->esc($rej->comments)) ?>
							</span>
						</p>
					</section>
				</li>

			<?php elseif ($rej->process == Model_Pitch_Writing_Process::PROCESS_WRITTEN_SENT_TO_ADMIN): ?>
				<li class="pad-top-bot-10">
					<section class="mb-writer-panel">
						<p class="nopad">
							Writer submitted pitch
							<span class="mb-date">
								on  
								<?php $pr_process=Date::out($rej->process_date); ?>
								<?= $pr_process->format('m/j');	?>
							</span>
						</p>						
					</section>
				</li>

			<?php elseif ($rej->process == Model_Pitch_Writing_Process::PROCESS_SENT_TO_CUSTOMER): ?>
				<li class="pad-top-bot-10">
					<section class="mb-editor-panel">
						<p class="nopad">
							Admin sent to customer for review
							<span class="mb-date">
								on  
								<?php $pr_process=Date::out($rej->process_date); ?>
								<?= $pr_process->format('m/j');	?>
							</span>
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
		<input type="hidden" name="pitch_order_id" id="pitch_order_id" value="<?= $vd->pitch_order_id ?>" />  
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
				<button class="bt-silver btn-modal bt-orange" name="bt_send_to_writer" 
					value="1">Send to Writer</button>
			</div>
		</div>
	</form>
</div>
