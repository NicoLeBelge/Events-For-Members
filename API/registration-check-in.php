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
	$sql="	SELECT registrations.member_id, subevents.name, events.name, events.code
				FROM registrations
				INNER JOIN subevents
				ON subevents.id = registrations.subevent_id
				INNER JOIN events
				ON events.id = subevents.event_id
				WHERE registrations.id=?;";
	$stmt = $conn->prepare(htmlspecialchars($sql));
	$stmt->execute([$reg_id]);

	if ($stmt->rowCount() == 0) 
	{ // event not found
		$response = $str["reg_not_found"];
	} else {
		$dataset = $stmt->fetch();
		if ($dataset["code"] !== strtoupper($_POST['code'])) {
			$response = $str["wrong_code"];
			$match = false;
		} else {
			$response = "OK";
			$match = true;
		}
		if ($match) 
		{
			//$requete="UPDATE `registrations` SET `present`=1 where id=$reg_id;";  //before prepare
			$res= $conn->prepare(htmlspecialchars("UPDATE `registrations` SET `present`=1 where id=?;"));
			$res->execute([$reg_id]);

		}
	}
} else {
	$response =  "wrong call to this API";
}
echo $response;




