<?php if ( ! ($ci->session->get('ac_nr_tokened_visit_nr_id')
	&& $ci->session->get('ac_nr_tokened_visit_nr_id') == $ci->newsroom->company_id)): ?>
	<a href='browse/claim_nr' class="btn btn-flat-blue strong marbot-20">ABOUT THIS NEWSROOM</a>
<?php endif ?>