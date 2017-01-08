<div class="chunkination chunkination-centered">
	
	<ul>
		
		<?php if ($first === null): ?>
		<li class="disabled"><a>First</a></li>
		<?php else: ?>
		<li><a class="chunk" data-chunk="<?= $first->chunk ?>">First</a></li>
		<?php endif ?>
		
		<?php if ($prev === null): ?>
		<li class="disabled"><a>Prev</a></li>
		<?php else: ?>
		<li><a class="chunk" data-chunk="<?= $prev->chunk ?>">Prev</a></li>
		<?php endif ?>
		
	</ul>
	
	<ul>
		
		<?php if ($prev_2 !== null): ?>
		<li>
			<a class="chunk" data-chunk="<?= $prev_2->chunk ?>"><?= $prev_2->chunk ?></a>
		</li>
		<?php endif ?>
		
		<?php if ($prev !== null): ?>
		<li>
			<a class="chunk" data-chunk="<?= $prev->chunk ?>"><?= $prev->chunk ?></a>
		</li>
		<?php endif ?>
		
		<li class="active">
			<a class="chunk" data-chunk="<?= $current->chunk ?>">
				<?= $current->chunk ?>
			</a>
		</li>
		
		<?php if ($next !== null): ?>
		<li>
			<a class="chunk" data-chunk="<?= $next->chunk ?>"><?= $next->chunk ?></a>
		</li>
		<?php endif ?>
		
		<?php if ($next_2 !== null): ?>
		<li>
			<a class="chunk" data-chunk="<?= $next_2->chunk ?>"><?= $next_2->chunk ?></a>
		</li>
		<?php endif ?>
		
	</ul>
	
	<ul>
		
		<?php if ($next === null): ?>
		<li class="disabled"><a>Next</a></li>
		<?php else: ?>
		<li><a class="chunk" data-chunk="<?= $next->chunk ?>">Next</a></li>
		<?php endif ?>
		
		<?php if ($last === null): ?>
		<li class="disabled"><a>Last</a></li>
		<?php else: ?>
		<li><a class="chunk" data-chunk="<?= $last->chunk ?>">Last</a></li>
		<?php endif ?>
		
	</ul>	
	
</div>