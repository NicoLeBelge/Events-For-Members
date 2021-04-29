/*
used by temporary code searchmember-with-XHR.php
*/
function PlayersObjToTable(playerlist) {
	var i = 0;
	var tablech = '';
	var debugch='';
	tablech += '<table>';
	tablech += '<tr>';
	tablech += '<th>idFFE</th>';
	tablech += '<th>nom</th>';
	tablech += '<th>prénom</th>';
	tablech += '<th>elo</th>';
	tablech += '<th>cat</th>';
	tablech += '<th>club</th>';
	tablech += '<th>ville</th>';
	
	tablech += '</tr>';
	//console.log('response', this.response); // ça marche
	for (i in playerlist){
		
		tablech += '<tr onclick = pickplayer(\'' + playerlist[i].id + '\')>';
		tablech += '<td>' + playerlist[i].fede_id + '</td>';
		tablech += '<td>' + playerlist[i].lastname + '</td>';
		tablech += '<td>' + playerlist[i].firstname + '</td>';
		tablech += '<td>' + playerlist[i].rating + '</td>';
		tablech += '<td>' + playerlist[i].cat + '</td>';
		tablech += '<td>' + playerlist[i].club_name + '</td>';
		tablech += '<td>' + playerlist[i].city + '</td>';
		tablech += '</tr>';
	}
	tablech += '<table>';
	return tablech;
}
function CatArrayToList (FullList, ShortList) {
	/*
	Constructs a html div block where all elements of FullList are displayed with class E4M_on/off
	whether the element is included or not in ShortList
	*/
	let html_string="";
	let Style_on = "E4M_on";
	let Style_off = "E4M_off";
	
	html_string += "<div class='E4M_catlist'>" ;
	FullList.forEach(function(element){
		if (ShortList.includes(element)){
			html_string += "<div class='"+ Style_on + "'>" + element + "</div>";
		} else {
			html_string += "<div class='"+ Style_off + "'>" + element + "</div>";
		}
	});
	html_string += "</div>" ;
	return html_string;
}
/*function MaSomme(a,b){
	console.log (str['Gender']);
	return a+b;
	
}*/
function Hello(){
	console.log (str['Gender']);
}
function eventInfos2html (infoset){
	/* 	
	constructs a HTML bloc from the object containing events infos 	
	input : infoset = associative array with event information (name, datestart,...)
	*/
	let html_string="";
	html_string += "<h3>" + infoset.name + "</h3>" ;
	html_string += "<p>" + infoset.datestart + "</p>" ;
	html_string += "<p>" + str['Date_max_registration'] + " : " +infoset.datelim + "</p>" ;
	if (infoset.secured =="1"){
		html_string += str['Event_secured_info'] + "</p>" ;
	} else {
		html_string += "<p>" + str['Event_notsecured_info'] + "</p>" ;
	}
	html_string += "<p>" + str['Contact'] + " : " + infoset.contact +"</p>" ;
	if (infoset.pos_long !==null && infoset.pos_lat !==null ){
		let url = "https://openstreetmap.org/"
		url+="?mlat="+infoset.pos_lat;
		url+="&mlon="+infoset.pos_long;
		url+="#map=17";
		url+="/"+infoset.pos_lat;
		url+="/="+infoset.pos_long;
		html_string += "<p>" + str['Show_on_map'] + " <a href = " + url 
		html_string += " target='_blank'><img src='./img/geomarker.png'> </a></p>" ;
		
		/* debug niveau de zoom (#map) pas respecté, à creuser, pourquoi ? */
	}
	html_string += str['Nb_tot_reg'] + " : " + NbRegTot;
	return html_string;
}