<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );

	/** Let's check if event_id is valid (we're already know $_GET['id'] isset)
	 * if not let's write an error message
	 * if so, then let's check if current user is owner of this subevent
	 * abnormal access to this page is unlikely --> error messages in english
	 */
	$message="";
	$subeventId = $_GET['id'];
	$requete="SELECT owner 
	FROM events
	INNER JOIN subevents
	ON subevents.event_id = events.id	
	WHERE subevents.id=$subeventId";
	$res= $conn->query(htmlspecialchars($requete));
	$display_form = false;
	if ($res->rowCount() == 0) { // subevent not found
		$message="event_id $subeventId not found";
	} else {
		$owner = $res->fetch();
		if (! isset($_SESSION['user_id'])) {
			$message="you must be connected to access this page";
		} else { // visitor is connected
			if ($owner[0] <> $_SESSION['user_id']) {
				$message="Only owner of this subevent can edit it";
			} else { // visitor is the owner
				$current_IP = getIp();
				if ( $_SESSION['user_ip'] <> $current_IP ){	
					$message="IP has change since last login. Log out and log back in ";
				} else {
					$display_form = true;
				}
			}
		}
	}
	if ($message <>"") {
		echo "<h1>".$message."</h1>";
	}

	if ($display_form) {
		// warning - redundant with register-event-core - putting that in a function should be better
		$cfg = json_decode(file_get_contents('./json/config.json'),true);	
		$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
		$registration_search_page = json_encode($cfg['registration_search_page']); 
		$cat_names_str = json_encode($cfg['cat_names']);
		$gender_names_str = json_encode($cfg['gender_names']);
		$rating_names_str = json_encode($cfg['rating_names']);
		$type_names_str = json_encode($cfg['type_names']);

		$str = json_decode(file_get_contents('./json/strings.json'),true);	
		
		$requete="SELECT * FROM subevents WHERE id=$subeventId;";
		$res= $conn->query(htmlspecialchars($requete));
		$array_old = $res->fetch();
		$array_old_jsonstr = json_encode($array_old);

	}
	
?>
<?php if($display_form): ?>

<!-- debug - onchange="validate()" temporarily suppressed | should be added with JS and implemented in a customized way-->
<form action="" method="post">
	
	<label for="subname"><?=$str["subevent_name_label"]?></label>  
	<input type="text" id="subname" name="subname" />
	<div id="E4M_subevent_cat" class="E4M_catlist"></div>
	<div id="E4M_subevent_gen" class="E4M_catlist"></div>
	<div id="E4M_subevent_typ" class="E4M_catlist"></div>
	<br/>
	<label for="nbmax"> <?=$str["Max_reg"]?></label> <br/>
	<input type="number" id="nbmax" name="nbmax" />  <br/><br/>
	
	

	<label for="sublink"><?=$str["Label_link_to_sub"]?></label><br/>  
	<input type="text" id="sublink" name="sublink" /><br/>

	<label for="rating-select"><?=$str["Rating_name"]?></label><br/>
	<select id="rating-select" name="rating-select"><br/>
	</select><br/>
	<p><?=$str["restriction_apply?"]?></p>
	
	<label for="restriction_no"><?=$str["no"]?></label>
	<input type="radio" id="restriction_no" name="restriction" value="0" onchange="update_visibility()">
	<label for="restriction_yes"><?=$str["yes"]?></label>
	<input type="radio" id="restriction_yes" name="restriction" value="1" onchange="update_visibility()">
	
	<select id="comparator" name="comparator"><br/>
		<option value=">"><?=$str["restriction_>"]?></option>
		<option value="<"><?=$str["restriction_<"]?></option>
	</select>
	<input id="limit" name="limit" type="number">

	<p><input type="submit" value="<?=$str["Save_modications"]?>" id="submitButton"></p>
	
