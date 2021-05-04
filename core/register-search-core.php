<?php
/*
page to be included in a php page (register-search.php or any name chosen by admin - see config.json)
input : subevent id | eg event.php?sub=12
*/

/* lets get strings from json folder (strings displayed and configuration strings) */
// debug --> enlever ce qui est inutile
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_page = json_encode($cfg['registration_page']); // debug --> à garder
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	

/* this page is supposed to be called with event id, let's set it to 1 if omitted */
if(isset($_GET['sub'])){ 
	$subeventid=$_GET['sub'];
} else {
	echo "this page is not supposed to be called without subenvent identifier";
}
?>
<div class='E4M_maindiv'>
<form id='myForm'>
		<label for="namestart"><?= $str['enter_start_name'] ?></label>
		<input type="text" autocomplete="off" name="identifier" id="namestart" required>
	</form> 
	<button onclick = trouve() ><?= $str['search'] ?></button>
	<br/><br/>
	<div id="members_table"></div>

	
</div>
<script src="./JS/E4M-search.js"></script>
<script type="text/javascript">
	var registration_page = `<?= $cfg['registration_page'] ?>`;
	console.log ("regisration_page");
	console.log (registration_page);
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
			var members = this.response;
			var tch = MembersObjToTable(members);
			var e = document.getElementById('members_table');
			e.innerHTML = tch;
		}
	}
</script>
