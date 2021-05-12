<?php
/*
page to be included in a php page (register-search.php or any name chosen by admin - see config.json)
input : subevent id | eg event.php?sub=12
recovers data about subevents of selected events through $_SESSION['subs_data_set']
*/

/* lets get strings from json folder (strings displayed and configuration strings) */
// debug --> enlever ce qui est inutile
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_check_page = json_encode($cfg['registration_check_page']); // debug --> à garder
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	
$subs_data_set_str = $_SESSION['subs_data_set'];
var_dump($subs_data_set_str);

/* this page is supposed to be called with event id, let's set it to 1 if omitted */
if(isset($_GET['sub']) && isset($_SESSION['subs_data_set'])){ 
	//$subevent_id=$_GET['sub_json'];
	$subevent_id = $_GET['sub'];
	var_dump($subevent_id);
} else {
	echo "this page can only be called from event description page";
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
	var registration_check_page = `<?= $cfg['registration_check_page'] ?>`;
	console.log("registration_check_page = ", registration_check_page)
	var request = new XMLHttpRequest();
	var subs_data_set= JSON.parse(`<?=$subs_data_set_str?>`);
	var subevent_id = `<?=$subevent_id?>`;
	console.log("subs_data_set = ", subs_data_set);
	// var subevent_set = JSON.parse(`<!=$subevent_set_str?>`); // ca marche sauf si restrictions catégories (ou autres ??...)
	console.log("subevent_id = ", subevent_id);
	//document.write(subs_data_set[subevent_id]['name']);
	function trouve(){
		/*
		Gets the string in the field 'namestart' of the form, pass it to API that returns the list of members
		who's name starts with this string. 
		Build the table with members that match the start
		*/
		var myForm = document.getElementById('myForm');
		formData = new FormData(myForm);
		var start = document.getElementById('namestart').value;
		var XHR = './API/get-memberlist-by-namestart.php?start=' + start;
		request.open('GET', XHR);
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
