<section class="top-menu">
	<div class="container">
		<div class="row-fluid">
			<ul class="nav-activate">				
				<li>
					<a data-on="^admin/store" 
						href="admin/store/order<?= $vd->esc(gstring()) ?>">Newswire.com
					</a>
				</li>
				<li class="separator"></li>
				<li>
					<a data-on="^admin/virtual/store/<?= Model_Virtual_Source::ID_PRESSRELEASECOM ?>"
						href="admin/virtual/store/<?= Model_Virtual_Source::ID_PRESSRELEASECOM ?>">PressRelease.com</a>
				</li>				
			</ul>
		</div>
	</div>
</section>