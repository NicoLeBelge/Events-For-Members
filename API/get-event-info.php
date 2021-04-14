<?php
/*
needs id of event as input
returns un json containing
	- event data (name, date, ...)
	- subevents data
	- list of registered members for all subevents

returns json of object like this  :
Obj["infos"] = Array(1) of Objects  
	Event["0"] = {id="1", name = "eventname", datestart ="blabla",...}
Obj["subs"] = Array (n) of Objects  
	Sub[0] = {id="u", name = "subeventname", ...}
	Sub[1] = {id="v", name = "subeventname", ...}
	...
	Sub[n-1] = {id="w", name = "subeventname", ...}
	+...
*/


include('../../_local-connect/connect.php');
if (isset($_GET['event'])) { // returns dummy line if no string to search
	$event_id = $_GET['event'];
	$qtxt = "SELECT * from events where id=$event_id";
	$reponse = $conn->query($qtxt);
	$event_set = array();
	$event_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	//$qtxt = "SELECT * from subevents where event_id=$event_id";
	$reponse = $conn->query("SELECT * from subevents where event_id=$event_id");
	$event_set["subs"] = $reponse->fetchAll(PDO::FETCH_ASSOC);

	$qtxt = "SELECT subevents.event_id as eventid,
					subevents.id as subid,
					registrations.member_id as memberid,
					members.lastname,
					members.firstname,
					clubs.name as clubname,
					clubs.region
	FROM registrations
	INNER JOIN subevents
	ON registrations.subevent_id = subevents.id	
	INNER JOIN members
	ON registrations.member_id = members.id	
	INNER JOIN events
	ON events.id = subevents.event_id
	INNER JOIN clubs
	ON members.club_id = clubs.club_id
	WHERE subevents.event_id = $event_id";
	$reponse = $conn->query($qtxt);
	$event_set["registrations"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($event_set);

/*
$qtxt = "SELECT events.name as eventname,
					subevents.event_id as eventid,
					subevents.name as subenventname,
					subevents.id as subid,
					registrations.member_id as memberid,
					members.lastname,
					members.firstname,
					clubs.name as clubname,
					clubs.region
	FROM registrations
	INNER JOIN subevents
	ON registrations.subevent_id = subevents.id	
	INNER JOIN members
	ON registrations.member_id = members.id	
	INNER JOIN events
	ON events.id = subevents.event_id
	INNER JOIN clubs
	ON members.club_id = clubs.club_id
	WHERE subevents.event_id = $event_id";
*/



