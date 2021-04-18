function eventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing events infos 	*/
	let h="";
	h += "<h3>" + infoset.name + "</h3>" ;
	h += "<p>" + infoset.datestart + "</p>" ;
	h += "<p>" + "<?=$str['Date_max_registration']?> : " + infoset.datelim +"</p>" ;
	if (infoset.secured =="1"){
		h += "<p><?=$str['Event_secured_info']?></p>" ;
	} else {
		h += "<p><?=$str['Event_notsecured_info']?></p>" ;
	}
	h += "<p>" + "<?=$str['Contact']?> : " + infoset.contact +"</p>" ;
	if (infoset.pos_long !==null && infoset.pos_lat !==null ){
		let url = "https://openstreetmap.org/"
		url+="?mlat="+infoset.pos_lat;
		url+="&mlon="+infoset.pos_long;
		url+="#map=17";
		url+="/"+infoset.pos_lat;
		url+="/="+infoset.pos_long;
		h += "<p><?=$str['Show_on_map']?> <a href = " + url + " target='_blank'><img src='./img/geomarker.png'> </a></p>" ;
		
		/* debug niveau de zoom pas respecter, à creuser pourquoi */
	}
	h+="<?=$str['Nb_tot_reg']?> : "+NbRegTot;
	

	return h;
}
function SubeventInfos2html (infoset){
	/* 	constructs a HTML bloc from the object containing subevents infos 	*/
	let h="";
	h += "<h3>" + infoset.name + "</h3>" ;
	if (infoset.nbmax !==  null){
		h += "<p>" + "<?=$str['Nb_max_participants']?> : " + infoset.nbmax + "</p>" ;
	}
	if (infoset.datestart !== null){
		h += "<p>" + infoset.datestart + "</p>" ;
	}
	if (infoset.link !== null){
		h += "<p><?=$str['Label_link_to_sub']?> : <a href=" + infoset.link + ">"+infoset.link+"</p>" ;
	}
	return h;
	
}
function RegList2htmltable (infoset, subid){
	/* 	constructs a HTML table bloc from the object containing the list of registered members for a specific subevent	*/
	let h="";
	let k=0;
	let nbreg = infoset.length;
	let sublist = infoset.filter(function(filter){
		return filter.subid == CurrentSubEventId ;
	});
	let nbregsub = sublist.length;
	h += "<p><?=$str['Nb_reg']?> : " + nbregsub + "</p>" ;
	h += "<table>" ;
	for (k=0;k<nbreg;k++){
		
		if(infoset[k].subid==CurrentSubEventId){
			h += "<tr>" ;
			h += "<td>" + infoset[k].firstname + " "+infoset[k].lastname + "</td>";
			switch(CurrentRating) { // ugly code, no ? and why do I have to put double  quotes ??
			case "1":
				h += "<td>" + infoset[k].rating1+"</td>";
				break;
			case "2":
				h += "<td>" + infoset[k].rating2+"</td>";
				break;
			case "3":
				h += "<td>" + infoset[k].rating3+"</td>";
				break;
			case "4":
				h += "<td>" + infoset[k].rating4+"</td>";
				break;
			case "5":
				h += "<td>" + infoset[k].rating5+"</td>";
				break;
			case "6":
				h += "<td>" + infoset[k].rating6+"</td>";
				break;
			default:
				alert("this should not happen");
			}
			h += "<td>" + infoset[k].clubname+"</td>";
			h += "<td>" + infoset[k].region+"</td>";
			h += "</tr>" ;
		}
	}
	h += "</table>" ;
	
	return h;
	
}


function SelectEvent(NumEvent) {
	console.log("subevent_list[" + NumEvent+"]\n",subevent_list[NumEvent]);
	CurrentSubEventId = subevent_list[NumEvent]["id"];
	console.log("subevent_list\n = ",subevent_list);
	CurrentRating = subevent_list[NumEvent]["rating_type"]; 
	console.log("CurrentSubEventId = ",CurrentSubEventId);
	console.log("CurrentRating = ",CurrentRating);
	s.innerHTML = SubeventInfos2html(subevent_list[CurrentSubEvent]);
	r.innerHTML = RegList2htmltable (member_list, NumEvent);
}

function BuildHTMLEventSelector (n){
	/* 	Builds the set of number one can click on to select subevent */
	/* 	Should not be called if NbSubEvents =1 */
	
	let html_string="<div class='E4M_buttonset'>";
	let k=0;
	let sel = document.getElementById('E4M_select_event');
	
	for (k=0; k<=n-1; k++) {
		html_string += "<div onclick=SelectEvent(" + k + ") ><p>" + (k+1) + "</p></div>";
	}
	html_string +="</div>";
	sel.innerHTML = html_string;
}