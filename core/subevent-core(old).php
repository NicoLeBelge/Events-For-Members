<?php
$json = file_get_contents('strings.json');
$str = json_decode($json,true);	
?>

<div id="eventinfo"></div>


<div id="subeventinfo"></div>

<script type="text/javascript">

var rq_event = new XMLHttpRequest();
var requestURL = './API/get-event-info.php?event=1';

rq_event.open('GET', requestURL);
rq_event.responseType = 'json';
rq_event.send();

rq_event.onreadystatechange  = function() {
	if (this.readyState == 4 && this.status == 200) {
		var event_data_set = this.response;
		console.log(event_data_set, "full set");
		console.log("------------");

		//console.log(event_data_set[0]);
		var event_data = event_data_set[0];
		
		var subevent_list = event_data_set['subs'];
		
		var e = document.getElementById('eventinfo');
		var s = document.getElementById('subeventinfo');
		var event_info = "";
		var subevent_info = "";
		console.log(subevent_list,"event_data_set['subs']");
		event_info += "<h3>" + event_data['name'] + "</h3>" ;
		event_info += "<p>" + event_data['datestart'] + "</p>" ;
		
		if (event_data['secured']){ /* bon ça, ça marche pas*/
			event_info += "<p>" + "info : sécurisé, à tradurie" + "</p>" ;
		}
		event_info += "<p>" + "inscription avant le " + event_data['datelim'] +"</p>" ;
		e.innerHTML = event_info;
		
		//subevent_info="pipo subevent info";
		var NbSubs = Object.keys(subevent_list).length;
		console.log("------------");
		console.log(Nbsubs, "subevents in this event");
		var subevent_line = subevent_list[0];
		console.log("------------");
		console.log(subevent_line);

		s.innerHTML = subevent_info;


		
	}
	/*
	Textes à passer : oui, non, confirmation, le(date), texte_date_lim
	*/
	
}
</script>
