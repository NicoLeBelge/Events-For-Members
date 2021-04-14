<?php
$json = file_get_contents('strings.json');
$str = json_decode($json,true);	
?>

<div id="eventinfo"></div>
<hr/>
<div id="select_event"></div>
<hr/>
<div id="subeventinfo"></div>
<hr/>

<script type="text/javascript">
/* marche avec toutes les  variables en local dans onreadystatechange*/ 
function eventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing events infos 	*/
	var h="";
	h += "<h3>" + infoset.name + "</h3>" ;
		h += "<p>" + infoset.datestart + "</p>" ;
		if (infoset.secured =="1"){
			h += "<p>" + "info : sécurisé, à traduire" + "</p>" ;
		}
		h += "<p>" + "inscription avant le " + infoset.datelim +"</p>" ;
	return h;
}

function SelectEvent(NumEvent) {
	alert("tu as cliqué  sur " + NumEvent);

}
function BuildHTMLEventSelector (n){
	var h="";
	var k=0;
	var sel = document.getElementById('select_event');
	for (k=0; k<=n-1; k++) {
		h += "<div onclick=SelectEvent(" + k + ") >" + (k+1) + "</div>";
		
	}
	sel.innerHTML = h;

}

function SubeventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing subevents infos 	*/
	var h="";
	h += "<h3>" + infoset.name + "</h3>" ;
		h += "<p>" + infoset.datestart + "</p>" ;
		if (infoset.nbmax ==  null){
			h += "<p>" + "No limit !" + "</p>" ;
		} else {
			h += "<p>" + "there's a limit !" + "</p>" ;
		}
		//h += "<p>" + "inscription avant le " + infoset.datelim +"</p>" ;
	return h;
}

var rq_event = new XMLHttpRequest();
var requestURL = './API/get-event-info.php?event=1';
/*
returns object like this  :
Obj["infos"] = Array(1) of Objects  
	Event["0"] = {id="1", name = "eventname", datestart ="blabla",...}
Obj["subs"] = Array (n) of Objects  
	Sub[0] = {id="u", name = "subeventname", ...}
	Sub[1] = {id="v", name = "subeventname", ...}
	...
	Sub[n-1] = {id="w", name = "subeventname", ...}
*/


rq_event.open('GET', requestURL);
rq_event.responseType = 'json';
rq_event.send();

rq_event.onreadystatechange  = function() {
	if (this.readyState == 4 && this.status == 200) {
		var event_data_set = this.response;
		var event_infos = event_data_set['infos'];
		var eventinfoset = event_infos["0"]; // can only be "0"
		var subevent_list = event_data_set['subs'];
		var subEventObj =  {};
		var e = document.getElementById('eventinfo');
		var s = document.getElementById('subeventinfo');
		var event_info_html = "";
		var subevent_info = "";
		var subhtml = SubeventInfos2html(subevent_list[0]); // vérifié, c'est OK

		e.innerHTML = eventInfos2html(eventinfoset);
		s.innerHTML = subhtml;

		var k = 0;
		var NbSubs = subevent_list.length; // je teste...
		BuildHTMLEventSelector (NbSubs);
		

		for (k=0; k<=NbSubs-1; k++) {
			subEventObj[k]=subevent_list[k];
			console.log (subEventObj[k].name,"SubEvent #"+k);
		}
	}
	/*
	Textes à passer : oui, non, confirmation, le(date), texte_date_lim
	*/
	
}
</script>
