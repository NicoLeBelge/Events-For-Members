<?php
/*
page to be included in a php page (event.php or any name chosen by admin)
input : event id | eg event.php?id=12
javascript functions are at the bottom of the page, not externally located, to allow custom strings ($str) insertion
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members

note : subevent data are passed to REGISTER page via POST of hidden field in form (debug) or session variables  - might be made in a smarter way...
*/
/* lets get strings from json folder (strings displayed and configuration strings) */

$cfg = json_decode(file_get_contents('./json/config.json'),true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_search_page = json_encode($cfg['registration_search_page']); 
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);

$str = json_decode(file_get_contents('./json/strings.json'),true);	
$jsonstr = json_encode($str);	

/* this page is supposed to be called with subevent id, let's warn the visitor if omitted */
include('../_local-connect/connect.php'); // PDO connection required
if(isset($_GET['id'])){ 
	$event_id = $_GET['id'];
	$event_set = array();
	
	$reponse = $conn->query("SELECT * from events where id=$event_id");
	$event_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	
	$reponse = $conn->query("SELECT * from subevents where event_id=$event_id");
	$event_set["subs"] = $reponse->fetchAll(PDO::FETCH_ASSOC);

	//var_dump($event_set["subs"]);
	$subs_data_jsonstr = json_encode($event_set["subs"], JSON_UNESCAPED_UNICODE);
	$_SESSION['subs_data_set']=$subs_data_jsonstr;
	$ratinglist="";
	for ($k=1;$k<=$cfg['Nb_rating'];$k++){
		$ratinglist .= "members.rating$k, ";
	}
	$qtxt = "SELECT subevents.event_id as eventid,
					subevents.id as subid,
					registrations.member_id as memberid,
					registrations.confirmed,
					members.fede_id,
					members.lastname,
					members.firstname,
					members.firstname,
					$ratinglist
					clubs.name as clubname,
					clubs.region as region
	FROM registrations
	INNER JOIN subevents
	ON registrations.subevent_id = subevents.id	
	INNER JOIN members
	ON registrations.member_id = members.id	
	INNER JOIN events
	ON events.id = subevents.event_id
	INNER JOIN clubs
	ON members.club_id = clubs.club_id
	WHERE subevents.event_id = $event_id";
	$reponse = $conn->query($qtxt);
	$event_set["registrations"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	$event_set_jsonstr = json_encode($event_set);
	
	
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

	<form name="E4M_destination" action="<?=$cfg['registration_search_page']?>" method="get">
	
	
	<button type="submit" ><?=$str['Register']?></button>
	</form >
	<div id="E4M_regtable" class="E4M_regtable"></div>
	
</div>
<script src="./JS/E4M.js"></script>

<script type="text/javascript">
	
	/* let's declare global variables used by external JS */
	
	var rating_names = JSON.parse(`<?=$rating_names_str?>`);
	var cat_names = JSON.parse(`<?=$cat_names_str?>`);
	var gender_names = JSON.parse('<?=$gender_names_str?>');
	var str = JSON.parse(`<?=$jsonstr?>`);
	var subevent_link_icon = JSON.parse(`<?=$subevent_link_icon_str?>`);
	
	
	var subs_data_set = JSON.parse(`<?=$subs_data_jsonstr?>`);
	console.log(subs_data_set);
	//subevent_list_str =JSON.stringify(event_data_set['subs']);
	
	// note : we can then access subevent data (eg cat) or order n (eg 0)  with 
	// let new_subevent_list = JSON.parse(subevent_list_str);
	// cat_list = new_subevent_list[0]['cat']

	var event_data_set = JSON.parse(`<?=$event_set_jsonstr?>`);
	console.log(event_data_set);
	
	var registration_search_page = `<?= $cfg['registration_search_page'] ?>`;
	
	var CurrentSubEvent = 0; 	// index of the internal table from json
	var CurrentSubEventId = 0; 	// id in the database
	var CurrentRating = 1; 	// default value, will later depend on rating in subevent
	var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
	var CurrentSubEventObj; // {Array for current subevent}
	var subevent_list; // Array() of subevent_info_sets
	var NbSubs; // Number of SubEvents in current events
	var NbRegTot; // Total Number of registred members
	var CurrentNbmax ; // max subscriptions for current subevent
	var subevent_list_str; // will contain JSON of subevents data
	//var hidden_json = document.getElementById('sub_json'); // hidden field to pass subevents data to next page // debug à garder ??
	
	/* those 3 html elements will be updated each time the user selects a subevents*/
	var event_html_id = document.getElementById('E4M_eventinfo');
	var subevent_html_id = document.getElementById('E4M_subeventinfo');
	var registred_html_id = document.getElementById('E4M_regtable');
	var form_destination = document.E4M_destination.action;
	

	var rq_event = new XMLHttpRequest();
	rq_event.open('GET', './API/get-event-info.php?event=<?=$eventid?>'); // bug wrong subevent selected
	rq_event.responseType = 'json';
	rq_event.send();
	

	eventinfoset =event_data_set['infos'][CurrentSubEvent];
	//subevent_list = event_data_set['subs'];
	
	//CurrentSubEventId = subevent_list[0]["id"];
	CurrentSubEventId = subs_data_set[CurrentSubEvent]["id"];
	
	
	//CurrentSubEventObj = subevent_list[0]; subs_data_set
	CurrentSubEventObj = subs_data_set[CurrentSubEvent]; 
	
	//CurrentNbmax = subevent_list[0]["nbmax"];
	CurrentNbmax = CurrentSubEventObj["nbmax"]
	console.log("CurrentNbmax = ", CurrentNbmax)
	member_list =  event_data_set['registrations'];
	NbRegTot = member_list.length;

	// hidden_json.value = JSON.stringify(subevent_list[0]);

	event_html_id.innerHTML = eventInfos2html(eventinfoset);
	/*
	 
	console.log("CurrentSubEvent");
	console.log(CurrentSubEvent);
	console.log("subs_data_set[CurrentSubEvent]");
	console.log(subs_data_set[CurrentSubEvent]);
	*/
	subevent_html_id.innerHTML = SubeventInfos2html(subs_data_set[CurrentSubEvent]);
	registred_html_id.innerHTML = RegList2htmltable (member_list, CurrentSubEvent);
	let destination = `<?=$cfg['registration_search_page']?>`+ "?sub=" + CurrentSubEvent;
	console.log("form_destination before modification = ",document.E4M_destination.action);
	console.log("destination = ",destination);
	
	document.E4M_destination.action = destination;
	console.log("form_destination after modification = ",document.E4M_destination.action)
	//form_destination = destination;
	NbSubs=subs_data_set.length;
	
	if (NbSubs > 1){
		BuildHTMLEventSelector (NbSubs);
	}
	

</script>
