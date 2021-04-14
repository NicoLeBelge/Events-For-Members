<?php
$n=1;  // default rating
$nauth = array(1,2,3,4,5,6);
$rating = "";
include('../_local-connect/connect.php');
if (!isset($_GET['start'])) { // returns dummy line if no string to search
	$matchlist['idffe']='------';
	$matchlist['nom']='correspondance';
	$matchlist['prenom']='aucune';
	$matchlist['elo']=0;
	$matchlist['cat']='---';
	$matchlist['club']='---';
	$matchlist['ville']='---';
} else {
	$start = $_GET['start'];
	if (isset($_GET['ratn'])) {
		if (!in_array($_GET['ratn'], $nauth)){
			$n=1;
		} else {
			$n=$_GET['ratn'];
		}
	}
	var_dump($n);
	$rating = "rat"."$n";	
		
	
	
	$qtxt = "	SELECT members.id, fede_id, lastname, firstname, $rating, cat, clubs.id, clubs.name, clubs.city
				FROM members 
				LEFT JOIN clubs
				ON members.club_id = clubs.id
				where nom like '$start%' order by nom LIMIT 25" ;
	$reponse = $conn->query($qtxt);
	$matchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($matchlist);
