<?php
/*
page to be included in a php page (register-check.php or any name chosen by admin - see config.json)
input (POST) : subevent id + member id
output : registration of the member for the mentionned subevent if constraints respected
constraints checked : number of registrations for event/subevent + member not registred already.
*/

/* lets get strings from json folder (strings displayed and configuration strings) */
include('./include/str-tools.php');
$json = file_get_contents('./_json/config.json'); 
$cfg = json_decode($json,true);	

$registration_check_page = json_encode($cfg['registration_check_page']); // debug --> à garder
$full_margin = intval($cfg['Full_margin'],10);

$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$type_names_str = json_encode($cfg['type_names']);
$json = file_get_contents('./_json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	

/* this page must be called with event id and member id */

if(isset($_POST['member_id']) && isset($_POST['sub_id'])){ 
	$subevent_id = $_POST['sub_id'];
	$member_id = $_POST['member_id'];
	//var_dump($_POST);
	if (isset($_POST['member_email'])) {
		$member_email = $_POST['member_email'];
	}
	
	include('../_local-connect/connect.php'); 
	// get information about selected subevent
	$qtxt = "SELECT subevents.name as subname, 
					events.nbmax as e_nbmax,
					subevents.nbmax as s_nbmax,
					events.id as e_id,
					events.name as eventname,
					events.secured as secured
			FROM subevents
			INNER JOIN events
			ON events.id = subevents.event_id
			WHERE subevents.id=?";
	$stmt = $conn->prepare($qtxt);
	$stmt->execute([$subevent_id]);
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC); // to be changed by fetch since only one record expected
	$eventname = $data[0]["eventname"];
	$subname = $data[0]["subname"];
	$secured = $data[0]["secured"];
	$s_nbmax = intval($data[0]["s_nbmax"], 10); // zero if null
	$e_nbmax = intval($data[0]["e_nbmax"], 10); // zero if null
	$e_id = $data[0]["e_id"];
	$destination=$cfg["event_page"] . "?id=" .$e_id;
	
	$html_message ="<h3>" . $eventname ."</h3>";
	$html_message .="<h4>" . $subname . "</h3>";
	// catch name of member 
	$stmt = $conn->prepare("SELECT firstname, lastname FROM members WHERE id=?");
	$stmt->execute([$member_id]);
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC); // to be replaced by fetch since only one record expected
	$fullname = $data[0]["firstname"] . " " . $data[0]["lastname"];
	$html_message.= "<h5>" . $fullname ."</h5>";
	// select registrations of this member in the selecte subevent to ensure single registration
	$qtxt = "SELECT registrations.id, 
					confirmed 
			FROM registrations
			INNER JOIN members
			ON members.id = registrations.member_id
			INNER JOIN subevents
			ON subevents.id = registrations.subevent_id
			WHERE member_id=:bind_member_id 
			AND subevent_id=:bind_subevent_id";
	$stmt = $conn->prepare($qtxt);
	$stmt->bindParam('bind_member_id', $member_id, PDO::PARAM_INT); 
	$stmt->bindParam('bind_subevent_id', $subevent_id, PDO::PARAM_INT); 
	$stmt->execute(); 
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // to be replaced by fetch since only one record expected
	if (count($result) <>0 ) 
	{ 
		//member already registered in this subevent
		$confirmed=($result[0]["confirmed"]=="1");
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
		$stmt = $conn->prepare("SELECT COUNT(id) as tot_sub FROM registrations WHERE subevent_id=?");
		$stmt->execute([$subevent_id]);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // to be replaced by fetch since only one record expected
		$tot_sub=intval($data[0]["tot_sub"],10);
		// count the number of registration in the event (and in the subevent ??)
		$qtxt = "SELECT	count(member_id) as count_members 
				FROM registrations
				INNER JOIN subevents
				ON subevents.id = registrations.subevent_id	
				INNER JOIN events
				ON events.id = subevents.event_id	
				WHERE events.id=?";
		$stmt = $conn->prepare($qtxt);
		$stmt->execute([$e_id]);
		
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC); // to be replaced by fetch since only one record expected
		$tot_evt=intval($data[0]["count_members"],10);
		
		$sub_full = ($s_nbmax > 0 && ($tot_sub >= $s_nbmax));
		$evt_full = ($e_nbmax > 0 && ($tot_evt >= $e_nbmax));
		
		if ($sub_full || $evt_full){
			$wait=true;
			$html_message.= "<p>" . $str["Full"]."</p>";
		} else {
			// maybe almost full --> warning if secured event
			$wait=false;
			if ($secured){
				$sub_almost_full = ($s_nbmax > 0 && ($tot_sub >= $s_nbmax - $full_margin));
				$evt_almost_full = ($e_nbmax > 0 && ($tot_evt >= $e_nbmax - $full_margin));
				if ($sub_almost_full || $evt_almost_full){
					$html_message.= "<p>" . $str["Almost_full"]."</p>";
					$html_message.= "<p>" . $str["Hurry_up"]."</p>";
				}
			}
		}
		
		$req=$conn->prepare("INSERT INTO registrations (member_id, 
										subevent_id, 
										confirmed, 
										wait, 
										code, 
										email) 
						VALUES (:new_member,
								:new_sub, 
								:new_confirmed,
								:new_wait,
								:new_code,
								:new_email)");
		$req->BindParam(':new_member', $newmember); 
		$req->BindParam(':new_sub', $newsub);
		$req->BindParam(':new_confirmed', $newconfirmed);
		$req->BindParam(':new_wait', $newwait);
		$req->BindParam(':new_code', $newcode);
		$req->BindParam(':new_email', $newemail);
		$newmember = $member_id;
		$newsub = $subevent_id;
		if($secured){
			$newcode = RandomString(10);
			$newconfirmed=0;
		} else {
			$newcode = "--";
			$newconfirmed=1;
		}
		if (isset($_POST["email_to_owner"]))
		{
			$allow = ($_POST["email_to_owner"] == "on") ;
		} else {
			$allow = false;
		}
		
		$newemail = (isset($member_email) && $allow) ? $member_email : null;
		 
		$newwait = $wait? 1 : 0;
		$req->execute();	
		if ($wait) {
			$html_message.= "<p>" . $str["Registration_wait"]."</p>";
		} else {
			$html_message.= "<p>" . $str["Registration_OK"]."</p>";
		}
		if ($secured) {
			$html_message.= "<p>" . $str["e-mail_sent_to"] . "<strong>" . $member_email . "</strong></p>";
			$html_message.= "<p>" . $str["Confirm_mail"]."</p>";
		}
		// send e-mail if event secured
		if($secured){
			// $newcode = RandomString(10);
			$mailto= $member_email;
			
			
		$from  = $cfg["e-mail_from"];  // adresse MAIL related to webhosting.
		$JOUR  = date("Y-m-d");
		$HEURE = date("H:i");
		$mailobject = $str["confirmation_mail_subject"]; 
		$ReplyTo = $cfg["e-mail_reply"];
		$link =	$cfg["domain"] . $cfg["registration_confirm_page"] . "?code=". $newcode;
		$conf_message = $str["confirmation_mail_message"];
		
		$mailmessage = "<p> $eventname </p>";
		$mailmessage .= "<p> $subname </p>";
		$mailmessage .= "<p> $fullname </p>";
		$mailmessage .= "<p> $conf_message </p>";
		if ($wait) {
			$mailmessage .= "<p>" . $str["Full"] . "</p>";
			$mailmessage .= "<p>" . $str["Waiting_list"] . "</p>";
		}
		$mailmessage .="<a href='$link'>$link</a>";
		$mail_Data = $mailmessage;
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=UTF-8 \n";
		$headers .= "From: $from  \n";
		$headers .= "Reply-To: $ReplyTo  \n";
		$CR_Mail = TRUE;
		$CR_Mail = @mail ($mailto, $mailobject, $mail_Data, $headers);
		if ($CR_Mail === FALSE)   echo " Error mail \n";
		}
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
	let link = `<?=$link?>`;
	console.log(link);
	var html_message = `<?=$html_message?>`;
	
	document.getElementById('E4M_message').innerHTML = html_message;
	
</script>
