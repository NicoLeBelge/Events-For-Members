<?php
/*

page to be included in a php page (event.php or any name chosen by admin)
input : event id | eg event.php?id=12
javascript functions are at the bottom of the page, not externally located, to allow custom strings ($str) insertion
Defines 4 div elements showing event information, subevent selector, info about selected event, list of registred members
*/
$json = file_get_contents('./json/config.json'); // not sure if needed //ici debug
$cfg = json_decode($json,true);	
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);


$json = file_get_contents('./json/strings.json');

$str = json_decode($json,true);	
$jsonstr = json_encode($str);	


if(isset($_GET['id'])){ // bug : affiche toujours l'évènement 1 :-(
	$eventid=$_GET['id'];
} else {
	$eventid=1;
}

?>

<div id="E4M_eventinfo" ></div>
<hr/>
<div id="E4M_select_event"></div>
<hr/>
<div id="E4M_subeventinfo" class="E4M_subeventinfo"></div>
<a href='<?=$cfg['registration_page']?>'><button><?=$str['Register']?></button></a> &nbsp;
<?php if (ISSET($_SESSION['user_id'])): ?>
	<button onclick="download()"><?=$str['Download']?></button>	
<?php endif; ?>


<hr/>
<div id="E4M_regtable" class="E4M_regtable"></div>
<hr/>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">
	
	// let's build the object 'rating_names' from the JSON passed by php
	var rating_names = new Array();
	rating_names = JSON.parse(`<?=$rating_names_str?>`);
	// the same for the names of categories
	var cat_names = new Array();
	cat_names = JSON.parse(`<?=$cat_names_str?>`);
	// ... for the genders
	var gender_names = new Array();
	gender_names = JSON.parse('<?=$gender_names_str?>');
	
	// let's do it for customs strings
	var str = JSON.parse(`<?=$jsonstr?>`);
	//console.log (MaSomme(1,2));
	//console.log("Hello from the script");
	Hello();

	var CurrentSubEvent = 0; 	// index of the internal table from json
	var CurrentSubEventId = 0; 	// id in the database
	var CurrentRating = 1; 	// default value, will later depend on rating in subevent
	var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
	var subevent_list; // Array() of subevent_info_sets
	var NbSubs; // Number of SubEvents
	var NbRegTot; // Total Number of registred members

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
		eventinfoset =event_data_set['infos'][0];
		subevent_list = event_data_set['subs'];
		CurrentSubEventId = subevent_list[0]["id"];
		member_list =  event_data_set['registrations'];
		NbRegTot = member_list.length;
		event_html_id.innerHTML = eventInfos2html(eventinfoset);
		subevent_html_id.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
		registred_html_id.innerHTML = RegList2htmltable (member_list, CurrentSubEvent);
		NbSubs=subevent_list.length;
		if (NbSubs > 1){
			BuildHTMLEventSelector (NbSubs);
		}
	}
}
function eventInfos2html_old (infoset){
	/* 	
	constructs a HTML bloc from the object containing events infos 	
	input : infoset = associative array with event information (name, datestart,...)
	*/
	let html_string="";
	html_string += "<h3>" + infoset.name + "</h3>" ;
	html_string += "<p>" + infoset.datestart + "</p>" ;
	html_string += "<p>" + "<?=$str['Date_max_registration']?> : " + infoset.datelim +"</p>" ;
	if (infoset.secured =="1"){
		html_string += "<p><?=$str['Event_secured_info']?></p>" ;
	} else {
		html_string += "<p><?=$str['Event_notsecured_info']?></p>" ;
	}
	html_string += "<p>" + "<?=$str['Contact']?> : " + infoset.contact +"</p>" ;
	if (infoset.pos_long !==null && infoset.pos_lat !==null ){
		let url = "https://openstreetmap.org/"
		url+="?mlat="+infoset.pos_lat;
		url+="&mlon="+infoset.pos_long;
		url+="#map=17";
		url+="/"+infoset.pos_lat;
		url+="/="+infoset.pos_long;
		html_string += "<p><?=$str['Show_on_map']?> <a href = " + url + " target='_blank'><img src='./img/geomarker.png'> </a></p>" ;
		
		/* debug niveau de zoom (#map) pas respecté, à creuser, pourquoi ? */
	}
	html_string += "<?=$str['Nb_tot_reg']?> : "+NbRegTot;
	return html_string;
}
function SubeventInfos2html (infoset){
	/*
	constructs a HTML bloc from the object containing subevents infos 	
	input : infoset = associative array with subevent information (name, rating_type,...)
	*/
	let html_string="";
	
	/* subevent name */
	html_string += "<h3>" + infoset.name + "</h3>" ;
	
	/* date subevent */
	if (infoset.datestart !== null){
		html_string += "<p>" + infoset.datestart + "</p>" ;
	}
	/* gender */
	html_string += "<p><?=$str['Gender']?> : ";
	let gender_array = new Array();
	if (infoset.gender == "*") {
		gender_array = gender_names;
	} else {
		gender_array = JSON.parse(infoset.gender);
	}
	let iconString = CatArrayToList (gender_names, gender_array); 
	html_string += iconString + "</p>";
	
	/* categories */
	html_string += "<p><?=$str['Categories']?> : ";
	let cat_array = new Array();
	if (infoset.cat == "*") {
		cat_array = cat_names;
	} else {
		cat_array = JSON.parse(infoset.cat);
	}
	iconString = CatArrayToList (cat_names, cat_array); 
	html_string += iconString + "</p>";
	
	/* max participants */
	if (infoset.nbmax !==  null){
		html_string += "<p>" + "<?=$str['Nb_max_participants']?> : " + infoset.nbmax + "</p>" ;
	}
	/* link */
	if (infoset.link !== null){
		html_string += "<p><?=$str['Label_link_to_sub']?> : <a href=" + infoset.link + ">"+infoset.link+"</a></p>" ;
	}
	
	/* rating_type + optional rating restriction */
	let restriction_string="";
	if (infoset.rating_restriction == 1) {
		restriction_string = infoset.rating_comp + infoset.rating_limit;
	} 
	html_string += "<p><?=$str['Rating_name']?> : " + rating_names[infoset.rating_type-1] + " " + restriction_string  +"</p>" ;
	
	
	return html_string;
	
}
function RegList2htmltable (infoset, subid){
	/* 	
	constructs a HTML table bloc from the object containing the list of registered members for a specific subevent	
	input1 : infoset = array of members registred to the selected subevent
	input2 : subid = id of subevent
	*/
	let html_string="";
	let k=0;
	let nbreg = infoset.length; // which is actually nbtotreg !! debug
	
	let sublist = infoset.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	
	let nbregsub = sublist.length;
	html_string += "<p><?=$str['Nb_reg']?> : " + nbregsub + "</p>" ;
	
	html_string += "<table>" ;
	rating_selector = "rating"+ CurrentRating;
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	sublist.forEach(function(member){
		html_string += "<tr>" ;
		html_string += "<td>" + member.lastname + " "+member.firstname + "</td>";
		html_string += "<td>" + member[rating_selector]+"</td>";
		html_string += "<td>" + member.clubname+"</td>";
		html_string += "<td>" + member.region+"</td>";
		html_string += "</tr>" ;
	});
	html_string += "</table>" ;
	return html_string;
}
function SelectEvent(NumEvent) {
	CurrentSubEvent = NumEvent;
	if (NbSubs > 1){
			BuildHTMLEventSelector (NbSubs);
	}
	CurrentSubEventId = subevent_list[NumEvent]["id"];
	CurrentRating = subevent_list[NumEvent]["rating_type"]; 
	subevent_html_id.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
	registred_html_id.innerHTML = RegList2htmltable (member_list, NumEvent);
}
function BuildHTMLEventSelector (n){
	/* 	Builds the set of number one can click on to select subevent */
	/* 	Should not be called if NbSubEvents =1 */
	
	let html_string="<div class='E4M_buttonset'>";
	let k=0;
	let sel = document.getElementById('E4M_select_event');
	
	for (k=0; k<=n-1; k++) {
		
		if (k == CurrentSubEvent){
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='current_sub_button' ><p>" + (k+1) + "</p></div>";
		} else {
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='other_sub_button' ><p>" + (k+1) + "</p></div>";
		}
	}
	html_string +="</div>";
	sel.innerHTML = html_string;
}
function download() {
	let CSVstring ="";
	let sublist = member_list.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	rating_selector = "rating"+ CurrentRating;
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	sublist.forEach(function(member){
		CSVstring += member.memberid + ";";
		CSVstring += member.fede_id + ";";
		CSVstring += member.lastname + ";";
		CSVstring += member.firstname + ";";
		CSVstring += member[rating_selector]+ ";";
		CSVstring += member.clubname+ ";";
		CSVstring += member.region+ ";";
		CSVstring += "\n" ;
	});
	var filename =subevent_list[CurrentSubEvent].name +".csv";
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(CSVstring));
	element.setAttribute('download', filename);
	element.style.display = 'none';// necessary ??
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element); // necessary ??
	}
</script>
