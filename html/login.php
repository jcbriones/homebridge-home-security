<?php

# login.php
# SET VARIABLES #########################
#
# Set seconds before cookie expires.
#
session_set_cookie_params(600); // 10 minutes.
#
# Change your password. Very important!
#
$password = "homeKit"; 
#
# END SETTING VARIABLES #################

session_start();

$action = htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES);

function printForm($info)
{
global $action;

print <<<END

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
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-loading">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Main -->
					<section id="main">
						<header>
							<span class="avatar"><a href="index.php"><img src="images/homekit.png" alt="" /></a></span>
							<h1>Welcome to your Home</h1>
							<p>JcBriones HomeKit</p>
						</header>
						
						<hr />

						<h2>$info</h2>
						<form method="post" action="$action">
							<div class="field">
								<input type="password" name="pass" id="pass" placeholder="Password" />
							</div>
							<div class="field">
								<label>Stay login?</label>
								<input type="checkbox" name="auto" id="auto" value="1" /><label for="auto">Yes</label>
							</div>
                            <div class="field">
								<input type="submit" name="login" id="login" value="Login" />
							</div>
						</form>
                        
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
END;
}

if(isset($_POST['pass']))
{
	if($_POST['pass'] == $password)
	{
	// Success, set users IP address as login.
	$_SESSION['LOGIN'] = $_SERVER["REMOTE_ADDR"];
    	if($_POST['auto'] == "1")
        {
        	setcookie(session_name(),session_id(),strtotime( '+365 days' ));
        }
	// Redirect kills page expired error.
	header("Location: $action");
	}
	else
	{
	// Wrong password submitted.
	printForm('Wrong password! Try again');
	exit();
	}
}
else if(isset($_REQUEST['logout']))
{
unset($_SESSION['LOGIN']);
printForm('Logged Out');
exit();
}
else if(isset($_SESSION['LOGIN']))
{
// Check if IP address matches session login.
// Otherwise unset any session var named LOGIN.

	if($_SERVER["REMOTE_ADDR"] != $_SESSION['LOGIN'])
	{
	unset($_SESSION['LOGIN']);
	printForm('Enter your password to begin');
	exit();
	}

// Cookie already set login not required.
// Continue to page content.
}
else
{
printForm('Enter your password to begin');
exit();
}

?>
