<?php require_once("login.php"); ?>
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
<script src='https://code.responsivevoice.org/responsivevoice.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/annyang/2.0.0/annyang.min.js"></script>
<script src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script>responsiveVoice.setDefaultVoice("US English Female");</script>
<script type="text/javascript">
$(document).ready(function(){
 $("#data").load("data.php");
 setInterval(function(){
  $("#data").load("data.php");
 },5000);
});

// Voice Recognition 'annyang'
if (annyang) {
	// Commands
	var commands = {
	  // annyang will capture anything after a splat (*) and pass it to the function.
	  // e.g. saying "Show me Batman and Robin" is the same as calling showFlickr('Batman and Robin');
	  'Hello (home)': function() { responsiveVoice.speak("Hello Jc! How can I help you?"); },
	
	  // A named variable is a one word variable, that can fit anywhere in your command.
	  // e.g. saying "calculate October stats" will call calculateStats('October');
	  'calculate :month stats': function() { responsiveVoice.speak("Hello Jc! How can I help you?"); },
	  
	  'Turn on my *acc': function(acc) {
		  responsiveVoice.speak("I'm now turning on your "+acc);
		  document.getElementById('lrl1').checked = true;
		  },
	  
	  'Turn off my *acc': function(acc) {
		  responsiveVoice.speak("I'm now turning off your "+acc);
		  document.getElementById('lrl0').checked = true;
		  },
	
	  // By defining a part of the following command as optional, annyang will respond to both:
	  // "say hi to my little friend" as well as "say hi friend"
	  'say hi (to my little friend)': function() { responsiveVoice.speak("Hi friend! It's nice to meet you"); }
	};

  // Add our commands to annyang
  annyang.addCommands(commands);

  // Start listening. You can call this here, or attach this call to an event, button, etc.
  annyang.start();
}

 // Set the new option to the given value
 function setAccessoryElem(accname,val1,val2,pin,elem)
 {
   if(elem.checked || elem.selected) {
     var val = 1;
	 responsiveVoice.speak("Your "+accname+" is now "+val1);
   }
   else {
     var val = 0;
	 responsiveVoice.speak("Your "+accname+" is now "+val2);
   }
   setAccessory(pin, val);
 }
 
 // Call set.php and set the accessory
 function setAccessory(pin,val)
 {
  $.ajax({
  
  type : 'POST',
  url  : 'set.php',
  data : 'pin='+pin+'&val='+val,
  });
  return false;
 }

 function setAccessoryElemWithPass(accname,val1,val2,pin,elem)
 {
   var answer = prompt("Please enter your door password to unlock", "");
   responsiveVoice.speak("Please enter your door password to unlock");
   if (answer != "door") {
   	alert("Password incorrect");
	if(elem.checked)
		elem.checked = false;
	else
		elem.checked = true;
	return false;
   }
   if(elem.checked || elem.selected) {
     var val = 1;
	 responsiveVoice.speak("Your "+accname+" is now "+val1);
   }
   else {
     var val = 0;
	 responsiveVoice.speak("Your "+accname+" is now "+val2);
   }
   setAccessory(pin, val);
 }
</script>
</head><body onload='responsiveVoice.speak("Hello Jc! Welcome to your home! Together we can rule your... home!");' class="is-loading">

<!-- Wrapper -->
<div id="wrapper"> 
  
  <!-- Main -->
  <section id="main">
    <header> <span class="avatar"><img src="images/homekit.png" alt="" /></span>
      <h1>Welcome to your Home!</h1>
      <p>JcBriones HomeKit</p>
    </header>
    <h2>Weather</h2>
    <div id="weather">
      <iframe id="forecast_embed" type="text/html" frameborder="0" height="245" width="100%" src="http://forecast.io/embed/#lat=38.8602415&lon=-77.2150139&name=Briones'%20Home&color=#3385ff"></iframe>
    </div>
    <hr>
    <h1>Your HomeKit Status</h1>
	<div id="data">
    </div>
    <hr>
    <div class="field">
      <label>HomeKit PIN</label>
      <code class="pin">101-71-990</code>
      <p>Scan using your iPhone</p>
    </div>
    <hr>
    <form method="post" action="<?php print htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <p>
        <input type="hidden" name="logout" value="1" />
        <input type="submit" value="Logout" />
      </p>
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