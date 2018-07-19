<!-- iPad (Retina, portrait) SPLASHSCREEN-->
<link href="/static/assets/static/images/web-app-splash-portrait.png" media="(orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<!-- iPad (Retina, landscape) SPLASHSCREEN-->
<link href="/static/assets/static/images/web-app-splash.png" media="(orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<!-- iPad (Retina) ICON-->
<link href="/static/assets/static/images/web-app.png" sizes="144x144" rel="apple-touch-icon">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>

<script type="text/javascript">
  (function(document,navigator,standalone) {
    // prevents links from apps from oppening in mobile safari
    // this javascript must be the first script in your <head>
    if ((standalone in navigator) && navigator[standalone]) {
      var curnode, location=document.location, stop=/^(a|html)$/i;
      document.addEventListener('click', function(e) {
        curnode=e.target;
        while (!(stop).test(curnode.nodeName)) {
          curnode=curnode.parentNode;
        }
        // Condidions to do this only on links to your own app
        // if you want all links, use if('href' in curnode) instead.
        if(
          'href' in curnode && // is a link
          'stay' !in curnode.classList &&
          (chref=curnode.href).replace(location.href,'').indexOf('#') && // is not an anchor
          (	!(/^[a-z\+\.\-]+:/i).test(chref) ||                       // either does not have a proper scheme (relative links)
            chref.indexOf(location.protocol+'//'+location.host)===0 ) // or is in the same protocol and domain
        ) {
          e.preventDefault();
          location.href = curnode.href;
        }
      },false);
    }
  })(document,window.navigator,'standalone');
</script>
