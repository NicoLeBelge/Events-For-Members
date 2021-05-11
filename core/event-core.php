<?php
/*
page to be included in a php page (event.php or any name chosen by admin)
input : event id | eg event.php?id=12
javascript functions are at the bottom of the page, not externally located, to allow custom strings ($str) insertion
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members

note : subevent data are passed to REGISTER page via POST of hidden field in form - might be made in a smarter way...
*/
/* lets get strings from json folder (strings displayed and configuration strings) */
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_search_page = json_encode($cfg['registration_search_page']); 
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	

/* this page is supposed to be called with subevent id, let's warn the visitor if omitted */
if(isset($_GET['id'])){ 
	$eventid=$_GET['id'];
} else {
	echo "this page needs parameter";
}
?>
<div class='E4M_maindiv'>
	<div id="E4M_eventinfo" ></div>
	</br>
	
	<div id="E4M_select_event"></div>
	
	<div id="E4M_subeventinfo" class="E4M_subeventinfo"></div>
	<?php if (ISSET($_SESSION['user_id'])): ?>
		<button onclick="download()"><?=$str['Download']?></button>	
	<?php endif; ?>

	<form action="<?=$cfg['registration_search_page']?>" method="post">
	<input id="sub_json" type="text" value="pipo" name="sub_json">
	<button type="submit" ><?=$str['Register']?></button>
	</form >
	<div id="E4M_regtable" class="E4M_regtable"></div>
	
</div>
<script src="./JS/E4M.js"></script>

<script type="text/javascript">
	
	/* let's build arrays 'rating_names' etc. from the JSON passed by php */
	var rating_names = new Array();
	rating_names = JSON.parse(`<?=$rating_names_str?>`);
	
	var cat_names = new Array();
	cat_names = JSON.parse(`<?=$cat_names_str?>`);
	
	var gender_names = new Array();
	
	let gender_names_str = '<?=$gender_names_str?>'; // debug
	console.log("et pourtant il devrait y avoir des guillemets, ici...");
	console.log(gender_names_str);

	gender_names = JSON.parse('<?=$gender_names_str?>');
	
	var str = JSON.parse(`<?=$jsonstr?>`);
	var subevent_link_icon = JSON.parse(`<?=$subevent_link_icon_str?>`);
	
	/* let's declare global variables used by external JS */
	var registration_search_page = `<?= $cfg['registration_search_page'] ?>`;
	var CurrentSubEvent = 0; 	// index of the internal table from json
	var CurrentSubEventId = 0; 	// id in the database
	var CurrentRating = 1; 	// default value, will later depend on rating in subevent
	var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
	var CurrentSubEventObj; // {Array for current subevent}
	var subevent_list; // Array() of subevent_info_sets
	var NbSubs; // Number of SubEvents
	var NbRegTot; // Total Number of registred members
	var CurrentNbmax ; // max subscriptions for current subevent
	var subevent_list_str; // will contain JSON of subevents data
	var hidden_json = document.getElementById('sub_json'); // hidden field to pass subevents data to next page
	
	/* those 3 html elements will be updated each time the user selects a subevents*/
	var event_html_id = document.getElementById('E4M_eventinfo');
	var subevent_html_id = document.getElementById('E4M_subeventinfo');
	var registred_html_id = document.getElementById('E4M_regtable');

	var rq_event = new XMLHttpRequest();
	rq_event.open('GET', './API/get-event-info.php?event=<?=$eventid?>'); // bug wrong subevent selected
	rq_event.responseType = 'json';
	rq_event.send();

rq_event.onreadystatechange  = function() {
	/* 
	When XHR ready, 3 events information are displayed, and subevent selector is built.
	*/
	if (this.readyState == 4 && this.status == 200) {
		let event_data_set = this.response;
		// ici
		
		let event_data_set_str = JSON.stringify(event_data_set);
		
		subevent_list_str =JSON.stringify(event_data_set['subs']);
		// note : we can then access subevent data (eg cat) or order n (eg 0)  with 
		// let new_subevent_list = JSON.parse(subevent_list_str);
		// cat_list = new_subevent_list[0]['cat']

		eventinfoset =event_data_set['infos'][0];
		subevent_list = event_data_set['subs'];
		
		CurrentSubEventId = subevent_list[0]["id"];
		CurrentSubEventObj = subevent_list[0];
		console.log(CurrentSubEventObj);
		CurrentNbmax = subevent_list[0]["nbmax"];
		member_list =  event_data_set['registrations'];
		NbRegTot = member_list.length;

		hidden_json.value = JSON.stringify(subevent_list[0]);
	
		event_html_id.innerHTML = eventInfos2html(eventinfoset);
		subevent_html_id.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
		registred_html_id.innerHTML = RegList2htmltable (member_list, CurrentSubEvent);
		NbSubs=subevent_list.length;
		if (NbSubs > 1){
			BuildHTMLEventSelector (NbSubs);
		}
	}
}
</script>
