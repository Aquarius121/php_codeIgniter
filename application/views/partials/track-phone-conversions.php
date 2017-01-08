<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<script>

/* gives a google number to track conversions over the phone */
(function(a,e,c,f,g,b,d){var h={ak:"993382658",cl:"cNjMCM-5ll0QgqLX2QM"};a[c]=a[c]||function(){(a[c].q=a[c].q||[]).push(arguments)};a[f]||(a[f]=h.ak);b=e.createElement(g);b.async=1;b.src="//www.gstatic.com/wcm/loader.js";d=e.getElementsByTagName(g)[0];d.parentNode.insertBefore(b,d);a._googWcmGet=function(b,d,e){a[c](2,b,h,d,null,new Date,e)}})(window,document,"_googWcmImpl","_googWcmAk","script");

/* target all .our-phone-number elements */
_googWcmGet('our-phone-number', '1-800-713-7278');

</script>