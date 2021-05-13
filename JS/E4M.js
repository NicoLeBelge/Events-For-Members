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
	html_string += "<p><span class='E4M_css_key'>" + str['Contact'] + "</span> " + infoset.contact +"</p>" ;
	html_string += "<p><span class='E4M_css_key'>" + str['Nb_tot_reg'] + " </span> " + NbRegTot ;
	if (infoset.nbmax !== null){
		html_string += "<span class='E4M_css_key'> " + str['Over_total_max'] + " </span> " + infoset.nbmax +"</p>";
	}
	html_string += "<p>";
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
		
	/* subevent name */
	html_string += "<h3>" + infoset.name + "</h3>" ;
	
	/* link */
	if (infoset.link !== null){
		//html_string += "<p>" + str['Label_link_to_sub'] + " : <a href=" + infoset.link + ">"+infoset.link+"</a></p>" ;
		html_string += "<p> <a href=" + infoset.link + "> <img src='"+ subevent_link_icon + "'/></a></p>" ;
	}
	/* date subevent */
	if (infoset.datestart !== null){
		html_string += "<p>" + infoset.datestart + "</p>" ;
	}
	/* gender */
	//html_string += "<p>" + str['Gender'] + " : ";
	let gender_array = new Array();
	if (infoset.gender == "*") {
		gender_array = gender_names;
	} else {
		gender_array = JSON.parse(infoset.gender.replace(/'/g, '"')); // see cat below to understand
	}
	let iconString = CatArrayToList (gender_names, gender_array); 
	html_string += iconString + "</p>";
	
	/* categories */
	//html_string += "<p>" + str['Categories'] + " : ";
	let cat_array = new Array();
	if (infoset.cat == "*") {
		cat_array = cat_names;
	} else {
		// cat_array = JSON.parse(infoset.cat); doesn't work, I don't know why (debug)
		cat_array = JSON.parse(infoset.cat.replace(/'/g, '"')); // need to replace single quote by double quotes
	}
	iconString = CatArrayToList (cat_names, cat_array); 
	html_string += iconString + "</p>";
	
	
	/* rating_type + optional rating restriction */
	let restriction_string="";
	if (infoset.rating_restriction == 1) {
		restriction_string = infoset.rating_comp + infoset.rating_limit;
	} 
	html_string += "<p>" + str['Rating_name'] +" : " + rating_names[infoset.rating_type-1] + " " + restriction_string  +"</p>" ;
	html_string += "<br/>"
	
	
	return html_string;
	
}
function BuildHTMLEventSelector (n){
	/* 	Builds the set of number one can click on to select subevent */
	/* 	Should not be called if NbSubEvents =1 */
	let html_string="";
	html_string += str['Selector_help'] + n + " " + str['Subevnent_names'] ; 
	html_string += "<div class='E4M_buttonset'>";
	
    
	let k=0;
	let sel = document.getElementById('E4M_select_event');
	
	for (k=0; k<=n-1; k++) {
		
		if (k == CurrentSubEvent){
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='current_sub_button' ><p>" + (k+1) + "</p></div>";
		} else {
			html_string += "<div onclick=SelectEvent(" + k + ") id='selector_"+ k + "' class='other_sub_button' ><p>" + (k+1) + "</p></div>";
		}
	}
	html_string +="</div>";
	sel.innerHTML = html_string;
}
function RegList2htmltable (infoset, subid){
	/* 	
	constructs a HTML table bloc from the object containing the list of registered members for a specific subevent	
	input1 : infoset = array of members registred to the selected subevent
	input2 : subid = id of subevent
	*/
	let html_string="";
	let k=0;
	let nbreg = infoset.length; // which is actually nbtotreg !! debug
	
	let sublist = infoset.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	
	let nbregsub = sublist.length;
	html_string += "<p>" + nbregsub + " <span class='E4M_css_key'>" +  str['registrations'] + "</span> "  ;

	/* max participants */

	if (CurrentNbmax !==  null){
		html_string += "(" +  CurrentNbmax  + str['max'] +")" ;
	}
	
	html_string += "</p>" ;
	html_string += "<table>" ;
	rating_selector = "rating"+ CurrentRating;
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	sublist.forEach(function(member){
		if(member.confirmed == "1"){
			html_string += "<tr class='E4M_tab_confirmed'>" ;
		} else {
			html_string += "<tr class='E4M_tab_not_confirmed'>" ;
		}
		
		
		html_string += "<td>" + member.lastname + " "+member.firstname + "</td>";
		html_string += "<td>" + member[rating_selector]+"</td>";
		html_string += "<td>" + member.clubname+"</td>";
		html_string += "<td>" + member.region+"</td>";
		html_string += "</tr>" ;
	});
	html_string += "</table>" ;
	return html_string;
}
function SelectEvent(NumEvent) {
	CurrentSubEvent = NumEvent;
	hidden_id.value = NumEvent;
	/* selector rebuilt to update highlighted selection */
	if (NbSubs > 1){
			BuildHTMLEventSelector (NbSubs);
	}
	CurrentSubEventId = subs_data_set[NumEvent]["id"];
	CurrentRating = subs_data_set[NumEvent]["rating_type"]; 
	subevent_html_id.innerHTML = SubeventInfos2html(subs_data_set[CurrentSubEvent]);
	registred_html_id.innerHTML = RegList2htmltable (member_list, NumEvent);
	CurrentNbmax = subs_data_set[NumEvent]["nbmax"];
	
	CurrentSubEventObj = subs_data_set[CurrentSubEvent];
	
}
function download() {
	let CSVstring ="";
	let sublist = member_list.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	rating_selector = "rating"+ CurrentRating;
	/* sublist contains the list filtered for current subevent, the html table is filled with these members */
	sublist.forEach(function(member){
		CSVstring += member.memberid + ";";
		CSVstring += member.fede_id + ";";
		CSVstring += member.lastname + ";";
		CSVstring += member.firstname + ";";
		CSVstring += member[rating_selector]+ ";";
		CSVstring += member.clubname+ ";";
		CSVstring += member.region+ ";";
		CSVstring += "\n" ;
	});
	var filename =subevent_list[CurrentSubEvent].name +".csv";
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(CSVstring));
	element.setAttribute('download', filename);
	element.style.display = 'none';// necessary ??
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element); // necessary ??
	}
