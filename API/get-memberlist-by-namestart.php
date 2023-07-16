<?php
/*
page called with GET input being the beginning of the name searched
returns the list of all members who's name starts with the input  
input : string | eg ...php?start=SMI
output : array of members (several data, including rating provided in GET) who's lastname matche with the searched strings
if no match, returns dummy array so that the caller doesn't receive NULL string.
*/
$n=1;  // default rating
$nauth = array(1,2,3,4,5,6); // What if NbRating change ??
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
	$sql = "	SELECT members.id as id, fede_id, lastname, firstname, $rating as rating, cat, member_type, gender, clubs.id as id_club, clubs.name as club_name, clubs.city as city, clubs.region as reg
				FROM members 
				LEFT JOIN clubs
				ON members.club_id = clubs.club_id
				WHERE lastname LIKE ? order by lastname LIMIT 25;" ;
	$stmt = $conn->prepare($sql);
	$stmt -> bindValue(1, $start . '%', PDO::PARAM_STR);
	$stmt -> execute();
	$matchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($matchlist)==25){
		/* 	
		The limit has restricted the result, which is a problem if the user cannot fine-tune because he has entered the exact name. 
		The result has to be replaced by full answer if the result with exact name is longer than 25
		*/
		$rating = "rating"."$n";	
		$qtxt = "	SELECT members.id as id, fede_id, lastname, firstname, $rating as rating, cat, member_type, gender, clubs.id as id_club, clubs.name as club_name, clubs.city as city, clubs.region as reg
					FROM members 
					LEFT JOIN clubs
					ON members.club_id = clubs.club_id
					where lastname=? ORDER BY firstname" ;
		$reponse = $conn->prepare($qtxt);
		$reponse -> bindValue(1, $start, PDO::PARAM_STR);
		$reponse ->execute();
		
		$perfectmatchlist = $reponse->fetchAll(PDO::FETCH_ASSOC);
		if (count($perfectmatchlist)>25){
			$matchlist = $perfectmatchlist;
		}
	}
}
echo json_encode($matchlist);
