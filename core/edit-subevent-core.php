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
				if ( $_SESSION['user_ip'] <> getIp() ){	
					$message="IP has change since last login. Log out and log back in";
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
		$requete="SELECT * FROM subevents WHERE id=$subeventId;";
		$res= $conn->query(htmlspecialchars($requete));
		$array_old = $res->fetch();
		var_dump($array_old["name"]);

	}
	
?>
<?php if($display_form): ?>
<h1>is_owner is true</h1>

<!-- debug - onchange="validate()" temporarily suppressed | should be added with JS and implemented in a customized way-->
<form action="./core/editsubevent-action-core.php" method="post">
	<label for="subname">Nom du subevent :</label>  
	<input type="text" id="subname" name="subname" />
	
	<p>__________________________________________</p>
	
	<input type="number" name="nbmax" /><label for="nbmax"> joueurs maximum</label>  
	
	<p>__________________________________________</p>
	<label for="pet-select">Choose a pet:</label>
	<select id="pet-select">
		<option value="">--Please choose an option--</option>
		<option value="dog">Dog</option>
		<option value="cat">Cat</option>
		<option value="hamster">Hamster</option>
		<option value="parrot">Parrot</option>
		<option value="spider">Spider</option>
		<option value="goldfish">Goldfish</option>
	</select>
	<p>__________________________________________</p>

	<label for="sublink">lien</label>  
	<input type="text" id="sublink" name="sublink" />

	<p>__________________________________________</p>
	<label for="rating_type">classement</label>  
	<input type="text" id="rating_type" name="rating_type" />

	<p>__________________________________________</p>
	<p>Appliquer des restriction de classement ? </p>
	<label for="restriction_yes">oui</label>
	<input type="radio" id="restriction_yes" name="restriction" value="oui">
	<label for="restriction_no">non</label>
	<input type="radio" id="restriction_no" name="restriction" value="non">
	


	<p><input type="submit" value="OK" id="submitButton"></p>
	
</form>

<script type="text/javascript">
	
	let e=document.getElementById("subname");
	e.value=`<?=$array_old['name']?>`;

	const form = document.forms[0];
	form.addEventListener("submit", function(event) {
		event.preventDefault();
		const formData = new FormData(this);
		formData.append("gender", "*");
		const entries = formData.entries();
		const data = Object.fromEntries(entries);
		//console.log(formData.subname.value);
		console.log(data);
		// const { subname, nbmax } = this.elements;
		
		//console.log(subname.value, nbmax.value);
	});

	
	
</script> 
<?php endif; ?>
