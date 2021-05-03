<?php
/*
page to be included in a php page (event.php or any name chosen by admin)
input : event id | eg event.php?id=12
javascript functions are at the bottom of the page, not externally located, to allow custom strings ($str) insertion
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members
*/
/* lets get strings from json folder (strings displayed and configuration strings) */
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	

/* this page is supposed to be called with event id, let's set it to 1 if omitted */
if(isset($_GET['id'])){ 
	$eventid=$_GET['id'];
} else {
	$eventid=1;
}
?>
<div class='E4M_maindiv'>
<form id='myForm'>
		<label for="namestart"><?= $str['enter_start_name'] ?></label>
		<input type="text" name="identifier" id="namestart" required>
	</form> 
	<button onclick = trouve() ><?= $str['search'] ?></button>
	<br/><br/>
	<div id="playertable"></div>

	
</div>

<script type="text/javascript">
	var request = new XMLHttpRequest();
	function trouve(){
		/*
		Gets the string in the field 'namestart' of the form, pass it to API that returns the list of members
		who's name starts with this string. 
		Build the table with members that match the start
		*/
		var myForm = document.getElementById('myForm');
		formData = new FormData(myForm);
		var start = document.getElementById('namestart').value;
		var requestURL = './API/get-memberlist-by-namestart.php?start=' + start;
		console.log(requestURL);
		request.open('GET', requestURL);
		request.responseType = 'json';
		request.send();
	}
	request.onreadystatechange  = function() {
		if (this.readyState == 4 && this.status == 200) {
			var players = this.response;
			console.log(players);
			//var tch = PlayersObjToTable(players);
			var tch = "pipo";
			var e = document.getElementById('playertable');
			e.innerHTML = tch;
		}
	}
</script>
