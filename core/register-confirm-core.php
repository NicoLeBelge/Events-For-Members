<?php
/*
page to be included in a php page (register-confirm.php or any name chosen by admin - see config.json)
input (GET) : code
output : set confirm = 1 in registrations table
*/

if (!isset($_GET['code'])) {
	echo "This page can only be called with valid code as parameter";
} else {
	/*
	 Check that code exists
	 if not --> message
	 else 
			if not already confirmed  --> confirmation + message
			else --> message  
	 */
	$json = file_get_contents('./_json/strings.json');
	$str = json_decode($json,true);	
	$json = file_get_contents('./_json/config.json'); 
	$cfg = json_decode($json,true);	
	$code = $_GET['code'];
	include('../_local-connect/connect.php');
	
	$qtxt = "SELECT registrations.id, 
					confirmed,
					code,
					wait,
					members.firstname as first,
					members.lastname as last,
					subevents.name as subname,
					events.name as evtname,
					events.id as e_id
			FROM registrations
			INNER JOIN members
			ON members.id = registrations.member_id
			INNER JOIN subevents
			ON subevents.id = registrations.subevent_id
			INNER JOIN events
			ON events.id = subevents.event_id
			WHERE code='$code'";
	/**WHERE ; */
	$result = $conn->query($qtxt);
	if ($result->rowCount() == 0) { 
		echo "<p>Code de confirmation inconnu </p>";
		} 
	else {
		// code found. Let's get data from the database
		// and set confirm=1 if not yet confirmed, else print message
		$data = $result->fetch();
		$e_id = $data["e_id"];
		$destination=$cfg["event_page"] . "?id=" .$e_id;
		
		$html_message ="<h3>" . $data["evtname"] ."</h3>";
		$html_message .="<h4>" . $data["subname"] . "</h3>";
		$html_message.= "<h5>" . $data["first"] . " " . $data["last"] ."</h5>";
		$confirmed = ($data["confirmed"]<>"0");
		$wait = ($data["wait"]<>"0");
		$database_update=false;
		if ($confirmed){
			if ($wait){
				$html_message.= "<p>" . $str["Already_confirmed_wait"] ."<p>";
			} else {
				$html_message.= "<p>" . $str["Already_confirmed_OK"] ."<p>";
			}
		} else {
			if ($wait){
				$html_message.= "<p>" . $str["Already_confirmed_wait"] ."<p>";
			} else {
				$html_message.= "<p>" . $str["Registration_confirmed"] ."<p>";
				$database_update=true;
			}
		}
		if ($database_update){
			$req=$conn->prepare("UPDATE registrations 
								SET confirmed = :new_confirmed
								WHERE code='$code'");
			// debug et si je mets 1 directement ??
			$req->BindParam(':new_confirmed', $newconfirmed);
			$newconfirmed = 1;
			$req->execute();	
		}
	}
}

?>
<div class='E4M_maindiv'>
<div id="E4M_message"></div>

<br/>
<a href="<?=$destination?>"><button><?=$str["Back_to_event"]?></button></a>
</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">

	let html_message = `<?=$html_message?>`;
	
	document.getElementById('E4M_message').innerHTML = html_message;
	
</script>