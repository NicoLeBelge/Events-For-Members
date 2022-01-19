<?php
/*
deletes an event or a subevent
input  (POST): event_id or subevent_id
action authorized only if user_id (SESSION) = owner
*/
/* note : many lines in common with set-subevent-info.php --> code optimization recommended */
session_start();
function getIp()
	{
		/*
			Action : This function returns the visitor\'s IP address 
			Return : $ip - String  
		*/
		if(!empty($_SERVER['HTTP_CLIENT_IP']))	$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
	}
$response="";

$do_delete = false;
if ((isset($_POST['event_id']) || isset($_POST['subevent_id']) )  && isset($_SESSION['user_id'])) { 
	/* we have data to check if deleteion request is made by the owner */
	$pathbdd = '../../_local-connect/connect.php';
	include($pathbdd);
	if (isset($_POST['event_id'])){
		$eventId = $_POST['event_id'];
		$requete="SELECT owner FROM events WHERE id=$eventId;";
	} else {
		$subeventId = $_POST['subevent_id'];
		$requete="SELECT owner FROM events 
					INNER JOIN subevents
					ON subevents.event_id = events.id		
					WHERE subevents.id=$subeventId;";
	}
	
	$res= $conn->query(htmlspecialchars($requete));
	if ($res->rowCount() == 0) { // event not found
		$response="event or subevent not found";
	} else {
		$owner = $res->fetch();
		if ($owner[0] <> $_SESSION['user_id']) {
			$response="Only owner of this subevent can edit it";
		} else { // visitor is the owner
			$current_IP = getIp();
			if ( $_SESSION['user_ip'] <> $current_IP ){	
				$message="IP has change since last login. Log out and log back in ";
			} else {
				$do_delete = true;
				$response = "OK";
			}
		}
	}
} else {
	$response =  "wrong call to this API";
}
if ($do_delete){
	if (isset($_POST['event_id'])){
		$req=$conn->prepare("DELETE from events WHERE id=:eid LIMIT 1;");
		$req->BindParam(':eid', $eventId);
	} else {
		$req=$conn->prepare("DELETE from subevents WHERE id=:sid LIMIT 1;");
		$req->BindParam(':sid', $subeventId);
	}
    $req->execute();
	$response = "deletion done";
}
sleep(1);
echo $response;