<?php
/*
page to be included in a php page (register-check.php or any name chosen by admin - see config.json)
input (POST) : subevent id + member id
output : registration of the member for the mentionned subevent if constraints respected
constraints checked : number of registrations for event/subevent + member not registred already.
*/

/* lets get strings from json folder (strings displayed and configuration strings) */
// debug --> enlever ce qui est inutile

include('./include/str-tools.php');
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	

$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_check_page = json_encode($cfg['registration_check_page']); // debug --> à garder
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$type_names_str = json_encode($cfg['type_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	

/* this page must be called with event id and member id */
/** Registration_of */

if(isset($_POST['member_id']) && isset($_POST['sub_id'])){ 
	$subevent_id = $_POST['sub_id'];
	$member_id = $_POST['member_id'];
	include('../_local-connect/connect.php'); 
	
	
	$qtxt = "SELECT subevents.name as subname, 
					events.name as eventname
			FROM subevents
			INNER JOIN events
			ON events.id = subevents.event_id
			WHERE subevents.id=$subevent_id";
	$result = $conn->query($qtxt);
	$data = $result->fetchAll(PDO::FETCH_ASSOC);
	$eventname = $data[0]["eventname"];
	$subname = $data[0]["subname"];
	$html_message ="<h3>" . $eventname ."</h3>";
	$html_message .="<h4>" . $subname . "</h3>";
	
	$result = $conn->query("SELECT firstname, lastname FROM members WHERE id=$member_id");
	$data = $result->fetchAll(PDO::FETCH_ASSOC);
	$fullname = $data[0]["firstname"] . " " . $data[0]["lastname"];
	$html_message.= "<h5>" . $fullname ."</h5>";
	$qtxt = "SELECT registrations.id, 
					confirmed 
			FROM registrations
			INNER JOIN members
			ON members.id = registrations.member_id
			INNER JOIN subevents
			ON subevents.id = registrations.subevent_id
			WHERE member_id=$member_id 
			AND subevent_id=$subevent_id";
	$result = $conn->query($qtxt);
	if ($result->rowCount() !== 0) { 
		//member already registered in this subevent
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		$registered = true;
		
		$confirmed=($data[0]["confirmed"]=="1") ? true : false;
		if ($confirmed) {
			$html_message.= "<p>" . $str["Already_confirmed"]."</p>";
		} else {
			$html_message .="<p>" . $str["Already_registered"] ."</p>";
			$html_message.= "<p>" . $str["Waiting_confirmation"]."</p>";
			$html_message.= "<p>" . $str["Check_spams"]."</p>";
			$html_message.= "<p>" . $str["Pb_contact_organizer"]."</p>";
		}

	} else {
		// recover subevent.name and events.secured
		$qtxt = "SELECT	subevents.event_id as eventid,
						subevents.name as subname,
						subevents.id as subid,
						events.secured as sec
				FROM subevents
				INNER JOIN events
				ON subevents.event_id = events.id	
				WHERE subevents.id = $subevent_id";
		$result = $conn->query($qtxt);
		//$data = array();
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		var_dump($data);
		echo"<br/>";
		$secured_str = $data[0]["sec"];
		//$secured_str = $data["sec"];
		$is_secured = ($secured_str == "1") ? true : false; 
		$subname = $data[0]["subname"];
		//$subname = $data["subname"];
		echo "<br/>" ; var_dump($secured_str); 
		echo "<br/>" ; var_dump($is_secured); 
		echo "<br/>" ; var_dump($subname); 
		/* Nombre d'inscriptions dans le subevent */
		
		$result = $conn->query("SELECT COUNT(id) as totsub FROM registrations WHERE subevent_id=$subevent_id");
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		var_dump($data);
		$totsubb=$data[0]["totsub"];

		
		$qtxt = "SELECT	count(member_id) as cid 
				FROM registrations
				INNER JOIN subevents
				ON subevents.id = registrations.subevent_id	
				INNER JOIN events
				ON events.id = subevents.event_id	
				WHERE events.id=1";
		$result = $conn->query($qtxt);
		//$data = array();
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		$totevent=$data[0]["cid"];
		
		
		$subevent_name=$data[0]["subname"];
		$event_secured=$data[0]["sec"];

		$req=$conn->prepare("INSERT INTO registrations (member_id, subevent_id, confirmed, code) 
						VALUES (:new_member,
								:new_sub, 
								:new_confirmed,
								:new_code)");
		$req->BindParam(':new_member', $newmember); // debug --> faudra quand même voir si on peut pas simplifier !!!
		$req->BindParam(':new_sub', $newsub);
		$req->BindParam(':new_confirmed', $newconfirmed);
		$req->BindParam(':new_code', $newcode);
		$newmember = $member_id;
		$newsub = $subevent_id;
		//$newcode = RandomString(10);
		$newcode = "pipocode";
		//$link = "https://www.chessmooc.org/web/login/signup-end.php?code=$newcode";
		$newconfirmed = $event_secured? 1 : 0;
		echo "$newconfirmed :";
		var_dump($newconfirmed);
		$req->execute();	
		$html_message="inscription enregistrée avec succès, affiner le html_message.";
		
	}		
 
	
} else {
	echo "Unothorized access to this page";
}

?>

<div class='E4M_maindiv'>
<div id="E4M_message"></div>
</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">
/*
vérifier que
	le joueur n'est pas déjà inscrit dans ce tournoi
	l'email est fourni si tournoi sécurisé 
	le nombre max d'inscrits n'est pas atteint
	le nombre total d'inscrits n'est pas atteint
 */
	var html_message = `<?=$html_message?>`;
	
	document.getElementById('E4M_message').innerHTML = html_message;
	
</script>
