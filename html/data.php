<?php require_once("login.php"); ?>
<?php
function getStatus($pin) {
	$exec = shell_exec('/var/db/DB '.$pin);
	return $exec;
}
function announce($device, $switch) {
	switch(rand(1,5))
	{
		default:
			$message = "I am now turning to $switch your $device";
			break;
		case 1:
			$message = "Ok. Just a second... Turning $switch your $device";
			break;
		case 2:
			$message = "Your $device is now $switch";
			break;
		case 3:
			$message = "Indeed. Your $device is now $switch";
			break;
		case 4:
			$message = "I am now switching to $switch your $device";
			break;
		case 5:
			$message = "Done! $device is now $switch";
			break;
	}
	return $message;
}
?>
    <h2>Living Room</h2>
    <div class="field">
      <label>Living Room Lamp</label>
      <input id="ctl" type="checkbox" onclick='setAccessoryElem("Living Room Lamp","on","off",1,this)' <?php if(getStatus(1) == "1") echo "checked"; ?> />
      <label for="ctl"></label>
    </div>
    <div class="field">
      <label>Living Room Light</label>
      <input id="lrl" type="checkbox" onclick='setAccessoryElem("Living Room Light","on","off",5,this)' <?php if(getStatus(5) == "1") echo "checked"; ?> />
      <label for="lrl"></label>
    </div>
    <div class="field">
      <label>Living Room TV Light</label>
      <input id="lrtvl" type="checkbox" onclick='setAccessoryElem("Living Room TV Light","on","off",6,this)' <?php if(getStatus(6) == "1") echo "checked"; ?> />
      <label for="lrtvl"></label>
    </div>
    <br>
    <h2>Dining Room</h2>
    <div class="field">
      <label>Dining Room Light</label>
      <input id="drl" type="checkbox" onclick='setAccessoryElem("Dining Room Light","on","off",3,this)' <?php if(getStatus(3) == "1") echo "checked"; ?> />
      <label for="drl"></label>
    </div>
    <br>
    <h2>Kitchen</h2>
    <div class="field">
      <label>Kitchen Light</label>
      <input id="krl" type="checkbox" onclick='setAccessoryElem("Kitchen Light","on","off",4,this)' <?php if(getStatus(4) == "1") echo "checked"; ?> />
      <label for="krl"></label>
    </div>
    <br>
    <h2>My Bedroom</h2>
    <div class="field">
      <label>Ceiling Light</label>
      <input id="mbl" type="checkbox" onclick='setAccessoryElem("Bedroom Ceiling Light","on","off",2,this)' <?php if(getStatus(2) == "1") echo "checked"; ?> />
      <label for="mbl"></label>
    </div>
    <br>
    <h2>Main</h2>
    <div class="field">
      <label>Front Door Lock</label>
      <input id="fdl" type="checkbox" onclick='setAccessoryElemWithPass("Front Door Lock","locked","unlocked","fdl",this)' <?php if(getStatus("fdl") == "1") echo "checked"; ?> />
      <label for="fdl"></label>
    </div>
    <div class="field">
      <label>Home Security</label>
        <div class="button-group">
            <a onclick='responsiveVoice.speak("<?php echo announce("home security","home"); ?>"); setAccessory("hs",0); $("#data").load("data.php");' class="button<?php if(getStatus("hs") == "0") echo " primary"; ?>">Home</a>
            <a onclick='responsiveVoice.speak("<?php echo announce("home security","away"); ?>"); setAccessory("hs",1); $("#data").load("data.php");' class="button<?php if(getStatus("hs") == "1") echo " primary"; ?>">Away</a>
            <a onclick='responsiveVoice.speak("<?php echo announce("home security","night"); ?>"); setAccessory("hs",2); $("#data").load("data.php");' class="button<?php if(getStatus("hs") == "2") echo " primary"; ?>">Night</a>
            <a onclick='responsiveVoice.speak("<?php echo announce("home security","disarm"); ?>"); setAccessory("hs",3); $("#data").load("data.php");' class="button<?php if(getStatus("hs") == "3") echo " primary"; ?>">Disarm</a>
        </div>
    </div>
    <!--<div class="field" id="camera">
      <label>Front Door Camera</label>
    	<img src="http://home.jcbriones.com:81/image.jpg?cidx=351500271" width="427" height="320">
    </div>-->
