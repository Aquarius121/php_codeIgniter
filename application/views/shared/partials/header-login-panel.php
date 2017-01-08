<div class="login-panel">
	<ul>
		<li class="welcome">
			<?= $vd->esc(Auth::user()->first_name) ?>
			<?= $vd->esc(substr(Auth::user()->last_name, 0, 1)) ?>.
		</li>
		<li>
			<div class="btn-group dd-menu-nav">
				<a href="#" data-toggle="dropdown" class="btn dropdown-toggle">
					Account <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<?php if (Auth::is_admin_online()): ?>
					<li>
						<a href="#" class="status-muted">
							S: &nbsp;<span class="status-info"><?= strtoupper(file_get_contents('/etc/machine')) ?></span>
							(<span class="status-info-muted"><?= $ci->conf('ip_address') ?></span>)
						</a>
					</li>
					<li class="divider"></li>
					<?php endif ?>
					<?php if ($ci->uri->segment(1) !== 'admin' && Auth::user()->is_admin): ?>
					<li><a href="admin">
						<i class="icon-lock"></i> Admin Panel</a></li>
					<?php endif ?>
					<?php if ($ci->uri->segment(1) !== 'reseller' && Auth::user()->is_reseller): ?>
					<li><a href="reseller">
						<i class="icon-repeat"></i> Reseller Panel</a></li>
					<?php endif ?>
					<?php if ($ci->uri->segment(1) !== 'manage'): ?>
					<li><a href="manage">
						<i class="icon-user"></i> User Panel</a></li>
					<?php endif ?>
					<li><a href="manage/account">
						<i class="icon-cog"></i> Account Details</a></li>
					<li><a href="manage/upgrade">
						<i class="icon-circle-arrow-up"></i> Account Upgrades</a></li>
					<li><a href="manage/companies">
						<i class="icon-briefcase"></i> Manage Companies</a></li>
					<li><a href="<?= $ci->conf('website_url') ?>helpdesk/">
						<i class="icon-comment"></i> Helpdesk</a></li>
					<li><a href="shared/logout">
						<i class="icon-signout"></i> Logout</a></li>
				</ul>
			</div>
		</li>
	</ul>
</div>