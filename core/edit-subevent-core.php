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
	echo "<br/>";
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
					var_dump ($_SESSION['user_ip']); echo "<br/>";
					var_dump ($current_IP);

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
		$jsonstr = json_encode($str);	
		$requete="SELECT * FROM subevents WHERE id=$subeventId;";
		$res= $conn->query(htmlspecialchars($requete));
		$array_old = $res->fetch();
		$array_old_jsonstr = json_encode($array_old);

	}
	
?>
<?php if($display_form): ?>

<!-- debug - onchange="validate()" temporarily suppressed | should be added with JS and implemented in a customized way-->
<form action="./core/editsubevent-action-core.php" method="post">
	<label for="subname"><?=$str["subevent_name_label"]?></label>  
	<input type="text" id="subname" name="subname" />

	<label for="nbmax"> <?=$str["Max_reg"]?></label> <br/>
	<input type="number" name="nbmax" />  <br/><br/>
	
	<label for="rating-select"><?=$str["Rating_name"]?></label><br/>
	<select id="rating-select"><br/>
		
	</select><br/>

	<label for="sublink"><?=$str["Label_link_to_sub"]?></label><br/>  
	<input type="text" id="sublink" name="sublink" /><br/>

	

	<p>Appliquer des restriction de classement ? </p>
	<label for="restriction_yes">oui</label>
	<input type="radio" id="restriction_yes" name="restriction" value="oui">
	<label for="restriction_no">non</label>
	<input type="radio" id="restriction_no" name="restriction" value="non">
	<div id="E4M_subevent_cat" class="E4M_catlist"></div>


	<p><input type="submit" value="OK" id="submitButton"></p>
	
</form>
<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	let array_old = JSON.parse(`<?=$array_old_jsonstr?>`);

	document.getElementById("subname").value = array_old.name;
	let e=document.getElementById("rating-select");
	var ratingOption = "";
	let rating_names = JSON.parse(`<?=$rating_names_str?>`);
	console.log(rating_names);
	let NbRatingStr = `<?=$cfg["Nb_rating"]?>`; 
	let NbRating = parseInt(NbRatingStr);
	
	console.log("NbRatingStr = ",NbRatingStr);
	for ( let i = 0; i <NbRating ; i++) {
		ratingOption += "<option value='" + i.toString(10)+1 + "'> " + rating_names[i]+ " </option>"; 
	}
	e.innerHTML = ratingOption;

	let cat_names = JSON.parse(`<?=$cat_names_str?>`);

	var cat_set = new IconSet (
		"E4M_subevent_cat", 
		cat_names,
		//subs_data_set[CurrentSubEventIndex].cat,
		"*",
		"E4M_cat",
		false
	);

	const form = document.forms[0];
	form.addEventListener("submit", function(event) {
		event.preventDefault();
		const formData = new FormData(this);
		formData.append("gender", "*");
		const entries = formData.entries();
		const data = Object.fromEntries(entries);
		//console.log(formData.subname.value);
		//console.log(data);
		// const { subname, nbmax } = this.elements;
		
		//console.log(subname.value, nbmax.value);
	});

	
	
</script> 
<?php endif; ?>
