<?php $date = ''; $count = -1;?>
<?php if (count($vd->results)): ?>
	<?php foreach ($vd->results as $result): ?>
		<?php if ($result['type'] == "Images"): ?>
		
			<?php if($date != $result['date']->format('M j, Y H:i:s')):?>
				<?php $date = $result['date']->format('M j, Y H:i:s'); $count++; ?>
				<div class="his-header" data-rel="<?= $vd->counts[$count]+1 ?>">
					<div class="text-center"><i class="icon-plus"></i></div>
					<div><b><?= $vd->esc($result['date']->format('M j, Y H:i:s')) ?></b>
						<?php if($result['user_title']): ?>
							<b  class="user-<?= $vd->esc($result['user_title']) ?>"><span class="user-name"><?= $vd->esc($result['user_name']) ?></span> / <?= $vd->esc($result['user_title']) ?></b>
						<?php endif ?>
					</div>
					<div class="text-center" ><b><?= $vd->counts[$count]?> change<?php if($vd->counts[$count]>1): ?>s<?php endif ?></b></div>
				</div>
			<?php endif ?>
				<div style="display:none">
					<div></div>	
					<div><?= $vd->esc($result['field']) ?></div>
					<div>
						<?php foreach ($result['content'] as $content): ?>
							<div class='his-img'><img src="<?= $vd->esc($content['src']) ?>"><?= $content['text'] ?></div>
						<?php endforeach ?>
					</div>
				</div>
		<?php else: if($result['type'] == "video-array"): ?>
			<?php if($date != $result['date']->format('M j, Y H:i:s')):?>
				<?php $date = $result['date']->format('M j, Y H:i:s'); $count++; ?>
				<div class="his-header" data-rel="<?= $vd->counts[$count]+1 ?>">
					<div width="30" class="text-center"><i class="icon-plus"></i></div>
					<div width="350"><b><?= $vd->esc($result['date']->format('M j, Y H:i:s')) ?></b>
						<?php if($result['user_title']): ?>
							<b  class="user-<?= $vd->esc($result['user_title']) ?>"><span class="user-name"><?= $vd->esc($result['user_name']) ?></span> / <?= $vd->esc($result['user_title']) ?></b>
						<?php endif ?>
					</div>
					<div class="text-center"><b><?= $vd->counts[$count]?> change<?php if($vd->counts[$count]>1): ?>s<?php endif ?></b></div>
				</div>
			<?php endif ?>
				<div style="display:none">
					<div></div>
					<div><?= $vd->esc($result['field']) ?></div>
					<div>
						<?php foreach ($result['content'] as $content): ?>
							<?php if($content['ext1'] == "" && $content['ext2'] == ""): ?>
								<?= $vd->esc($content['text']) ?>:
									<del><?= $vd->esc($content['tag']) ?></del>
									<ins><?= $vd->esc($content['src']) ?></ins> <br>
							<?php else: ?>
								<?= $vd->esc($content['text']) ?>: 
									<?php if ($content['tag']): ?>
										<del><a target="_blank" href="<?= $vd->esc($content['ext1']) ?>"><?= 
											$vd->esc($content['tag']) ?></a></del>
									<?php endif ?>
									<?php if ($content['src']): ?>
										<ins><a target="_blank" href="<?= $vd->esc($content['ext2']) ?>"><?= 
											$vd->esc($content['src']) ?></a></ins><br>
									<?php endif ?>
							<?php endif ?>
						<?php endforeach ?>
					</div>
				</div>
			<?php else: if($result['type'] == "content-array"): ?>
				<?php if($date != $result['date']->format('M j, Y H:i:s')):?>
					<?php $date = $result['date']->format('M j, Y H:i:s'); $count++; ?>
					<div class="his-header" data-rel="<?= $vd->counts[$count]+1 ?>">
						<div width="30" class="text-center"><i class="icon-plus"></i></div>
						<div width="350"><b><?= $vd->esc($result['date']->format('M j, Y H:i:s')) ?></b>
							<?php if($result['user_title']): ?>
								<b  class="user-<?= $vd->esc($result['user_title']) ?>"><span class="user-name"><?= $vd->esc($result['user_name']) ?></span> / <?= $vd->esc($result['user_title']) ?></b>
							<?php endif ?>
						</div>
						<div class="text-center"><b><?= $vd->counts[$count]?> change<?php if($vd->counts[$count]>1): ?>s<?php endif ?></b></div>
					</div>
				<?php endif ?>
					<div style="display:none">
						<div></div>	
						<div><?= $vd->esc($result['field']) ?></div>
						<div>
							<?php foreach ($result['content'] as $content): ?>
							<?php if (!$content['text']) continue; ?>
							<<?= $content['tag'] ?>><?= $vd->esc($content['text']) ?></<?= $content['tag'] ?>><br>
							<?php endforeach ?>
						</div>
					</div>
				<?php else: ?>
					<?php if($date != $result['date']->format('M j, Y H:i:s')):?>
						<?php $date = $result['date']->format('M j, Y H:i:s'); $count++; ?>
						<div class="his-header" data-rel="<?= $vd->counts[$count]+1 ?>">
							<div width="30" class="text-center"><i class="icon-plus"></i></div>
							<div width="350"><b><?= $vd->esc($result['date']->format('M j, Y H:i:s')) ?></b>
								<?php if($result['user_title']): ?>
									<b  class="user-<?= $vd->esc($result['user_title']) ?>"><span class="user-name"><?= $vd->esc($result['user_name']) ?></span> / <?= $vd->esc($result['user_title']) ?></b>
								<?php endif ?>
							</div>
							<div class="text-center"><b><?= $vd->counts[$count]?> change<?php if($vd->counts[$count]>1): ?>s<?php endif ?></b></div>
						</div>
					<?php endif ?>
						<div style="display:none">							
							<div></div>
							<div><?= $vd->esc($result['field']) ?></div>
							<div>
								<?php if (in_array($result['key'], $this->vd->html_fields)): ?>
								<div class="html-content-code">
									<?= $result['content'] ?>
								</div>
								<?php else: ?>
								<?= $result['content'] ?>
								<?php endif ?>
							</div>
						</div>
				<?php endif ?>
			<?php endif ?>
		<?php endif ?>
	<?php endforeach ?>
<?php else: ?>
	<div style="width:100%;">None Found</div>
<?php endif ?>

<script>

$(function() {

	$("div.his-header").click(function() {
		if (!$(this).next().is(':visible')) {
			$(this).children('div:eq(0)').html('<i class="icon-minus"></i>');
			$(this).nextUntil("div.his-header").show();
		} else {
			$(this).children('div:eq(0)').html('<i class="icon-plus"></i>');
			$(this).nextUntil("div.his-header").hide();
		}	
	});

});

</script>