<form action="<?= Auth::is_admin_mode() ? Admo::url() : $ci->website_url() ?>manage/companies/create" method="post">
	<div class="row">
		<div class="col-lg-12">
			<div class="input-group">
				<input type="text" placeholder="Company Name" name="company_name" class="form-control col-lg-12 in-text nomarbot">
				<div class="input-group-btn">
					<input type="submit" value="Add" class="btn btn-primary in-text nomarbot">
				</div>
			</div>
		</div>
	</div>
</form>