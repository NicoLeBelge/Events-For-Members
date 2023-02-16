<?php
	$pathbdd = './../_local-connect/connect.php';
	include($pathbdd);
	$str = json_decode(file_get_contents('./_json/strings.json'),true);	
	$ID = $_GET['id']; 
	$requete="SELECT members.firstname, members.lastname
			FROM members
			INNER JOIN registrations
			ON members.id = registrations.member_id
			WHERE registrations.id=$ID;";
	$res= $conn->query(htmlspecialchars($requete));
	$member = $res->fetch();
	$firstname = $member["firstname"];
	echo "<h1>$firstname</h1>";
	$lastname = $member["lastname"];
	echo "<h1>$lastname</h1>";
?>

<form id="form" action="#" >
	<label for="code">Entrez le code de pointage</label>
	<input type="text" autocomplete="off" name="code" id="code" required>
</form> 
<div class="E4M_bigbutton">
	<button id="checkinButton">Je suis présent !</button>
</div>
<br>
<h1 id="result"></h1>
<img id="picto" src="" alt="">
<script>
	let CheckinButton = document.getElementById('checkinButton');
	const request = new XMLHttpRequest();
	CheckinButton.addEventListener("click", function(event) {
		const formData = new FormData();
		formData.append("reg_id", <?=$ID?>);
		formData.append("code", document.getElementById("code").value);
		request.open("POST", "./API/registration-check-in.php");
		request.responseType = 'text';
		request.send(formData);
		request.onreadystatechange  = function() {
			if (this.readyState == 4 && this.status == 200) {
				let result = document.getElementById("result");
				
				let resultOK = (request.response == "OK" ) ? true : false;
				if (resultOK) {
					document.getElementById("form").hidden = true;
					document.getElementById("checkinButton").hidden = true;
					document.getElementById("picto").src = "./_img/tick.png";
					result.innerHTML = "Vous êtes pointé !";
					
				} else {
					result.innerHTML = request.response;
					document.getElementById("picto").src = "./_img/cross.png";
				}
			}
		}
	});
</script>
