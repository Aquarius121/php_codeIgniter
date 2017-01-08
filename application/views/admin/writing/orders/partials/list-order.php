<?php extract(get_object_vars($result)); ?>

<td class="left order-data
	<?= value_if_test($writing_order && 
		$writing_order->is_editor_action_required(), 'action-required') ?>" 
	data-woc-id="<?= @$writing_order_code->id ?>"
	data-wo-id="<?= @$writing_order->id ?>">
	<div class="marbot-5">
		<span class="label-class">
			<a href="#" class="tl" title="<?= $writing_order_code->code() ?>">
				<strong class="status-muted">
					<?= $writing_order_code->nice_code() ?>
				</strong>
			</a>
		</span>
		<?php if ($writing_order_code->customer_email): ?>
			<?php if ($user && !$reseller): ?>
			<a data-gstring="&amp;filter_user=<?= $user->id ?>"
				href="#" class="add-filter-icon"></a>	
			<a href="#" class="tl status-alternative"
				title="<?= $vd->esc($user->name()) ?>">
				<?= $vd->esc($user->email) ?></a>
			<?php else:  ?>
			<a href="#" class="tl status-alternative"
				title="<?= $vd->esc($writing_order_code->customer_name) ?>">
				<?= $vd->esc($writing_order_code->customer_email) ?></a>
			<?php endif ?>
			<br />
		<?php endif ?>
		<?php if ($writing_order && $writing_order->status): ?>
			<div>
				<?php if ($company): ?>
				<a data-gstring="&amp;filter_company=<?= $company->id ?>"
					href="#" class="add-filter-icon"></a>
				<?= $vd->esc($company->name) ?>
				<span>|</span>
				<?php endif ?>
				<span class="muted">
					<?= $vd->esc($writing_order->primary_keyword) ?>
				</span>
			</div>
		<?php else: ?>
			<span>No Details</span>
			<span>|</span>
			<span class="muted">
				<?= $writing_order_code->code() ?>
			</span>
		<?php endif ?>
		<?php if ($reseller): ?>
			<div class="status-muted marbot-5">
				Reseller: 
					<a data-gstring="&amp;filter_user=<?= $reseller->id ?>"
						href="#" class="add-filter-icon"></a>
					<a href="#" class="tl status-info-muted"
						title="<?= $vd->esc($reseller->name()) ?>">				
					<?= $vd->esc($reseller->email) ?>
				</a>
			</div>
		<?php endif ?>
	</div>
	<ul>
		
		<?php if ($writing_order && $writing_order->status): ?>
			<?php if ($content): ?>
			<li>
				<a target="_blank" title="<?= $vd->esc($vd->cut($content->title, 35)) ?>" class="tl"
					href="<?= $ci->website_url("view/preview/{$content->id}") ?>">
					Preview
				</a>
			</li>
			<?php endif ?>
			<?php if ($writing_session && $company): ?>
			<li>
				<a target="_blank" href="<?= $company->newsroom()->url() ?>manage/writing/process/<?= $writing_session->id ?>/4/review">
					Edit Order
				</a>
			</li>
			<?php elseif ($reseller): ?>
			<li>
				<a target="_blank" href="writing/prdetails/edit/<?= $writing_order->id ?>/<?= $writing_order_code->code() ?>">
					Edit Order
				</a>
			</li>
			<?php endif ?>
			<?php if ($content): ?>
			<li>
				<a target="_blank" href="admin/publish/edit/<?= $content->id ?>">
					Edit PR
				</a>
			</li>
			<?php endif ?>
		<?php endif ?>
		
		<li>
			<a href="#" class="archive-order-button">
				Archive
			</a>
		</li>
		
	</ul>	
</td>