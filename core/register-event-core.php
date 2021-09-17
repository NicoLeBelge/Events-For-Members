<?php
/*
page to be included in a php page (register-event.php or any name chosen by admin)
input : event id | eg register-event.php?id=12
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members
note : 	event data are passed to REGISTER page via session variables
		subevent is selected by user --> must be passed to next page via hidden field
*/

/* lets get strings from json folder (strings displayed and configuration strings) */

$cfg = json_decode(file_get_contents('./json/config.json'),true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_search_page = json_encode($cfg['registration_search_page']); 
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$type_names_str = json_encode($cfg['type_names']);

$str = json_decode(file_get_contents('./json/strings.json'),true);	
$jsonstr = json_encode($str);

/* this page is supposed to be called with event id, let's warn the visitor if omitted */
include('../_local-connect/connect.php'); // PDO connection required
if(isset($_GET['id'])){ 
	$event_id = $_GET['id'];
	$event_set = array();
	
	$reponse = $conn->query("SELECT * from events where id=$event_id");
	$event_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	if (!isset($_SESSION["user_id"])){
		$is_owner = false;
	} else {
		$is_owner = ($_SESSION["user_id"] === $event_set["infos"][0]["owner"]) ? true : false;
	}
	
	$_SESSION["secured"]=$event_set["infos"][0]["secured"]; // used on seach page to display e-mail input in form
	$reponse = $conn->query("SELECT * from subevents where event_id=$event_id");
	$event_set["subs"] = $reponse->fetchAll(PDO::FETCH_ASSOC);

	
	$subs_data_jsonstr = json_encode($event_set["subs"], JSON_UNESCAPED_UNICODE);
	$_SESSION['subs_data_set']=$subs_data_jsonstr;
	
	$ratinglist="";
	for ($k=1;$k<=$cfg['Nb_rating'];$k++){
		$ratinglist .= "members.rating$k, ";
	}
	$qtxt = "SELECT subevents.event_id as eventid,
					subevents.id as subid,
					registrations.id as regid,
					registrations.member_id as memberid,
					registrations.datereg as datereg,
					registrations.confirmed,
					registrations.wait,
					members.fede_id,
					members.lastname,
					members.firstname,
					members.member_grade,
					members.cat,
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
	WHERE subevents.event_id = $event_id
	ORDER BY datereg ASC";
	$reponse = $conn->query($qtxt);
	
	$event_set["registrations"] = $reponse->fetchAll(PDO::FETCH_ASSOC);

	$event_set_jsonstr = json_encode($event_set);
} else {
	echo "this page needs parameter";
}
?>
	<div class='E4M_maindiv'>
	<a href = "<?=$cfg['event_list_page']?>"><button> ⬆ <?=$str['Goto_all_events']?> ⬆ </button> </a>
	<div id="E4M_eventinfo" ></div>
	
	<?php if (ISSET($_SESSION['user_id'])): ?>
		<form action=<?= $cfg["event_modification_page"] ?> method="GET" >
		<button type="submit" name="id" value=<?=$_GET['id']?> ><?=$str['Modify']?></button>
		<br/>
	</form >
	<?php endif; ?>
	<div id="E4M_sub_data" ></div>
	
	<div id="sub_selector" class="E4M_buttonset"></div>
	<div id="E4M_subeventinfo" ></div>
	<div id="E4M_subevent_cat" class="E4M_catlist" ></div>
	<div id="E4M_subevent_gen" class="E4M_catlist" ></div>
	<div id="E4M_subevent_typ" class="E4M_catlist" ></div>
	
	<?php if (ISSET($_SESSION['user_id'])): ?>
		<br/>
		<button onclick="download()"><?=$str['Download']?></button>	
	</form >
	<?php endif; ?>

	<form action="<?=$cfg['registration_search_page']?>" method="POST">
		<input id="E4M_hidden_id" name="E4M_hidden_index" type=hidden value=0>
		<button type="submit" ><?=$str['Register']?></button>
	</form >
	
	<div id="E4M_regtable" class="E4M_regtable"></div>	
</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	var hidden_id= document.getElementById('E4M_hidden_id');
	
	/* let's declare global variables used by external JS */
	
	var rating_names = JSON.parse(`<?=$rating_names_str?>`);
	var cat_names = JSON.parse(`<?=$cat_names_str?>`);
	var gender_names = JSON.parse('<?=$gender_names_str?>');
	var type_names = JSON.parse('<?=$type_names_str?>');
	var str = JSON.parse(`<?=$jsonstr?>`);
	var subevent_link_icon = JSON.parse(`<?=$subevent_link_icon_str?>`);
	
	
	var subs_data_set = JSON.parse(`<?=$subs_data_jsonstr?>`);
	console.log(subs_data_set);

	var event_data_set = JSON.parse(`<?=$event_set_jsonstr?>`);
	
	
	var registration_search_page = `<?= $cfg['registration_search_page'] ?>`;
	
	var CurrentSubEventIndex = 0; 	// index of the internal table from json (0, 1,...)
	var CurrentSubEventId = 0; 	// id in the database
	var CurrentRating = 1; 	// default value, will later depend on rating in subevent
	var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
	var CurrentSubEventObj; // {Array for current subevent}
	// var subevent_list; // Array() of subevent_info_sets // debug ça sert à rien ?!!
	var NbSubs; // Number of SubEvents in current events
	var NbRegTot; // Total Number of registred members
	var CurrentNbmax ; // max subscriptions for current subevent
	var subevent_list_str; // will contain JSON of subevents data // debug ça sert à rien ?!!
	var is_owner = false;
	let is_owner_php = `<?= $is_owner ?>`;
	let newsubURL = "";
	if (is_owner_php == "1") { // is_owner = user connected is the owner of current event
		is_owner=true;
	};
	if (is_owner){
		newsubURL = "edit-autocreate-subevent.php?event_id=<?=$event_id ?>"
	}

	//console.log("subs_data_set = ", subevent_list)
	let nbSubevents = subs_data_set.length;
	
	console.log("core / subs_data_set.length = ", subs_data_set.length)
	console.log("nbSubevents = ", nbSubevents)
	if (is_owner || nbSubevents>1) {
		var subSelector = new Selector (
			"sub_selector",
			nbSubevents,
			CurrentSubEventIndex,
			SelectEvent,
			newsubURL
		);
	}
	var cat_set = new IconSet (
		"E4M_subevent_cat", 
		cat_names,
		subs_data_set[CurrentSubEventIndex].cat,
		"E4M_cat",
		false
	);
	var gen_set = new IconSet (
		"E4M_subevent_gen", 
		gender_names,
		subs_data_set[CurrentSubEventIndex].gender,
		"E4M_gen",
		false
	);	
	var typ_set = new IconSet (
		"E4M_subevent_typ", 
		type_names,
		subs_data_set[CurrentSubEventIndex].type,
		"E4M_typ",
		false
	);	
	/* those 3 html elements will be updated each time the user selects a subevents*/
	var event_html_id = document.getElementById('E4M_eventinfo');
	var subevent_html_id = document.getElementById('E4M_subeventinfo'); // icon sets added separately from Aug 30th 2021
	var registred_html_id = document.getElementById('E4M_regtable');
	
	var sort_method = "default"; // default (datereg) | name | rating | club | cat 
	
	

	eventinfoset =event_data_set['infos'][0]; 
	
	CurrentSubEventId = subs_data_set[CurrentSubEventIndex]["id"]; 
	
	hidden_id.value = CurrentSubEventIndex;
	CurrentSubEventObj = subs_data_set[CurrentSubEventIndex]; 
	CurrentNbmax = CurrentSubEventObj["nbmax"];
	
	member_list =  event_data_set['registrations'];
	NbRegTot = member_list.length;

	event_html_id.innerHTML = eventInfos2html(eventinfoset);
	subevent_html_id.innerHTML = SubeventInfos2html(subs_data_set[CurrentSubEventIndex]);
	registred_html_id.innerHTML = RegList2htmltable (member_list, CurrentSubEventIndex);
	
	NbSubs=subs_data_set.length;
	if (NbSubs > 1){
		BuildHTMLEventSelector (NbSubs);
	}
</script>
