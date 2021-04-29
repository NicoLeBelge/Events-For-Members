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
function MaSomme(a,b){
	return a+b;
}