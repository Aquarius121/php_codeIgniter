<h3 class="nomar pad-5v marbot-15">Activate email alerts for this search</h3>
<p class="marbot-15">We can deliver daily email updates straight to your inbox based on your
	search. To get started enter your email address and click to activate.</p>

<input type="email" id="alert-email" class="form-control" disabled 
	value="<?= $vd->esc(Auth::user()->email) ?>" />