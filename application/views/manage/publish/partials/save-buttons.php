<?php if ($vd->m_content && (
		$vd->m_content->is_published || 
	   $vd->m_content->is_under_review)): ?>
	<div class="row">
		<div class="col-lg-7 col-sm-7 col-xs-7">
			<button type="submit" name="is_preview" value="1" 
				class="btn btn-primary aside-btn">Preview</button>
		</div>
		<div class="col-lg-5 col-sm-5 col-xs-5">
			<button class="btn btn-success pull-right aside-btn
				nomar autosave-button" type="submit" name="publish"
				value="1" >Save</button>
		</div>
	</div>
<?php else: ?>
	<div class="row marbot-15">
		<div class="col-lg-6 col-sm-6 col-xs-6">
			<button type="submit" name="is_preview" value="1"
				class="btn btn-primary aside-btn
				autosave-button">Preview</button>
		</div>
		<div class="col-lg-6 col-sm-6 col-xs-6">
			<button type="submit" name="is_draft" value="1" 
				class="btn btn-default pull-right aside-btn nomar
				autosave-button">Save Draft</button>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="ta-center">
				<button type="submit" name="publish" value="1" 
					class="btn btn-lg btn-success btn-publish nomar
					autosave-button col-lg-12">Submit</button>
			</div>
		</div>
	</div>
<?php endif ?>