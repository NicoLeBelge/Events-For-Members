<?php
include('../../_local-connect/connect.php');
if (!isset($_GET['event'])) {
	$matchlist['idffe']='------';
	$matchlist['nom']='correspondance';
	$matchlist['prenom']='aucune';
	$matchlist['elo']=0;
	$matchlist['cat']='---';
	$matchlist['club']='---';
	$matchlist['ville']='---';
	
} else {
	$start = $_GET['start'];
	$qtxt = "	SELECT 	events.name,
				subevents.event_id,
				subevents.name,
				subevents.id as subid,
				registrations.member_id,
				members.lastname,
				clubs.name,
				clubs.region
			FROM registrations
			INNER JOIN subevents
				ON registrations.subevent_id = subevents.id	
			INNER JOIN members
				ON registrations.member_id = members.id	
			INNER JOIN events
				ON events.id = subevents.event_id
			INNER JOIN clubs
				ON members.club_id = clubs.club_id" ;
	$reponse = $conn->query($qtxt);
	$matchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($matchlist);



