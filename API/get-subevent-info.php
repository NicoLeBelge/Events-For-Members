<?php
/*
input : subevent id | e.g. ...php?sub=12
output : json containing information of selected subevent 
returns json of object like this  :
Obj_sub = {id="u", name = "subeventname", ...}
*/

$json = file_get_contents('../json/config.json'); // gets Nb_rating (1, 2, ... or 6)
$cfg = json_decode($json,true);	

include('../../_local-connect/connect.php'); // PDO connection required
if (isset($_GET['sub'])) { 
	$subevent_id = $_GET['sub'];
	$qtxt = "SELECT * from subevents where id=$subevent_id";
	$reponse = $conn->query($qtxt);
	//$subevent_set = array();
	//$subevent_set["infos"] = $reponse->fetchAll(PDO::FETCH_ASSOC);
	$subevent_set = $reponse->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($subevent_set);