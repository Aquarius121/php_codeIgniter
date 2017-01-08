<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script>

// this could be executed before the 
// google analytics is loaded so we 
// must call the code on document load

$(function() {
	
	var send_event = window.ga === undefined ?
		__console : window.ga;
		
	send_event("send", "event", "User", "Register", 
		<?= json_encode($vd->user->email) ?>, 1);
	
});

</script>