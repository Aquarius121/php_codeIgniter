<div class="clearfix">
	<div class="pull-left grid-report ta-left">
		All times are in UTC.
	</div>
	<div class="pull-right grid-report">
		Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> 
		Companies
	</div>
</div>


<div class="row-fluid">
	<div class="span12 pad-20v">
		<div class="pull-right">
			<button type="submit" class="btn btn-success" name="bulk_build_nrs"
				id="bulk_build_nrs" value="1">Bulk Build Newsrooms</button>
		</div>
	</div>
	<div class="span2"></div>
</div>