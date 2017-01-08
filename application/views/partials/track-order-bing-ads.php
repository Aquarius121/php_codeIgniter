<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script>

(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"4072609"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");

window.uetq = window.uetq || [];
window.uetq.push({ 'gv': <?= json_encode(round($vd->cart->total_with_discount(), 2)) ?> });

</script>

<noscript><img src="//bat.bing.com/action/0?ti=4072609&amp;Ver=2&amp;gv=<?= number_format($vd->cart->total_with_discount(), 2) ?>"
	height="0" width="0" style="display:none; visibility: hidden;" /></noscript>