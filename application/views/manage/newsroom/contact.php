<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 col-md-6 page-title">
				<h2>Company Contacts</h2>
			</div>
			<div class="col-lg-6 actions">
				<a href="manage/newsroom/contact/edit" class="btn btn-primary">Add Contact</a>
			</div>
		</div>			
	</header>
		
	<?php if (!$ci->newsroom->is_active): ?>
	<div class="below-header-feedback marbot-20">
		<div class="alert alert-info">
			<strong>Attention!</strong> Additional contacts are not visible 
			for companies without a newsroom. Only the main contact is used.
		</div>
	</div>
	<?php endif ?>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="table-responsive">
					<table class="table" id="selectable-results">
						<thead>
					
							<tr>
								<th class="left">Contact Name</th>
								<th>Title</th>
								<th>Email</th>
							</tr>
							
						</thead>
						<tbody>
							
							<?php foreach ($vd->results as $result): ?>
							<tr>
								<td class="left td-has-image <?= value_if_test(isset($result->image_url), 'finger') ?>">
									<?php if (isset($result->image_url)): ?>
									<a href="manage/newsroom/contact/edit/<?= $result->id ?>">
										<img src="<?= $result->image_url ?>" />
									</a>
									<?php endif ?>
									<h3>
										<a href="manage/newsroom/contact/edit/<?= $result->id ?>">
											<?= $vd->esc($result->name()) ?>
										</a>
										<?php if ($result->id == $this->newsroom->company_contact_id): ?>
										(Press Contact)
										<?php endif ?>
									</h3>
									<ul class="actions">
										<li><a href="<?= $result->url() ?>">View</a></li>	
										<li><a href="manage/newsroom/contact/edit/<?= $result->id ?>">Edit</a></li>
										<li><a href="manage/newsroom/contact/delete/<?= $result->id ?>">Delete</a></li>
									</ul>
								</td>
								<td>
									<?= $vd->esc($result->title) ?>
								</td>
								<td>
									<?= $vd->esc($result->email) ?>
								</td>
							</tr>
							<?php endforeach ?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

				
	<?= $vd->chunkination->render() ?>

	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Company Contacts</p>
	
	
			
			
</div>