</form>
<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	function Toggle_on_off_class(e) {
		let element = e.target ;
		if (element.classList.contains("E4M_on")) {
			element.classList.remove("E4M_on");
			element.classList.add("E4M_off");
		} else {
			element.classList.remove("E4M_off");
			element.classList.add("E4M_on");
		}
	}
	function update_visibility() {
		if (document.getElementById("restriction_yes").checked) {
			document.getElementById("comparator").style.visibility = "visible";
			document.getElementById("limit").style.visibility = "visible";
		} else {
			document.getElementById("comparator").style.visibility = "hidden";
			document.getElementById("limit").style.visibility = "hidden";
		}

	}
	
	/* let's feed the button to go back to event */
	/*  -- problem : makes submit whereas we don't want to
	document.getElementById("back").addEventListener("click", function(e){
		e.stopPropagation();
		let destination = "register-event.php?id=" + array_old.event_id.toString(10);
		document.location.replace=destination;
	});
	 */
	/* let's put in fields values before modifications */
	let array_old = JSON.parse(`<?=$array_old_jsonstr?>`);
	console.log(array_old);
	document.getElementById("subname").value = array_old.name;
	document.getElementById("nbmax").value = array_old.nbmax;
	document.getElementById("sublink").value = array_old.link;
	document.getElementById("limit").value = array_old.rating_limit;
	const restrictionRadioYes = document.getElementById("restriction_yes")
	const restrictionRadioNo = document.getElementById("restriction_no")
	
	if (array_old.rating_restriction =="0") {
		restrictionRadioNo.checked = true;
	} else {
		restrictionRadioYes.checked = true;
		if (array_old.rating_comp ==">"){
			document.getElementById("comparator").options[0].selected = 'selected';
		} else {
			document.getElementById("comparator").options[1].selected = 'selected';
		}
	}
	update_visibility();
	
	/* rating_type selection */
	let e=document.getElementById("rating-select");
	console.log(e);
	var ratingOption = "";
	let rating_names = JSON.parse(`<?=$rating_names_str?>`);
	console.log(rating_names);
	let NbRatingStr = `<?=$cfg["Nb_rating"]?>`; 
	let NbRating = parseInt(NbRatingStr);
	for ( let i = 0; i <NbRating ; i++) {
		ratingOption += "<option value='" + (i+1).toString(10) + "'> " + rating_names[i]+ " </option>"; 
	}
	e.innerHTML = ratingOption;
	let rating_index = parseInt(array_old.rating_type)-1; 
	document.getElementById("rating-select").options[rating_index].selected = 'selected';
	
	/* selectors for category, gender and type */
	let cat_names = JSON.parse(`<?=$cat_names_str?>`);
	const cat_set = new IconSet (
		"E4M_subevent_cat", 
		cat_names,
		array_old.cat,
		"E4M_cat",
		true
	);

	let gender_names = JSON.parse(`<?=$gender_names_str?>`);
	const gen_set = new IconSet (
		"E4M_subevent_gen", 
		gender_names,
		array_old.gender,
		"E4M_gen",
		true
	);	
	
	let type_names = JSON.parse(`<?=$type_names_str?>`);
	const typ_set = new IconSet (
		"E4M_subevent_typ", 
		type_names,
		array_old.type,
		"E4M_typ",
		true
	);	

	const request = new XMLHttpRequest();
	const form = document.forms[0];
	form.addEventListener("submit", function(event) {
		event.preventDefault();
		const formData = new FormData(this);
		formData.append("event_id", array_old.event_id.toString(10));
		formData.append("subevent_id", array_old.id.toString(10));
		formData.append("cat_list", cat_set.Status());
		formData.append("gen_list", gen_set.Status());
		formData.append("typ_list", typ_set.Status());
		//const entries = formData.entries();
		//const data = Object.fromEntries(entries);
		request.open("POST", "./API/set-subevent-info.php");
		request.responseType = 'text';
		request.send(formData);
	});
	request.onreadystatechange  = function() {
		if (this.readyState == 4 && this.status == 200) {
			
			alert (`<?=$str["Modications_saved"]?>`); 
		}
	}
</script> 
<?php endif; ?>
