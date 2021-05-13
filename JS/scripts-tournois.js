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
