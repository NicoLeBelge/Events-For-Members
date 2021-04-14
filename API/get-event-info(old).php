<?php
include('../../_local-connect/connect.php');
if (isset($_GET['event'])) { // returns dummy line if no string to search
	$event_id = $_GET['event'];
	$qtxt = "SELECT * from events where id=$event_id";
	$reponse = $conn->query($qtxt);
	$matchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
	
	$sublist = array();
	$sublist[]=1;
	$sublist[]=3;
	$sublist[]=8;
	$matchlist["list_subs"]=$sublist;
}
echo json_encode($matchlist);
