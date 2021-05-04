function MembersObjToTable(memberList) {
	/*
	Builds a html table from the list of members passed as parameter
	*/
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
	
	for (i in memberList){
		tablech += '<tr onclick = pickplayer(\'' + memberList[i].id + '\')>';
		tablech += '<td>' + memberList[i].fede_id + '</td>';
		tablech += '<td>' + memberList[i].lastname + '</td>';
		tablech += '<td>' + memberList[i].firstname + '</td>';
		tablech += '<td>' + memberList[i].rating + '</td>';
		tablech += '<td>' + memberList[i].cat + '</td>';
		tablech += '<td>' + memberList[i].club_name + '</td>';
		tablech += '<td>' + memberList[i].city + '</td>';
		tablech += '</tr>';
	}
	tablech += '<table>';
	return tablech;
}
function pickplayer (fede_id) {
	alert ('tu as cliqué sur le joueur avec l\'id' + fede_id);
}
