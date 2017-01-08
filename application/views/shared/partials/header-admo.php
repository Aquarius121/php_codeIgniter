<?php if (Auth::is_admin_mode()): ?>
<div class="admin-bar-spacer no-print"></div>
<div class="admin-bar no-print">
	<div class="admin-bar-header">
		<strong>Admin Session:</strong> Your current session is for the 
		<?= value_if_test(Auth::user()->is_reseller, 'reseller', 'user') ?>
		<a href="<?= $ci->website_url() ?>admin/users/view/<?= Auth::user()->id ?>">
			<strong><?= $vd->esc(Auth::user()->email) ?></strong></a>.
		<strong class="pull-right">
			<ul>
				<li><a id="admin-demo" href="#">Demo</a></li>
				<li><a href="<?= $ci->common()->url('admin') ?>">Leave Session</a></li>
			</ul>
		</strong>
		<script>

		$(function() {

			$("#admin-demo").on("click", function(ev) {
				ev.preventDefault();
				$.get("common/admo_demo", function() {
					window.location = window.location;
				});
			});

		});

		</script>
	</div>	
	<?php if (Auth::user()->is_virtual()): ?>
	<div class="admin-bar-virtual">	
		<?php $virtual_user = Auth::user()->virtual_user(); ?>
		<?php $virtual_source = $virtual_user->virtual_source(); ?>
		<div class="pull-left">
			<strong class="label-class">VIRTUAL</strong>
			&nbsp;&nbsp;
			<?php if ($virtual_user->name()): ?>
				<?= $vd->esc($virtual_user->name()) ?>
				&nbsp;&nbsp;			
				[ <a href="common/vuras/<?= Auth::user()->id ?>/default">
					<strong><?= $vd->esc($virtual_user->email) ?></strong></a> ]
			<?php else: ?>
				<a href="common/vuras/<?= Auth::user()->id ?>/default">
					<strong><?= $vd->esc($virtual_user->email) ?></strong></a>
			<?php endif ?>
			
		</div>
		<?php if ($virtual_source): ?>
		<div class="pull-right">
			<ul>
				<li><a href="common/vuras/<?= Auth::user()->id ?>/default">
					Remote Session</a></li>
				<?php if ($virtual_source->website): ?>
				<li><a href="<?= $vd->esc($virtual_source->website) ?>"><?= 
					$vd->esc($virtual_source->name) ?></a></li>
				<?php else: ?>
				<li><?= $vd->esc($virtual_source->name) ?></li>	
				<?php endif ?>
			</ul>
		</div>
		<?php endif ?>
	</div>
	<?php else: ?>
	<div class="admin-bar-menu">	
		<ul>
			
			<?php if ($vd->is_browse): ?>

				<?php if ($this->newsroom && $this->newsroom->company_id > 0): ?>
					<li><span class="panel-label">U</span><a href="<?= $this->newsroom->url() ?>manage">Dashboard</a></li>
					<li><span class="panel-label">U</span><a href="<?= $this->newsroom->url() ?>manage/publish/pr">Press Releases</a></li>
					<li><span class="panel-label">U</span><a href="<?= $this->newsroom->url() ?>manage/newsroom/company">Company Profile</a></li>
				<?php endif ?>

				<?php if ($vd->m_content && $vd->m_content->id): ?>
					<li>
						<span class="panel-label">U</span>
						<a href="<?= $ci->website_url() ?>admin/publish/edit/<?= $vd->m_content->id ?>">Edit Content</a>
					</li>				
					<li>
						<span class="panel-label">A</span>
						<a href="<?= $ci->website_url() ?>admin/publish/<?= 
							$vd->m_content->type ?>/all?filter_search=<?= 
							$vd->esc($vd->m_content->slug) ?>">Locate in Admin</a>
					</li>
				<?php endif ?>

			<?php else: ?>

				<li><span class="panel-label">U</span><a href="manage/upgrade">Upgrades</a></li>
				<li><span class="panel-label">U</span><a href="manage/account/billing">Billing</a></li>
				<li><span class="panel-label">U</span><a href="manage/account/order/history">Orders</a></li>
				<li><span class="panel-label">U</span><a href="manage/account/transaction/history">Transactions</a></li>
				<li><span class="panel-label">U</span><a href="manage/account/renewal">Renewals</a></li>

			<?php endif ?>

		</ul>
		<ul>
			<?php if ($vd->is_manage): ?>
			<li id="required-js-toggles">
				<script>

				$(function() {

					var buttons = $(".required-js-toggle");
					var update_buttons = function() {
						buttons.filter(".enable").toggleClass("dnone", window.required_js.enabled);
						buttons.filter(".disable").toggleClass("dnone", !window.required_js.enabled);
					};

					if (!window.required_js) {
						buttons.parent().remove();
						return;
					}

					buttons.on("click", function(ev) {
						ev.preventDefault();
						window.required_js.enabled = !window.required_js.enabled;
						update_buttons();
					});					

					update_buttons();

				});

				</script>
				<a href="#" class="required-js-toggle disable">Validation</a>
				<a href="#" class="required-js-toggle enable">Validation</a>
			</li>
			<li>
				<?php $cart = Cart::instance() ?>
				<span class="panel-label checkout-total-cost">					
					<?= $cart->format($cart->total_with_discount()) ?>
				</span>
				<a href="manage/order">
					Checkout
				</a>
			</li>
			<?php endif ?>
		</ul>
	</div>
	<?php endif ?>
</div>
<?php endif ?>