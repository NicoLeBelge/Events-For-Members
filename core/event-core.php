<?php
$json = file_get_contents('strings.json');
$str = json_decode($json,true);	
if(isset($_GET['id'])){
	$eventid=$_GET['id'];
} else {
	$eventid=1;
}
?>

<div id="eventinfo"></div>
<hr/>
<div id="select_event"></div>
<hr/>
<div id="subeventinfo"></div>
<hr/>
<div id="regtable"></div>
<hr/>

<script type="text/javascript">

var CurrentSubEvent = 0; 	// index of the internal table from json
var CurrentSubEventId = 0; 	// id in the database
var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
var subevent_list; // Array() of subevent_info_sets
var eventinfoset;
var NbSubs;
var e = document.getElementById('eventinfo');
var s = document.getElementById('subeventinfo');
var r = document.getElementById('regtable');


function eventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing events infos 	*/
	let h="";
	h += "<h3>" + infoset.name + "</h3>" ;
	h += "<p>" + infoset.datestart + "</p>" ;
	h += "<p>" + "<?=$str['Date_max_registration']?> : " + infoset.datelim +"</p>" ;
	if (infoset.secured =="1"){
		h += "<p><?=$str['Event_secured_info']?></p>" ;
	} else {
		h += "<p><?=$str['Event_notsecured_info']?></p>" ;
	}
	h += "<p>" + "<?=$str['Contact']?> : " + infoset.contact +"</p>" ;
	if (infoset.pos_long !==null && infoset.pos_lat !==null ){
		let url = "https://openstreetmap.org/"
		url+="?mlat="+infoset.pos_lat;
		url+="&mlon="+infoset.pos_long;
		url+="#map=17";
		url+="/"+infoset.pos_lat;
		url+="/="+infoset.pos_long;
		h += "<p><?=$str['Show_on_map']?> <a href = " + url + " target='_blank'><img src='./img/geomarker.png'> </a></p>" ;
		/* debug niveau de zoom pas respecter, à creuser pourquoi */
	}

	return h;
}
function SubeventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing subevents infos 	*/
	let h="";
	h += "<h3>" + infoset.name + "</h3>" ;
	if (infoset.nbmax !==  null){
		h += "<p>" + "<?=$str['Nb_max_participants']?> : " + infoset.nbmax + "</p>" ;
	}
	if (infoset.datestart !== null){
		h += "<p>" + infoset.datestart + "</p>" ;
	}
	if (infoset.link !== null){
		h += "<p><?=$str['Label_link_to_sub']?> : <a href=" + infoset.link + ">"+infoset.link+"</p>" ;
	}
	return h;
	
}
function RegList2htmltable (infoset, subid){
	/* 	constructs a HTML table bloc from the object containing the list of registered members for a specific subevent	*/
	let h="";
	let k=0;
	let nbreg = infoset.length;
	//console.log (nbreg, " inscrits")
	h += "<p>Nombre d'inscrits : " + nbreg + "</p>" ;
	h += "<table>" ;
	for (k=0;k<nbreg;k++){
		
		if(infoset[k].subid==CurrentSubEventId){
		//if(true){
			h += "<tr>" ;
			h += "<td>" + infoset[k].firstname + " "+infoset[k].lastname + "</td>";
			h += "<td>" + infoset[k].clubname+"</td>";
			h += "</tr>" ;
		}
	}
	h += "</table>" ;
	console.log(h);
	return h;
	
}


function SelectEvent(NumEvent) {
	console.log("subevent_list[" + NumEvent+"]\n",subevent_list[NumEvent]);
	CurrentSubEventId = subevent_list[NumEvent]["id"];
	
	console.log("CurrentSubEventId = ",CurrentSubEventId);
	s.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
	r.innerHTML = RegList2htmltable (member_list, NumEvent);
}

function BuildHTMLEventSelector (n){
	/* 	Builds the set of number one can click on to select subevent */
	/* 	Should not be called if NbSubEvents =1 */
	
	let h="<div class='buttonset'>";
	let k=0;
	let sel = document.getElementById('select_event');
	
	for (k=0; k<=n-1; k++) {
		h += "<div onclick=SelectEvent(" + k + ") ><p>" + (k+1) + "</p></div>";
	}
	h +="</div>";
	sel.innerHTML = h;
}



var rq_event = new XMLHttpRequest();
var requestURL = './API/get-event-info.php?event=<?=$eventid?>';

rq_event.open('GET', requestURL);
rq_event.responseType = 'json';
rq_event.send();

rq_event.onreadystatechange  = function() {
	if (this.readyState == 4 && this.status == 200) {
		var event_data_set = this.response;
		
		eventinfoset =event_data_set['infos'][0];
		subevent_list = event_data_set['subs'];
		member_list =  event_data_set['registrations'];
		console.log(member_list);
		e.innerHTML = eventInfos2html(eventinfoset);
		s.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
		r.innerHTML = RegList2htmltable (member_list, CurrentSubEvent);
		BuildHTMLEventSelector (subevent_list.length);
	}
}
</script>
