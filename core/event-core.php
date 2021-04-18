<?php
$json = file_get_contents('config.json'); // not sure if needed //ici
$cfg = json_decode($json,true);	

$json = file_get_contents('strings.json');
$str = json_decode($json,true);	

if(isset($_GET['id'])){
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
<hr/>
<div id="E4M_regtable" class="E4M_regtable"></div>
<hr/>
<script type="text/javascript" src="./JS/functions_event.js"></script>
<script type="text/javascript">

var CurrentSubEvent = 0; 	// index of the internal table from json
var CurrentSubEventId = 0; 	// id in the database
var CurrentRating = 1; 	
var eventinfoset; // {id="1", name = "eventname", datestart ="blabla",...}
var subevent_list; // Array() of subevent_info_sets
var eventinfoset;
var NbSubs; // Number of SubEvents
var NbRegTot; // Total Number of registred members

var e = document.getElementById('E4M_eventinfo');
var s = document.getElementById('E4M_subeventinfo');
var r = document.getElementById('E4M_regtable');




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
		NbRegTot = member_list.length;
		//console.log(NbRegTot, " incrits au total");
		e.innerHTML = eventInfos2html(eventinfoset);
		s.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
		r.innerHTML = RegList2htmltable (member_list, CurrentSubEvent);
		BuildHTMLEventSelector (subevent_list.length);
	}
}
</script>
