<?php
/*
input  (POST): event.code + registrations.id
returns "OK" if reg_id exists and code matches with related event.
returns error message otherwize
*/

$str = json_decode(file_get_contents('../_json/strings.json'),true);	// to get error message
$response="";

if ((isset($_POST['code']) && isset($_POST['reg_id']))) // if we have POST, we check the database
{ 
	include('../../_local-connect/connect.php');
	$reg_id = $_POST['reg_id'];
	$requete="	SELECT registrations.member_id, subevents.name, events.name, events.code
				FROM registrations
				INNER JOIN subevents
				ON subevents.id = registrations.subevent_id
				INNER JOIN events
				ON events.id = subevents.event_id
				WHERE registrations.id=$reg_id;";
	$res= $conn->query(htmlspecialchars($requete));
	if ($res->rowCount() == 0) 
	{ // event not found
		$response="registration_id not found";
	} else {
		$dataset = $res->fetch();
		if ($dataset["code"] !== $_POST['code']) {
			$response = "Le code ne correspond pas !";
		} else {
			$response = "OK";
		}
		$requete="UPDATE `registrations` SET `present`=1 where id=$reg_id;";
		$res= $conn->query(htmlspecialchars($requete));
	}
} else {
	$response =  "wrong call to this API";
}
echo $response;




