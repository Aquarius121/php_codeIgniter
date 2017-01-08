<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Users</h1>
				</div>
				<div class="span6">
					<a href="admin/users/view" class="btn bt-silver pull-right">New User</a>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/users/all" 
				href="admin/users/all<?= $vd->esc(gstring()) ?>">All</a></li>
			<li><a data-on="^admin/users/reseller" 
				href="admin/users/reseller<?= $vd->esc(gstring()) ?>">Resellers</a></li>
			<li><a data-on="^admin/users/admin" 
				href="admin/users/admin<?= $vd->esc(gstring()) ?>">Admins</a></li>
		</ul>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">User</th>
						<th>Details</th>
						<th>Created</th>
					</tr>
					
				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>

					<?php $virtual_user = null; ?>
					<?php $virtual_source = null; ?>
					<?php if ($result->is_virtual()): ?>
					<?php $virtual_user = $result->virtual_user(); ?>
					<?php $virtual_source = $virtual_user ? $virtual_user->virtual_source() : null; ?>
					<?php endif ?>

					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>

								<a href="admin/publish?filter_user=<?= $result->id ?>" 
									class="add-filter-icon"></a>

								<?php if ($result->is_virtual()): ?>
								<strong class="label-class status-alternative-2"><?= 
									$vd->esc(strtoupper(UUID::short($result->email))) ?></strong>
								<a class="view" href="admin/users/view/<?= $result->id ?>">
									<?= $vd->esc($virtual_user->email) ?>
								</a>
								<?php else: ?>
								<a class="view" href="admin/users/view/<?= $result->id ?>">
									<?= $vd->esc($result->email) ?>
								</a>
								<?php endif ?>
								
								<?php if ($result->source_title): ?>
									(<a href="<?= $result->nr->url() ?>" target="_blank"><?= 
										$result->source_title ?></a>)
								<?php endif ?>

								<?php if (!strlen($result->email)): ?>
								<a class="view" href="admin/users/view/<?= $result->id ?>" class="status-muted">
									<?= $result->id ?>
								</a>
								<?php endif ?>

							</h3>	
							<ul>
								<li><a href="admin/users/view/<?= $result->id ?>">Edit</a></li>
								<li><a href="<?= Admo::url('default', $result->id) ?>" target="_blank" 
									class="status-false">Admin Session</a></li>
							</ul>
						</td>
						<td>
							<?php if ($result->is_virtual()): ?>
							<?php if ($virtual_user && $virtual_source): ?>
							<div class="virtual-user-source"><span class="source"><?= $virtual_source->name ?></span> VIRTUAL</div>
							<?php endif ?>
							<?php endif ?>
							<div>
								<?= $vd->esc($result->first_name) ?>
								<?= $vd->esc($result->last_name) ?>
							</div>
							<?php if (!$result->is_virtual()): ?>
							<div class="muted">
								<?php if ($result->is_enabled && $result->is_verified): ?>
									<?= value_if_test($result->is_admin, 'Admin') ?>
									<?= value_if_test($result->is_reseller, 'Reseller') ?>
									<?php if (!$result->is_admin && !$result->is_reseller): ?>
										Normal User
									<?php endif ?>
								<?php else: ?>
								<?php if ($result->is_verified): ?>
									Verified, Disabled
								<?php else: ?>
									Not Verified
								<?php endif ?>
								<?php endif ?>
							</div>
							<?php endif ?>
						</td>
						<td>
							<?php if (@$result->source_title): ?>
								<?php $created = Date::out($result->date_claim_finalized); ?>
							<?php else: ?>
								<?php $created = Date::out($result->date_created); ?>
							<?php endif ?>
							<?= $created->format('M j, Y') ?>&nbsp;
							<span class="muted"><?= $created->format('H:i') ?></span>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Users
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>