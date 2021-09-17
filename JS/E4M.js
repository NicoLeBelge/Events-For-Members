function eventInfos2html (infoset){
	/* 	
	constructs a HTML bloc from the object containing events infos 	
	input : infoset = associative array with event information (name, datestart,...)
	*/
	let html_string="";
	html_string += "<h3>" + infoset.name + "</h3>" ;
	
	let DateEvent = new Date(infoset.datestart);
	let DateLim = new Date(infoset.datelim);

	//html_string += "<p> Le" + DateEvent.toLocaleDateString() + "</p>" ;
	html_string += "<p><span class='E4M_css_key'>" + str['Date_of_place'] + "</span> " + DateEvent.toLocaleDateString() + "</p>" ;

	
	html_string += "<p><span class='E4M_css_key'>" + str['Register_before'] + "</span> "  +DateLim.toLocaleString() + "</p>" ;
	if (infoset.secured =="1"){
		html_string += str['Event_secured_info'] + "</p>" ;
	} 
	if (infoset.contact !==null) {
		html_string += "<p><span class='E4M_css_key'>" + str['Contact'] + "</span> " + infoset.contact +"</p>" ;
	}
	
	html_string += "<p><span class='E4M_css_key'>" + str['Nb_tot_reg'] + " </span> " + NbRegTot + " </p> ";
	
	if (infoset.nbmax !== null){
		html_string += "<p><span class='E4M_css_key'> " + str['Max_reg'] + " </span> " + infoset.nbmax + " </p>";
		if (NbRegTot >= infoset.nbmax){
			html_string += "</p>"+ str['Full'] +"<p>";
			html_string += "</p>"+ str['Waiting_list'] +"<p>";
		}
	}

	
	if (infoset.pos_long !==null && infoset.pos_lat !==null ){
		let url = "https://openstreetmap.org/"
		url+="?mlat="+infoset.pos_lat;
		url+="&mlon="+infoset.pos_long;
		url+="#map=17";
		url+="/"+infoset.pos_lat;
		url+="/="+infoset.pos_long;
		html_string += "<p><span class='E4M_css_key'>" + str['Show_on_map'] + "</span> <a href = " + url 
		html_string += " target='_blank'><img src='./img/geomarker.png'> </a></p>" ;
		
		/* debug niveau de zoom (#map) pas respecté, à creuser, pourquoi ? */
	}
	
	return html_string;
}
function SubeventInfos2html (infoset){
	/*
	constructs a HTML bloc from the object containing subevents infos 	
	input : infoset = associative array with subevent information (name, rating_type,...)
	*/
	let html_string="";
	html_string += "<h3>" + infoset.name + "</h3>" ;
	if (infoset.datestart !== null){
		html_string += "<p>" + infoset.datestart + "</p>" ;
	}
	let restriction_string="";
	if (infoset.rating_restriction == 1) {
		restriction_string = infoset.rating_comp + infoset.rating_limit;
	} 
	html_string += "<p>" + str['Rating_name'] +" : " + rating_names[infoset.rating_type-1] + " " + restriction_string  +"</p>" ;
	
	/* nbmax for subevent */
	if (infoset.nbmax !== null){
		html_string += "<p><span class='E4M_css_key'>" +  str['Nb_max_participants'] + "</span> : "+ infoset.nbmax +"</p>"  ;
	}

	/* link */
	if (infoset.link !== null){
		//html_string += "<p>" + str['Label_link_to_sub'] + " : <a href=" + infoset.link + ">"+infoset.link+"</a></p>" ;
		html_string += "<p> <a href=" + infoset.link + "> <img src='"+ subevent_link_icon + "'/></a></p>" ;
	}
	// html_string += "<br/>"
	return html_string;
	
}
function BuildHTMLEventSelector (n){ 
	/* 	Builds the set of number one can click on to select subevent */
	/* 	Should not be called if NbSubEvents =1 */
	let html_string="";
	html_string += str['Selector_help'] + n + " " + str['Subevnent_names'] ; 
	html_string += "<div class='E4M_buttonset'>";
	let sel = document.getElementById('E4M_sub_data');
    /*
	let k=0;
	
	for (k=0; k<=n-1; k++) {
		
		if (k == CurrentSubEventIndex){
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='current_sub_button' ><p>" + (k+1) + "</p></div>";
		} else {
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='other_sub_button' ><p>" + (k+1) + "</p></div>";
		}
	}
	html_string +="</div>";
	*/
	sel.innerHTML = html_string;
}
function RegList2htmltable (infoset, subid){
	/* 	
	constructs a HTML table bloc from the object containing the list of registered members for a specific subevent	
	input1 : infoset = array of members registred to the selected subevent
	input2 : subid = id of subevent
	global variables needed : str, sort_method, is_owner
	*/
	let html_string="";
	let k=0;
	let nbreg = infoset.length; // which is actually nbtotreg !! debug
	let ordered_sublist=[];
	let sublist = infoset.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	rating_selector = "rating"+ CurrentRating;
	let sort_symbol = {
		"name" : "",
		"club" : "",
		"rating" : "",
		"cat" : "",
		"region" : ""
		}; 
	switch (sort_method) {
		case 'name' : 
			sublist.sort((a,b) => a.lastname.toString().localeCompare(b.lastname.toString())); 
			sort_symbol["name"] = str["sort_mark"];
			break;
		case 'club' : 
			sublist.sort((a,b) => a.clubname.toString().localeCompare(b.clubname.toString())); 
			sort_symbol["club"] = str["sort_mark"];
			break;	
		case 'rating' : 
			sublist.sort((a,b) => -parseFloat(a[rating_selector]) + parseFloat(b[rating_selector]));	
			sort_symbol["rating"] = str["sort_mark"];
			break;
		case 'region' : 
			sublist.sort((a,b) => a.region.toString().localeCompare(b.region.toString())); 	
			sort_symbol["region"] = str["sort_mark"];
			break;
		case 'cat' : 
			sublist.sort((a,b) => a.cat.toString().localeCompare(b.cat.toString())); 	
			sort_symbol["cat"] = str["sort_mark"];
			break;
		default :
			console.log("if you read this, then you should be able to contribute to this project !");
	}
	let nbregsub = sublist.length;
	html_string += "<p>" + nbregsub + " <span class='E4M_css_key'>" +  str['registrations'] + "</span> </p>"  ;

	html_string += "<table><tr>" ;
	html_string += "<th ></th>";
	html_string += "<th onclick=SortUpdate('name')>" + str["Member"] + sort_symbol["name"] +"</th>";
	html_string += "<th onclick=SortUpdate('rating')>" + str["header_rating_name"] + sort_symbol["rating"] +"</th>";
	html_string += "<th onclick=SortUpdate('cat')>" + str["cat"] + sort_symbol["cat"] +"</th>";
	html_string += "<th onclick=SortUpdate('club')>" + str["club_name"] + sort_symbol["club"] + "</th>";
	html_string += "<th onclick=SortUpdate('region')>" + str["region_name"] + sort_symbol["region"] + "</th>";
	html_string += "<th>🚦</th>";
	if(is_owner){
		html_string += "<th></th>";
	}
	html_string += "</tr>";
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	let StatusLegendNeeded = false;
	sublist.forEach(function(member){
		let fullname = member.lastname + " "+ member.firstname;
		html_string += "<tr>" ;
		if(member.confirmed == "0" || member.wait == "1"){
			html_string += "<tr class='E4M_tab_not_confirmed'>" ;
		} else {
			html_string += "<tr class='E4M_tab_confirmed'>" ;
		}
		html_string += "<td>" + member.member_grade  + "</td>";
		html_string += "<td>" + fullname + "</td>";
		html_string += "<td>" + member[rating_selector]+"</td>";
		html_string += "<td>" + member.cat+"</td>";
		html_string += "<td>" + member.clubname+"</td>";
		html_string += "<td>" + member.region+"</td>";
		
		
		if(member.wait == "1"){
			html_string += "<td>" + str["wait_sign"] +"</td>";
			StatusLegendNeeded ||= true;
		} else {
			if(member.confirmed == "0"){
				html_string += "<td>" + str["mail_sign"] +"</td>";
				StatusLegendNeeded ||= true;
			} else {
				html_string +=  "<td>" + str["OK_sign"] +"</td>";
			}
		}
		if(is_owner){ // add link to registration edit
			if (member.confirmed == "0"){
				let destination = ` onclick = "EditRegistration(${member.regid},'c')"`;
				// warning, browser adds double quotes if space between coma and 'c' !!
				html_string += "<th" + destination + ">" + str["Validate_sign"]+ "</th>";
			} else {
				let destination = ` onclick =EditRegistration(${member.regid},'d','${member.lastname}')`;
				html_string += "<th" + destination + ">" + str["Delete_sign"]+ "</th>";
			}
		}
		html_string += "</tr>" ;
	});
	html_string += "</table>" ;
	if (StatusLegendNeeded){
		html_string += "<p>" + str["status_legend"] + "</p>" ;
	}
	return html_string;
}
function SortUpdate (method) {
	sort_method = method;
	registred_html_id.innerHTML = RegList2htmltable (member_list, CurrentSubEventIndex);
}
function EditRegistration (reg, action, member_name) {
	/* 
	allows the change registration status 
	action = 'c' --> set confirmed = 1
	action = 'd' --> delete registration
	*/
	let owner_confirmation = true;
	if (action === 'd') {
		let owner_confirmation = confirm (str['Unregister'] + "_"+ member_name + "_?");
	}
	if (owner_confirmation){

	}
	let data = new FormData();
	data.append('reg_id', reg);
	data.append('action_code', action);
	let request = new XMLHttpRequest();
	let XHR = './API/set-registration-status.php';
	request.open('POST', XHR);
	request.responseType = 'json';
	request.send(data);
	location.reload();
}

