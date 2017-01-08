<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script>

var tout_action_taken = "Ordered Something"; // You can change this to anything
var tout_automatically_mark_email_as_success = true; // You can change this to false

(function() {var t = document.createElement('script'); t.type = 'text/javascript'; t.async = true;var u = document.location.href;var ti = document.title;if(tout_automatically_mark_email_as_success){u+='#success=true';}var i = "?action_taken=" + encodeURIComponent(tout_action_taken) + "&title=" + encodeURIComponent(ti) + "&url=" + encodeURIComponent(u);t.src =  'https://go.toutapp.com/action/ewczt3dgfg' + i;var st = document.getElementsByTagName('script')[0];st.parentNode.insertBefore(t, st);})();

</script>