<?php
$json = file_get_contents('strings.json');
$str = json_decode($json,true);	
?>

<div id="eventinfo"></div>
<hr/>

<div id="subeventinfo"></div>
<hr/>

<script type="text/javascript">
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
		console.log("------------");
		console.log("event_data_set | " + typeof(event_data_set));
		console.log(event_data_set);
		
		
		var event_infos = event_data_set['infos'];
		console.log("------------");
		console.log("event_infos | " + typeof(event_infos));
		console.log(event_infos);
		


		var eventinfoset = event_infos["0"]; // can only be "0"
		
		/*
		var i=1;
		var itxt = i.tostring;
		console.log(itxt);
		eventinfoset = event_infos[itxt];
		*/
		//alert(eventInfos2html(eventinfoset));
		
		var subevent_list = event_data_set['subs'];
		var subEventObj =  {};
		var e = document.getElementById('eventinfo');
		var s = document.getElementById('subeventinfo');
		
		// var s = document.getElementById('subeventinfo');
		var event_info_html = "";
		var subevent_info = "";
		var subhtml = SubeventInfos2html(subevent_list[0]);

		console.log(subhtml,"ce que retourne subeventinfohtml");
		e.innerHTML = eventInfos2html(eventinfoset);
		//s.innerHTML = SubeventInfos2html(subevent_list[0]);
		s.innerHTML = subhtml;
		//subevent_info="pipo subevent info";
		var k = 0;
		var NbSubs = subevent_list.length; // je teste...
		
		console.log (NbSubs,"nombre de subevents");
		for (k=0; k<=NbSubs-1; k++) {
			console.log (k,"k");
			subEventObj[k]=subevent_list[k];
			console.log (subEventObj[k].name);
		}

		subEventObj[0] = subevent_list[0];
		
		console.log("------------------");
		console.log("subEventObj['0']"); 

		console.log(subEventObj[0]);
		subEventObj[1] = subevent_list[1];
		console.log("------------------");
		console.log("subEventObj['1']");
		console.log(subEventObj[1]);
		console.log("------------------");
		console.log("nom du subEventObj['1']");
		console.log(subEventObj[1].name);
		/*
		var NbSubs = Object.keys(subevent_list).length;
		console.log("------------");
		console.log(Nbsubs, "subevents in this event");
		var subevent_line = subevent_list[0];
		console.log("------------");
		console.log(subevent_line);
		*/
		s.innerHTML = subevent_info;


		
	}
	/*
	Textes à passer : oui, non, confirmation, le(date), texte_date_lim
	*/
	
}
</script>
