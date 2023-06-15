<?php
/*
page to be included in a php page (register-event.php or any name chosen by admin)
input : event id | eg register-event.php?id=12
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members
note : 	event data are passed to REGISTER page via session variables
		subevent is selected by user --> must be passed to next page via property (SubIndex) of REGISTER button
*/

/* lets get strings from json folder (strings displayed and configuration strings) */

$cfg = json_decode(file_get_contents('./_json/config.json'),true);	
$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_search_page = json_encode($cfg['registration_search_page']); 
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$type_names_str = json_encode($cfg['type_names']);

$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$jsonstr = json_encode($str);

/* this page is supposed to be called with event id, let's warn the visitor if omitted */
include('../_local-connect/connect.php'); // PDO connection required
if(isset($_GET['id']))
{ 
	$event_id = $_GET['id'];
	$event_set = array();
	
	$reponse = $conn->query("SELECT * from events where id=$event_id");
	
	
	$event_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC); // improvement expected fetchAll --> fetch (only one record in reponse)
	
	// echo "<pre>";
	// var_dump($event_set["infos"][0]["code"]);
	// echo "</pre>";
	// if (isset($event_set["infos"][0]["code"])) {
	// 	echo "on a un code <br>";
	// } else {
	// 	echo "on n'a pas de code <br>";
	// }
	$is_check_in = (isset($event_set["infos"][0]["code"]));
	if (!isset($_SESSION["user_id"]))
	{
		$is_owner = false;
	} else {
		$is_owner = ($_SESSION["user_id"] === $event_set["infos"][0]["owner"]);
	}
	
	$paylink = (!is_null($event_set["infos"][0]["paylink"])) ? $event_set["infos"][0]["paylink"] : "";
	
	$_SESSION["secured"]=$event_set["infos"][0]["secured"]; // used on search page to display e-mail input in form
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
					registrations.present,
					members.fede_id,
					members.lastname,
					members.firstname,
					members.member_grade,
					members.member_type,
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
		<br>
		<?php if ($is_owner): ?>
			<button id="EditEventButton" ><?=$str['Modify']?></button>
			<button id="DeleteEventButton" ><?=$str['Delete']?></button>
			<button id="CloneEventButton" ><?=$str['Clone']?></button>
			<button id="ShareEventButton" ><?=$str['Share']?></button>
			<div class='E4M_sharelink' id="E4M_sharelink"></div>
		<?php endif; ?>
		
		<div id="E4M_eventinfo"></div>
		
		<div id="E4M_sub_data" ></div>
		<div id="sub_selector" class="E4M_buttonset"></div>
		<div id="E4M_subeventinfo" ></div>
		<div id="E4M_subevent_cat" class="E4M_catlist" ></div>
		<div id="E4M_subevent_gen" class="E4M_catlist" ></div>
		<div id="E4M_subevent_typ" class="E4M_catlist" ></div>
		
		<?php if ($is_owner): ?>
			<br/>
			<button id="EditSubEventButton"><?=$str['Modify']?></button>
			<button id="DeleteSubEventButton" ><?=$str['Delete']?></button>
			<button onclick="download()"><?=$str['Download']?></button>	
		</form >
		<?php endif; ?>

		<br/>	<br/>
		<div class="E4M_bigbutton">
			<button id="RegisterButton" ><?=$str['Register']?></button>
		</div>
		<br/>	<br/>	
		<table id="reglist" class="E4M_regtable"></table>
		<br/>
		<div id="E4M_legend_status" style="font-size: 0.8em"></div>	
	</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	
	/* let's declare global variables used by external JS */
	
	var rating_names = JSON.parse(`<?=$rating_names_str?>`);
	var cat_names = JSON.parse(`<?=$cat_names_str?>`);
	var gender_names = JSON.parse('<?=$gender_names_str?>');
	var type_names = JSON.parse('<?=$type_names_str?>');
	var str = JSON.parse(`<?=$jsonstr?>`);
	var subevent_link_icon = JSON.parse(`<?=$subevent_link_icon_str?>`);
	
	var subs_data_set = JSON.parse(`<?=$subs_data_jsonstr?>`);
	
	
	var event_data_set = JSON.parse(`<?=$event_set_jsonstr?>`);
	var registration_search_page = `<?= $cfg['registration_search_page'] ?>`;
	var CurrentEventId = <?=$event_id ?>;
	var CurrentSubEventIndex = 0; 	// index of the internal table from json (0, 1,...)
	var CurrentSubEventId = 0; 	// id in the database
	var CurrentRating = 1; 	// default value, will later depend on rating in subevent
	var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
	var CurrentSubEventObj; // {Array for current subevent}
	var NbSubs; // Number of SubEvents in current events
	var NbRegTot; // Total Number of registred members
	var CurrentNbmax ; // max subscriptions for current subevent
	var subevent_list_str; // will contain JSON of subevents data // debug ça sert à rien ?!!
	var is_owner = false;
	var paylink = `<?= $paylink ?>`
	var is_paylink = !(paylink == "");
	
	let is_owner_php = `<?= $is_owner ?>`;
	let is_check_in = (`<?= $is_check_in ?>` == 1);
	let newSubURL = "";
	let editSubURL = "";

	if (is_owner_php == "1") is_owner=true; // is_owner = user connected is the owner of current event
	
	if (is_owner)
	{
		newSubURL = "edit-autocreate-subevent.php?event_id=<?=$event_id ?>"
	}

	let nbSubevents = subs_data_set.length;
	
	if (is_owner || nbSubevents>1) // subevent selector is displayed if there are more than one subevent or (if 1) for the owner to add one
	{
		var subSelector = new Selector (
			"sub_selector",
			nbSubevents,
			CurrentSubEventIndex,
			SelectEvent,
			newSubURL
		);
	}
	
	
	
	var cat_set = new IconSet 
	(
		"E4M_subevent_cat", 
		cat_names,
		subs_data_set[CurrentSubEventIndex].cat,
		"E4M_cat",
		false
	);
	var gen_set = new IconSet 
	(
		"E4M_subevent_gen", 
		gender_names,
		subs_data_set[CurrentSubEventIndex].gender,
		"E4M_gen",
		false
	);	
	var typ_set = new IconSet 
	(
		"E4M_subevent_typ", 
		type_names,
		subs_data_set[CurrentSubEventIndex].type,
		"E4M_typ",
		false
	);	
	// let's hide iconsets and register button in check-in mode
	document.getElementById('E4M_subevent_cat').style.display = is_check_in ? "none" : "inherit";
	document.getElementById('E4M_subevent_gen').style.display = is_check_in ? "none" : "inherit";
	document.getElementById('E4M_subevent_typ').style.display = is_check_in ? "none" : "inherit";
	document.getElementById('RegisterButton').style.display = is_check_in ? "none" : "inherit";



	/* those 3 html elements will be updated each time the user selects a subevents*/
	var event_html_id = document.getElementById('E4M_eventinfo');
	var subevent_html_id = document.getElementById('E4M_subeventinfo'); // icon sets added separately from Aug 30th 2021
	var registred_html_id = document.getElementById('E4M_regtable');
	
	var sort_method = "default"; // default (datereg) | name | rating | club | cat 
	const EditSubBnt = document.getElementById('EditSubEventButton');
	const EditEvtBnt = document.getElementById('EditEventButton');
	const DelSubBnt = document.getElementById('DeleteSubEventButton');
	const DelEventBnt = document.getElementById('DeleteEventButton');
	const CloneEventBnt = document.getElementById('CloneEventButton');
	const ShareEventBnt = document.getElementById('ShareEventButton'); // keep that ??
	const sharetext = document.getElementById('E4M_sharelink');
	const RegisterBtn = document.getElementById('RegisterButton');

	RegisterBtn.addEventListener("click", GoToRegisterPage);

	RegisterBtn.SubIndex = CurrentSubEventIndex;
	let RegisterPage = (is_paylink && !is_owner) ? paylink : "<?=$cfg['registration_search_page']?>";
	RegisterBtn.nextPage = RegisterPage;
	

	/** Prepares buttons for Event and subevent (if viewed by owner) [part 1/2]*/
	if (is_owner) 
	{
		EditEvtBnt.addEventListener("click", GotoEditEventPage);
		EditEvtBnt.destination = CurrentEventId;
	
		CloneEventBnt.destination="<?=$cfg['event_page']?>";
		//console.log("hello")
		//console.log(CloneEventBnt.destination)
		CloneEventBnt.addEventListener("click", CloneCurrentEvent);

		sharetext.style.visibility = "collapse";	
		let destination = "<?=$cfg['short_url']?>" + CurrentEventId.toString(10);
		sharetext.innerHTML = destination;
		
		ShareEventBnt.addEventListener("click", function(){
			sharetext.style.visibility = "visible";
			let message = "<?=$str['Share_hint']?>";
			alert(message);
		});
	}

	eventinfoset =event_data_set['infos'][0]; 

	// as long as datetime will be stored with space in database, replacement by T is required for iOS
	let datelim = new Date(eventinfoset.datelim.replace(" ", "T"));
	
	RegisterBtn.disabled = !( datelim-Date.now() > 0 ); 
	CurrentSubEventId = subs_data_set[CurrentSubEventIndex]["id"]; 
	/** Prepares buttons for Event and subevent (if viewed by owner) [part 2/2]*/
	if (is_owner)
	{
		EditSubBnt.addEventListener('click', gotoEditCurrentSubevent);
		DelSubBnt.addEventListener('click', DeleteCurrentSubEvent);
		DelSubBnt.message = "<?=$str['Deletion_done']?>";
		DelEventBnt.addEventListener('click', DeleteCurrentEvent);
		DelEventBnt.nextpage = "<?=$cfg['event_list_page']?>";
		DelEventBnt.message = "<?=$str['Deletion_done']?>";
	}

	
	CurrentSubEventObj = subs_data_set[CurrentSubEventIndex]; 
	CurrentRating =CurrentSubEventObj.rating_type;

	CurrentNbmax = CurrentSubEventObj["nbmax"];
	
	member_list =  event_data_set['registrations'];
	console.log("member_list", member_list);
	
	NbSubs=subs_data_set.length;
	NbRegTot = member_list.length;

    if ( is_owner ) {
        console.log("member_list.length = ", member_list.length);
		console.log("NbSubs = ", NbSubs);
		DelEventBnt.disabled = !(member_list.length == 0 && NbSubs == 1);
		let registration_to_unwait = unwait(eventinfoset.nbmax, subs_data_set, member_list);
        if (registration_to_unwait == null) {
            console.log("no registration to unwait")
        } else {
            let TheOne = member_list.find(el => el.regid == registration_to_unwait);
			let TheOneIndex = member_list.findIndex(el => el.regid == registration_to_unwait);
			console.log("TheOneIndex in member_list", TheOneIndex)
			let TheOneFullName = TheOne.firstname + " " + TheOne.lastname;
			console.log(`registration ${registration_to_unwait} ( ${TheOneFullName} )  to unwait`);
			EditRegistration (registration_to_unwait, 'u', TheOneFullName);
        }
    }
	event_html_id.innerHTML = eventInfos2html(eventinfoset);
	subevent_html_id.innerHTML = SubeventInfos2html(subs_data_set[CurrentSubEventIndex]);
	

	if (NbSubs > 1){
		BuildHTMLEventSelector (NbSubs);
	}
	/* constrution of the table of the registered members */
	/* this table will be display thanks to SmartTable class */
	
	member_list.forEach((item) => {
		/**
		 * let's calculate full name to be displayed in the smartTable
		 * status is also calculated from wait and confirmed.
		 * determining displayedRating here is not appropriate since it may vary with selector
		 * this will be added later
		 */
		item.fullname = item.lastname + " " + item.firstname;
		
		
		/* in check-in mode, attribute .rowLink set to correct link for members not yet checked in */
		if (is_check_in) {
			if (item.present == "0") 
			{
				item.rowLink =  "./check-in.php?id=" + item.regid.toString(10); 
				item.status = str["wait_sign"];
			} else {
				item.status = str["OK_sign"];
			}
			/* adds class E4M_hoverable_list to the table */
			document.getElementById("reglist").classList.add("E4M_hoverable_list");
		} else {
			if(item.wait == "1")
			{
				item.status = str["wait_sign"];
				item.css = "E4M_tab_not_confirmed";
			} else {
				if(item.confirmed == "0")
				{
					item.status = str["mail_sign"];
					item.css = "E4M_tab_not_confirmed";
				} else {
					item.status = str["OK_sign"];
					item.css = "E4M_tab_confirmed";
				}
			}
		}
	});
	
	var filteredList = member_list.filter( filter => filter.subid == CurrentSubEventId );
	/* For owner, if no registration and if at least 2 subevents, Delete Subevent Button is enabled*/
	if (is_owner) {
		DelSubBnt.disabled = !(filteredList.length == 0 && NbSubs>=2);
	}
	
	/* If there is at least one wait or at least one confirmation needed, then legend is displayed  */
	let StatusLegendNeeded = false;
	filteredList.forEach( item => {
		item.displayedRating = parseFloat(item["rating"+ CurrentRating])
		if(item.wait == "1" || item.confirmed == "0" ) StatusLegendNeeded ||= true; 
	});
	// for check-in, alphabetical sort is more convenient (OK for main subvent, but does not work for others)
	if (is_check_in) {
		console.log ("tri alpha");
		filteredList.sort( (a,b) => a.fullname.toString().localeCompare(b.fullname.toString())) ;
	} else {
		console.log ("pas de tri");
	}

	document.getElementById("E4M_legend_status").innerHTML =  StatusLegendNeeded ? str["status_legend"] : "";
	
	/* let's display the list of registered member for current subevent */
	let regTableSettings = {
		"headArray" : ["", str["Member"],str["Type_header"] ,str["header_rating_name"],str["cat"],str["club_name"],str["region_name"], "🚦"],
		"IOfieldName": "wait",
		"activeHeader" :"",
		"colData" : ["member_grade", "fullname", "member_type","displayedRating", "cat", "clubname", "region", "status"],
		"colSorted" : -1
	};
	regTableSettings.active = is_owner;
	
	var regTable = new smartTable (
		"reglist", 
		filteredList,
		regTableSettings
	);
	

	
</script>
