<?php
/*
input : event id | e.g. ...php?event=12
output : json containing full information of selected event 
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
Obj["registrations"] = Array (m) of registred members
	[{
	"eventid":"1", "subid":"6","memberid":"x","lastname":"x","firstname":"x",
	"rating1":"x",...,"ratingn":"x","clubname":"Grasse Echecs","region":"PAC"
	},{...}]

*/
$json = file_get_contents('../_json/config.json'); // gets Nb_rating (1, 2, ... or 6)
$cfg = json_decode($json,true);	

include('../../_local-connect/connect.php'); // PDO connection required
if (isset($_GET['event'])) { 
	$event_id = $_GET['event'];
	$qtxt = "SELECT * from events where id=$event_id";
	$reponse = $conn->query($qtxt);
	$event_set = array();
	$event_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	//$qtxt = "SELECT * from subevents where event_id=$event_id";
	$reponse = $conn->query("SELECT * from subevents where event_id=$event_id");
	$event_set["subs"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	$ratinglist="";
	for ($k=1;$k<=$cfg['Nb_rating'];$k++){
		$ratinglist .= "members.rating$k, ";
	}
	$qtxt = "SELECT subevents.event_id as eventid,
					subevents.id as subid,
					registrations.member_id as memberid,
					registrations.confirmed,
					members.fede_id,
					members.lastname,
					members.firstname,
					members.firstname,
					$ratinglist
					clubs.name as clubname,
					clubs.region as region
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