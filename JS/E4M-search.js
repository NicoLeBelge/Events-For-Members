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
function pickplayer (member_id) {
	let filtered_members = members.filter(function(filter){
		return filter.id == member_id;
	});
	let member = filtered_members[0];
	//console.log(currentSubEvent);
	let isMatching = isPlayerMatching(member, currentSubEvent);
}
function isPlayerMatching (member, sub) {
	/*
	"Gender_matching_problem":"Problème de respect des restrictions de genre",
    "Category_matching_problem":"Problème de respect des restrictions de catégorie",
    "Rating_matching_problem":"Problème de respect des restrictions de classement Elo",
	*/
	console.log(member);
	console.log(sub);
	let isMatching= true;
	let alertSTR="";
	if (sub.gender !== '*'){
		if (!sub.gender.includes(member.gender)){
			isMatching= false;
			alertSTR += str["Gender_matching_problem"];
		} 
	}

	if (alertSTR!=="") {
		alert(alertSTR);
	}
}
