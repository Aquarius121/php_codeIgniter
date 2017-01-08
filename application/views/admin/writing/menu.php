<section class="top-menu">
	<div class="container">
		<div class="row-fluid">
			<ul id="nav-menu" class="nav-activate nav-reverse">
				<li>
					<a data-on="^admin/writing/orders" href="admin/writing/orders<?= $vd->esc(gstring()) ?>">
						Orders
					</a>
				</li>
				<li>
					<a data-on="^admin/writing/orders/[_a-z]+/(4|12)" href="admin/writing/orders/all/4<?= $vd->esc(gstring()) ?>">
						Orders (Direct)
					</a>
				</li>
				<li>
					<a data-on="^admin/writing/orders/[_a-z]+/(3|11)" href="admin/writing/orders/all/3<?= $vd->esc(gstring()) ?>">
						Orders (Reseller)
					</a>
				</li>
				<li>
					<a data-on="^admin/writing/pitch" href="admin/writing/pitch/pw_order/all<?= $vd->esc(gstring()) ?>">
						Pitches
					</a>
				</li>
				<li>
					<a data-on="^admin/writing/writers" href="admin/writing/writers<?= gstring() ?>">
						Writers
					</a>
				</li>
			</ul>
		</div>
	</div>
</section>