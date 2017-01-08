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
	<h3>Pre Writing Conversation</h3>
</div>

<div class="modal-body">
	<?php if (count($vd->history)) : ?>
		<ul>
			<?php foreach ($vd->history as $h): ?>
				<?php if($h->process == "writer_request_details_revision"): ?>
				<li>
					<section class="mb-writer-panel marbot-10">
						<p class="mb-author mb-writer">
							Writer 
                            <span class="mb-date">Posted 
								<?php $p_date = Date::out($h->process_date); ?>
								<?= $p_date->format('m/j');	?>														
							</span>
						</p>
                        
						<p><?php echo nl2br($vd->esc($h->comments));?></p>
					</section>
				</li>
				<?php endif;?> 

				<?php if($h->process == "sent_back_to_writer"): ?>
					<li>
						<section class="mb-customer-panel marbot-10">
							<p class="mb-author mb-customer">                           
								Admin to Writer 
								<span class="mb-date">Posted 
									<?php $p_date = Date::out($h->process_date); ?>
									<?= $p_date->format('m/j');	?>		
								</span>
							</p>
							<p>
								<?= nl2br($vd->esc($h->comments)) ?>
							</p>
						</section>
					</li>
				<?php endif;?>

				<?php if($h->process == "sent_to_customer_for_detail_change"): ?>
					<li>
						<section class="mb-customer-panel marbot-10">
						<p class="mb-author mb-customer">                            
							Admin to Customer 
							<span class="mb-date">Posted 
								<?php  $p_date = Date::out($h->process_date); ?>
								<?= $p_date->format('m/j');	?>	
							</span>
						</p>
						<p>
							<?= nl2br($vd->esc($h->comments)) ?>
						</p>
						</section>   
					</li>
				<?php endif;?>

				<?php if($h->process == "customer_revise_details"): ?>
					<li>                        
						<p class="mb-author">
							Customer Revised Details on                             
							<?php  $p_date = Date::out($h->process_date); ?>
							<?= $p_date->format('m/j');	?>
						</p>
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
			<div class="span4"></div>
			<div class="span8">
				<button class="bt-silver btn-modal btn-orange" name="bt_send_to_writer" type="submit"
					value="1">Send to Writer</button>

			
				<button class="bt-silver btn-modal btn-blue" name="bt_send_to_customer" type="submit"
					value="1">Send to Customer</button>
			</div>
		</div>
	</form>
</div>
    