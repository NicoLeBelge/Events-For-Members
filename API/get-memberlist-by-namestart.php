<?php
/*
Temporary code --> to be improved, adapted and commented
input : string | eg ...php?start=SMI
output : array of members who's lastnames matches with the searched strings
if 
*/
$n=1;  // default rating
$nauth = array(1,2,3,4,5,6);
$rating = "";
include('../../_local-connect/connect.php');
if (!isset($_GET['start'])) { // returns dummy line if no string to search
	$matchlist['idffe']='------'; // dummy data to be deleted or adapted
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
	$rating = "rating"."$n";	
	$qtxt = "	SELECT members.id as id, fede_id, lastname, firstname, $rating as rating, cat, member_type, gender, clubs.id as id_club, clubs.name as club_name, clubs.city as city, clubs.region as reg
				FROM members 
				LEFT JOIN clubs
				ON members.club_id = clubs.club_id
				where lastname like '$start%' order by lastname LIMIT 25" ;
	$reponse = $conn->query($qtxt);
	$matchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
	if (count($matchlist)==25){
		/* 	
		limit has restricted the result 
		The result has to be replaced if the result with exact name is longer than 25
		*/
		$rating = "rating"."$n";	
		$qtxt = "	SELECT members.id as id, fede_id, lastname, firstname, $rating as rating, cat, member_type, gender, clubs.id as id_club, clubs.name as club_name, clubs.city as city, clubs.region as reg
					FROM members 
					LEFT JOIN clubs
					ON members.club_id = clubs.club_id
					where lastname='$start' ORDER BY firstname" ;
		$reponse = $conn->query($qtxt);
		$perfectmatchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
		if (count($perfectmatchlist)>25){
			$matchlist = $perfectmatchlist;
		}
	}
}
echo json_encode($matchlist);
