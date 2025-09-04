<?php
/*
duplicates the selected event and all its subevents
input  (POST): event_id
action authorized only if user_id (SESSION) = owner
*/

session_start();
$str = json_decode(file_get_contents('../_json/strings.json'),true);	

$response="";
/* cloning to be done if POST present and user_id is owner of event_id   */

$do_clone = false;
if ((isset($_POST['event_id']) && isset($_SESSION['user_id'])))
{ 
	include('../../_local-connect/connect.php');
	$eventId = $_POST['event_id'];
	$res= $conn->prepare("SELECT * FROM events WHERE id=?;");
	$res->execute([$eventId]);
	if ($res->rowCount() == 0) 
	{ // event not found
		$response="event not found";
	} else {
		/* event found - let's duplicate it */
		$event = $res->fetch();
		$req_TXT = "INSERT INTO events (name, datestart, datelim, secured, contact, nbmax, pos_long, pos_lat, owner, api_key) ";
		$req_TXT .= "VALUES (:n_name, :n_datestart, :n_datelim, :n_secured, :n_contact, :n_nbmax, :n_pos_long, :n_pos_lat, :n_owner, :n_api_key);";
		$req_new_event = $conn->prepare($req_TXT);
		
		$req_new_event->BindParam(':n_name', $new_name);
		$req_new_event->BindParam(':n_datestart', $event["datestart"]);
		$req_new_event->BindParam(':n_datelim', $event["datelim"]);
		$req_new_event->BindParam(':n_secured', $event["secured"]);
		$req_new_event->BindParam(':n_contact', $event["contact"]);
		$req_new_event->BindParam(':n_nbmax', $event["nbmax"]);
		$req_new_event->BindParam(':n_pos_long', $event["pos_long"]);
		$req_new_event->BindParam(':n_pos_lat', $event["pos_lat"]);
		$req_new_event->BindParam(':n_owner', $event["owner"]);
		// same owner → no need to generate new random api_key
		$req_new_event->BindParam(':n_api_key', $event["api_key"]);
		$new_name = $event["name"] . $str["Cloned_mark"];
		
		$req_new_event->execute();
		
		/* let's get the id of the newly created event */
		$req_TXT = "SELECT id from events ORDER BY id DESC LIMIT 1;";
		$new_event_req = $conn->query($req_TXT);
		$new_event = $new_event_req->fetch();
		$new_event_id = $new_event["id"];

		/* let's browse all subevents of cloned event */
		$old_subs_req = $conn->prepare("SELECT * from subevents WHERE event_id=?;");
		$old_subs_req->execute([$eventId]);

		$old_subs = $old_subs_req->fetchAll(PDO::FETCH_ASSOC);
		
		$req_TXT = "INSERT INTO subevents (event_id, name, nbmax, rating_type, gender, cat, type) ";
		$req_TXT .= "VALUES (:n_event_id, :n_name, :n_nbmax, :n_rating_type, :n_gender, :n_cat, :n_type);";
		$req_new_sub = $conn->prepare($req_TXT);
		$req_new_sub->BindParam(':n_event_id', $new_event_id);
		$req_new_sub->BindParam(':n_name', $sub_name);
		$req_new_sub->BindParam(':n_nbmax', $sub_nbmax);
		$req_new_sub->BindParam(':n_rating_type', $sub_rating_type);
		$req_new_sub->BindParam(':n_gender', $sub_gender);
		$req_new_sub->BindParam(':n_cat', $sub_cat);
		$req_new_sub->BindParam(':n_type', $sub_type);

		foreach ($old_subs as $old_sub) 
		{
			$ch = $old_sub["id"] . " " . $old_sub["rating_type"];
			$sub_name = $old_sub["name"];
			$sub_nbmax = $old_sub["nbmax"];
			$sub_rating_type = $old_sub["rating_type"];
			$sub_gender = $old_sub["gender"];
			$sub_cat = $old_sub["cat"];
			$sub_type = $old_sub["type"];
			$req_new_sub->execute();
		}
		$response =  strval($new_event_id);
	}
} else {
	$response =  "wrong call to this API";
}
sleep(1); // is it really necessary ?
echo $response;