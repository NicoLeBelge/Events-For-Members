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
	//var_dump($sublist);echo"<br/>";
	
	$matchlist["list_subs"]=$sublist;
}
//echo"<pre>";var_dump($matchlist);echo"</pre>";
//echo"<br/>";
//echo"<br/>";

echo json_encode($matchlist);
