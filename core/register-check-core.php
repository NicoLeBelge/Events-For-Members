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

// $subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_check_page = json_encode($cfg['registration_check_page']); // debug --> à garder
$full_margin = intval($cfg['Full_margin'],10);

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
	$member_email = $_POST['member_email'];
	
	include('../_local-connect/connect.php'); 
	
	$qtxt = "SELECT subevents.name as subname, 
					events.nbmax as e_nbmax,
					subevents.nbmax as s_nbmax,
					events.id as e_id,
					events.name as eventname,
					events.secured as secured
			FROM subevents
			INNER JOIN events
			ON events.id = subevents.event_id
			WHERE subevents.id=$subevent_id";
	$result = $conn->query($qtxt);
	$data = $result->fetchAll(PDO::FETCH_ASSOC);
	$eventname = $data[0]["eventname"];
	$subname = $data[0]["subname"];
	$secured = $data[0]["secured"];
	$s_nbmax = intval($data[0]["s_nbmax"], 10); // zero if null
	$e_nbmax = intval($data[0]["e_nbmax"], 10); // zero if null

	
	$e_id = $data[0]["e_id"];
	$destination=$cfg["event_page"] . "?id=" .$e_id;
	
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
		$confirmed=($data[0]["confirmed"]=="1") ? true : false;
		if ($confirmed) {
			$html_message.= "<p>" . $str["Already_confirmed_OK"]."</p>";
		} else {
			$html_message .="<p>" . $str["Already_registered"] ."</p>";
			$html_message.= "<p>" . $str["Waiting_confirmation"]."</p>";
			$html_message.= "<p>" . $str["Check_spams"]."</p>";
			$html_message.= "<p>" . $str["Pb_contact_organizer"]."</p>";
		}

	} else {
		/* Before registering the member, we check if there is room in the event / subents */
		$result = $conn->query("SELECT COUNT(id) as tot_sub FROM registrations WHERE subevent_id=$subevent_id");
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		//var_dump($data);
		$tot_sub=intval($data[0]["tot_sub"],10);
		
		$qtxt = "SELECT	count(member_id) as count_members 
				FROM registrations
				INNER JOIN subevents
				ON subevents.id = registrations.subevent_id	
				INNER JOIN events
				ON events.id = subevents.event_id	
				WHERE events.id=$e_id ";
		$result = $conn->query($qtxt);
		
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		$tot_evt=intval($data[0]["count_members"],10);
		
		$sub_full = ($s_nbmax > 0 && ($tot_sub >= $s_nbmax)) ? true : false;
		$evt_full = ($e_nbmax > 0 && ($tot_evt >= $e_nbmax)) ? true : false;
		if ($sub_full || $evt_full){
			$html_message.= "<p>" . $str["Full"]."</p>";
		} else {
			// maybe almost full --> warning if secured event
			if ($secured){
				$sub_almsot_full = ($s_nbmax > 0 && ($tot_sub >= $s_nbmax - $full_margin)) ? true : false;
				$evt_almsot_full = ($e_nbmax > 0 && ($tot_evt >= $e_nbmax - $full_margin)) ? true : false;
				if ($sub_almost_full || $evt_almost_full){
					$html_message.= "<p>" . $str["Almost_full"]."</p>";
					$html_message.= "<p>" . $str["Hurry_up"]."</p>";
				}
			}
		}
		
		$req=$conn->prepare("INSERT INTO registrations (member_id, subevent_id, confirmed, code, email) 
						VALUES (:new_member,
								:new_sub, 
								:new_confirmed,
								:new_code,
								:new_email)");
		$req->BindParam(':new_member', $newmember); 
		$req->BindParam(':new_sub', $newsub);
		$req->BindParam(':new_confirmed', $newconfirmed);
		$req->BindParam(':new_code', $newcode);
		$req->BindParam(':new_email', $newemail);
		$newmember = $member_id;
		$newsub = $subevent_id;
		if($secured){
			$newcode = RandomString(10);
		} else {
			$newcode = "--";
		}
		$newemail=$member_email;
		/*$link = "https://www.chessmooc.org/web/login/signup-end.php?code=$newcode";*/
		$newconfirmed = $secured? 0 : 1;
		
		$req->execute();	
		
		$html_message.= "<p>" . $str["Registration_OK"]."</p>";
		
	}		
 
	
} else {
	echo "Unauthorized access to this page";
}

?>

<div class='E4M_maindiv'>
<div id="E4M_message"></div>

<br/>
<a href="<?=$destination?>"><button><?=$str["Back_to_event"]?></button></a>
</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">

	var html_message = `<?=$html_message?>`;
	
	document.getElementById('E4M_message').innerHTML = html_message;
	
</script>
