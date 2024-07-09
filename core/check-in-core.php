<?php
	$pathbdd = '../_local-connect/connect.php';
	include($pathbdd);
	$str = json_decode(file_get_contents('./_json/strings.json'),true);	
	$ID = $_GET['id']; 
	if (strlen($ID) > 6) {
		$ID= "1"; 
		echo "weird value for id ...";
	}	
	$requete="SELECT members.firstname, members.lastname
			FROM members
			INNER JOIN registrations
			ON members.id = registrations.member_id
			WHERE registrations.id=?;";
	$res=$conn->prepare($requete);
	$res->execute([$ID]);
	$member = $res->fetch();
	$firstname = $member["firstname"];
	echo "<h1>$firstname</h1>";
	$lastname = $member["lastname"];
	echo "<h1>$lastname</h1>";
?>
<div class="E4M_check-in E4M_bigbutton">
	<form id="form" action="#" onsubmit="intercept(event)">
		<label for="code"><?=$str["enter_code"]?></label>
		<br>
		<input type="text" autocomplete="off" name="code" id="code" onkeyup="EnableDisable(this)" required>
		<br>
	</form> 
	
	<button id="checkinButton"><?=$str["I_am_here"]?></button>
	<br>
	<button id="Oops_btn" onclick="history.back()"><?=$str["oops_check_in"]?></button>

	<br>
	<h1 id="result"></h1>
	<img id="picto" src="" alt="">
</div>
<script>
	window.onload = () => document.getElementById("checkinButton").disabled=true; // disables check-in button
	function EnableDisable(text_input) 
	{
 		document.getElementById("checkinButton").disabled = (text_input.value.trim() == "");
    };

	let CheckinButton = document.getElementById('checkinButton');
	const request = new XMLHttpRequest();
	CheckinButton.addEventListener("click", function(event) 
	{
		const formData = new FormData();
		formData.append("reg_id", <?=$ID?>);
		formData.append("code", document.getElementById("code").value);
		request.open("POST", "./API/registration-check-in.php");
		request.responseType = 'text';
		request.send(formData);
		request.onreadystatechange  = function() {
			if (this.readyState == 4 && this.status == 200) {
				let result = document.getElementById("result");
				
				let resultOK = (request.response == "OK" );
				if (resultOK) {
					document.getElementById("form").hidden = true;
					document.getElementById("checkinButton").hidden = true;
					document.getElementById("picto").src = "./_img/tick.png";
					result.innerHTML = `<?=$str["you_are_in"]?>`;
				} else {
					result.innerHTML = request.response;
					document.getElementById("picto").src = "./_img/cross.png";
				}
				document.getElementById("Oops_btn").hidden = true;
			}
		}
	});
	function intercept(e){
		/* form enter check-in code : don't reload page on ENTER + click on check-in button*/
		e.preventDefault();
		document.getElementById("checkinButton").click();
	}
</script>
