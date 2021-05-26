function MembersObjToTable(memberList) {
	/*
	Builds a html table from the list of members passed as parameter
	*/
	var i = 0;
	var tablech = '';
	var debugch='';
	tablech += '<table>';
	tablech += '<tr>';
	tablech += '<th>' + str["fede_id"] + '</th>';
	tablech += '<th>' + str["lastname"] + '</th>';
	tablech += '<th>' + str["firstname"] + '</th>';
	tablech += '<th>' + str["Type_header"] + '</th>';
	tablech += '<th>' + str["header_rating_name"] + '</th>';
	tablech += '<th>' + str["cat"] + '</th>';
	tablech += '<th>' + str["Gender_header"] + '</th>';
	tablech += '<th>' + str["club_name"] + '</th>';
	tablech += '<th>' + str["City"] + '</th>';
	tablech += '</tr>';

	for (i in memberList){
		tablech += '<tr onclick = pickplayer(\'' + memberList[i].id + '\')>';
		tablech += '<td>' + memberList[i].fede_id + '</td>';
		tablech += '<td>' + memberList[i].lastname + '</td>';
		tablech += '<td>' + memberList[i].firstname + '</td>';
		tablech += '<td>' + memberList[i].member_type + '</td>';
		tablech += '<td>' + memberList[i].rating + '</td>';
		tablech += '<td>' + memberList[i].cat + '</td>';
		tablech += '<td>' + memberList[i].gender + '</td>';
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
	
	
	if (isPlayerMatching(member, currentSubEventObj)){
		document.getElementById('member_id').value = member.id;
		document.getElementById('member_name').value = member.firstname + " " + member.lastname;
		document.getElementById('sub_id').value = currentSubEventId;
		
		document.getElementById('register_btn').disabled = false;
		
		//ValidationForm.style.visibility = "visible";
		ValidationForm.style.display = "inline";
	}
}
function isPlayerMatching (member, sub) {
	let isMatching= true;
	let alertSTR="";
	if (sub.gender !== '*'){
		if (!sub.gender.includes(member.gender)){
			isMatching= false;
			alertSTR += "\n" + str["Gender_matching_problem"];
		} 
	}
	if (sub.type !== '*'){
		if (!sub.type.includes(member.member_type)){
			isMatching= false;
			alertSTR += "\n" + str["Type_matching_problem"];
		} 
	}
	if (sub.cat !== '*'){
		if (!sub.cat.includes(member.cat)){
			isMatching= false;
			alertSTR += "\n" + str["Category_matching_problem"];
		} 
	}
	if (sub.rating_restriction !== '0'){
		if (sub.rating_comp == ">"){ 
			// note : data from the database are read as text
			if (parseFloat(member.rating) <= parseFloat(sub.rating_limit)){
				alertSTR += "\n" + str["Rating_matching_problem"];
				isMatching= false;
			}
		}
		if (sub.rating_comp == "<"){ 
			if (parseFloat(member.rating) >= parseFloat(sub.rating_limit)){
				alertSTR += "\n" + str["Rating_matching_problem"];
				isMatching= false;
			}
		}
	}
	if (alertSTR!=="") {
		alertSTR = member.firstname + " " + member.lastname + "\n" + alertSTR;
		alert(alertSTR);
	}
	return isMatching;
}