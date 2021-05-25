<?php
/*
page to be included in a php page (register-search.php or any name chosen by admin - see config.json)
input : subevent id | eg event.php?sub=12
recovers data about subevents of selected events through $_SESSION['subs_data_set']
The page allows the visitor to find a member by typing the beginning of the name and pick him from the list.
If the selected member matches the restrictions of the current subevent, the visitor can confirm the registration. 
He is then redirected to event page, where he can see the name added in the participants.
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
$type_names_str = json_encode($cfg['type_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	
$subs_data_set_str = $_SESSION['subs_data_set'];

/* this page must be called with event id, let's set it to 1 if omitted */
if(isset($_POST['E4M_hidden_id']) && isset($_SESSION['subs_data_set'])){ 
	//$subevent_id=$_GET['sub_json'];
	$subevent_index = $_POST['E4M_hidden_id'];
} else {
	echo "this page can only be called from event description page";
}
?>
<div class='E4M_maindiv'>
<div id="E4M_subeventinfo" class="E4M_subeventinfo"></div>

<form action="<?= $cfg['registration_check_page'] ?>" id='ValidationForm' method="POST" >
	<label for="member_name"><?= $str['Member'] ?></label>
	<input type="text" autocomplete="off" name="member_name" id="member_name" readonly required>
	<?php if ($_SESSION["secured"]=="1"): ?>
		<label for="member_email"><?= $str['email'] ?></label>
		<input type="email" autocomplete="on" name="member_email" id="member_email" placeholder="<?=$str["email_required"]?>"required>
	<?php endif; ?>
	<input type="hidden" autocomplete="off" name="member_id" id="member_id" required>
	<input type="hidden" autocomplete="off" name="sub_id" id="sub_id" required>
	<button type="submit" id="register_btn" disabled><?= $str["Register_confirm"] ?></button>
</form> 
<br/><br/>
<form>
	<label for="namestart"><?= $str['enter_start_name'] ?></label>
	<input type="text" autocomplete="off" name="identifier" id="namestart" required>
</form> 
<button onclick = trouve() ><?= $str['search'] ?></button>
<br/><br/>
<div id="E4M_members_table" class="E4M_hoverable_list"></div>
</div>


<script src="./JS/E4M-search.js"></script>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">
	var rating_names = JSON.parse(`<?=$rating_names_str?>`);
	var cat_names = JSON.parse(`<?=$cat_names_str?>`);
	var gender_names = JSON.parse('<?=$gender_names_str?>');
	var type_names = JSON.parse('<?=$type_names_str?>');
	var registration_check_page = `<?= $cfg['registration_check_page'] ?>`;
	var str = JSON.parse(`<?=$jsonstr?>`);
	var request = new XMLHttpRequest();
	var subs_data_set= JSON.parse(`<?=$subs_data_set_str?>`);
	let subevent_index_str = `<?=$subevent_index?>`;
	var subevent_index = parseInt(subevent_index_str,10);
	var currentSubEventObj = subs_data_set[subevent_index];
	var currentSubEventId = currentSubEventObj.id;
	console.log("currentSubEventId = ",currentSubEventId);
	var subevent_link_icon = JSON.parse(`<?=$subevent_link_icon_str?>`);
	var rating_t = currentSubEventObj.rating_type;

	var members; // list of members matching search
	var member; // member picked in list of member
	document.getElementById('member_name').placeholder=str["Register_instruction"];
	let searchInput = document.getElementById('namestart');
	searchInput.placeholder=str["Search_instruction"];
	searchInput.focus();

	let subevent_html_id = document.getElementById('E4M_subeventinfo');
	subevent_html_id.innerHTML = SubeventInfos2html (currentSubEventObj);
	/*"Search_instruction":"Entrez les premières lettres du nom recherché",
    "Pick_instruction":"Sélectionnez le nom dans la liste", ici
	*/
	function trouve(){
		/*
		Gets the string in the field 'namestart' of the form, pass it to API that returns the list of members
		who's name starts with this string. 
		Build the table with members that match the start
		*/
		
		let start = document.getElementById('namestart').value;
		var XHR = './API/get-memberlist-by-namestart.php?start=' + start + "&ratn=" + rating_t;
		request.open('GET', XHR);
		request.responseType = 'json';
		request.send();
	}
	request.onreadystatechange  = function() {
		if (this.readyState == 4 && this.status == 200) {
			// var members = this.response;
			let e = document.getElementById('E4M_members_table');
			members = this.response;
			let tch = MembersObjToTable(members);
			e.innerHTML = tch;
		}
	}
	
</script>
