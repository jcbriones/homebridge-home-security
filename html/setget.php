<!DOCTYPE HTML>
<html>
<head>
<title>JcBriones HomeKit</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
<link rel="stylesheet" href="assets/css/main.css" />
<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
<noscript>
<link rel="stylesheet" href="assets/css/noscript.css" />
</noscript>
</head><body class="is-loading">

<!-- Wrapper -->
<div id="wrapper"> 
  
  <!-- Main -->
  <section id="main">

<?php
	$get = $_GET['get'];
	$pin = $_GET['pin'];
	$val = $_GET['val'];
	$exec = shell_exec('/var/db/DB '.$pin.' '.$val);
	echo "Executed: ".$exec;
?>

  </section>
  
  <!-- Footer -->
  <footer id="footer">
    <ul class="copyright">
      <li>&copy; JcBriones.com</li>
    </ul>
  </footer>
</div>

<!-- Scripts --> 
<!--[if lte IE 8]><script src="assets/js/respond.min.js"></script><![endif]--> 
<script>
	if ('addEventListener' in window) {
		window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
		document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
	}
</script>
</body>
</html>