function SelectEvent(JS_Event) {
	// function called when selector clicked
	NumEvent = JS_Event.currentTarget.callback_arg;
	console.log("NumEvent = ", NumEvent)
	hidden_id.value = NumEvent;
	CurrentSubEventIndex = NumEvent;
	/* selector rebuilt to update highlighted selection */
	if (NbSubs > 1){
			BuildHTMLEventSelector (NbSubs);
	}
	CurrentSubEventId = subs_data_set[NumEvent]["id"];
	CurrentRating = subs_data_set[NumEvent]["rating_type"]; 
	subevent_html_id.innerHTML = SubeventInfos2html(subs_data_set[CurrentSubEventIndex]);
	registred_html_id.innerHTML = RegList2htmltable (member_list, NumEvent);
	cat_set.Refresh(subs_data_set[CurrentSubEventIndex].cat);
	gen_set.Refresh(subs_data_set[CurrentSubEventIndex].gender);
	typ_set.Refresh(subs_data_set[CurrentSubEventIndex].type);
	CurrentNbmax = subs_data_set[NumEvent]["nbmax"];
	CurrentSubEventObj = subs_data_set[CurrentSubEventIndex];
	subSelector.Update(NumEvent);
	
}
function download() {
	let CSVstring ="";
	let member_count=0;
	let sublist = member_list.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	rating_selector = "rating"+ CurrentRating;
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	sublist.forEach(function(member){
		member_count++;
		CSVstring += parseInt(member_count) + ";";
		CSVstring += member.memberid + ";";
		CSVstring += member.fede_id + ";";
		CSVstring += member.lastname + ";";
		CSVstring += member.firstname + ";";
		CSVstring += member[rating_selector]+ ";";
		CSVstring += member.clubname+ ";";
		CSVstring += member.region+ ";";
		CSVstring += "\n" ;
	});
	
	var filename = CurrentSubEventObj.name +".csv";
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(CSVstring));
	element.setAttribute('download', filename);
	element.style.display = 'none';// necessary ??
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element); // necessary ??
}