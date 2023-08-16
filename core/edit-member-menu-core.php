<?php
/*
comments to complete
*/

$pathbdd = './../_local-connect/connect.php';
include($pathbdd );
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	
$jsonstr = json_encode($str);


/* creates array with rating names */

?>
<h1><?= $str['create_edit_member'] ?></h1>
<form action="#" onsubmit="intercept(event)">
<label for="namestart"><?= $str['enter_start_name'] ?></label>
	<input type="text" autocomplete="off" name="identifier" id="namestart" required>
</form>
<div class="E4M_bigbutton">
	<button id="searchButton"><?= $str['search'] ?></button>
</div>
<br/>
<a href='./edit-member.php'><button ><?= $str['new_member'] ?></button></a>
<div id="E4M_members_table" class="E4M_hoverable_list"></div>
<table id="member_list" class="E4M_regtable E4M_hoverable_list"></table>
</div>

<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	let str = JSON.parse(`<?=$jsonstr?>`);
	var request = new XMLHttpRequest();
	let SearchButton = document.getElementById('searchButton');
	SearchButton.addEventListener('click', trouve);

	function trouve(){
		/*
		Gets the string in the field 'namestart' of the form, pass it to API that returns the list of members
		who's name starts with this string. 
		Build the table with members that match the start, and displays it with SmartTable
		*/
		
		let start = document.getElementById('namestart').value;
		var XHR = './API/get-memberlist-by-namestart.php?start=' + start + "&ratn=1"; // API needs a rating...
		request.open('GET', XHR);
		request.responseType = 'json';
		request.send();
	}
	request.onreadystatechange  = function() {
		if (this.readyState == 4 && this.status == 200) {
			var members = this.response;
			let e = document.getElementById('E4M_members_table');
			
			console.log(members)
			let MembersTableSettings = {
				"headArray" : [str["fede_id"], str["firstname"], str["lastname"]],
				"activeHeader" :"acth",
				"colData" : ["fede_id", "firstname", "lastname"],
				"active" : false,
				"colSorted" : -1
			};
			/* let's add .rowLink to allow click on a row */
			
			members.forEach((element) =>{
				let dest = "./edit-member.php?id=" + element.id.toString(10);
				element.rowLink=dest;
			});
			var MembersTable = new smartTable (
				"member_list", 
				members,
				MembersTableSettings
			);
			
		}
	}
	function intercept(e){
		/* form namestart : don't reload page on ENTER + click on search button*/
		e.preventDefault();
		document.getElementById("searchButton").click();
		console.log("je cherche ...");
	}
</script>