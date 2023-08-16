<?php
/*
comments to complete
*/

$pathbdd = './../_local-connect/connect.php';
include($pathbdd );
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	



/* creates array with rating names */

?>

<form action="#" onsubmit="intercept(event)">
<label for="namestart"><?= $str['enter_start_name'] ?></label>
	<input type="text" autocomplete="off" name="identifier" id="namestart" required>
</form>
<div class="E4M_bigbutton">
	<button id="searchButton"><?= $str['search'] ?></button>
</div>
<br/><br/>
<div id="E4M_members_table" class="E4M_hoverable_list"></div>

</div>

<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	var request = new XMLHttpRequest();
	let SearchButton = document.getElementById('searchButton');
	SearchButton.addEventListener('click', trouve);
	// let EventsTableSettings = {
	// 	"headArray" : [str["date_label"], str["event_label"]],
	// 	"activeHeader" :"",
	// 	"colData" : ["datestart_typed", "name"],
	// 	"active" : false,
	// 	"colSorted" : -1
	// };
	// /* let's add .rowLink to allow click on a row */
	// event_list.forEach((element) =>{
	// 	let dest = "<?=$cfg['event_page']?>?id=" + element.id.toString(10);
	// 	element.rowLink=dest;
	// 	let truedate = new Date(element.datestart);
    //     element.datestart_typed = truedate;
	// });
	// var EventsTable = new smartTable (
	// 	"event_list", 
	// 	event_list,
	// 	EventsTableSettings
	// );
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
			// var members = this.response;
			
			let e = document.getElementById('E4M_members_table');
			members = this.response;
			let tch = MembersObjToTable(members);
			if(members.length==0){
				Instruction.innerHTML=str["No_result"];
			} else {
				if(members.length==25){
					Instruction.innerHTML=str["Too_many_answers"];
				} else {
					Instruction.innerHTML=str["Select_in_list"];
				}
			}
			e.innerHTML = tch;
		}
	}
	function intercept(e){
		/* form namestart : don't reload page on ENTER + click on search button*/
		e.preventDefault();
		document.getElementById("searchButton").click();
		console.log("je cherche ...");
	}
</script>