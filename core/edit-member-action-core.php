<?php
session_start();
// echo "<pre>";
// var_dump($_POST);	
// echo "</pre>";
if (!isset($_SESSION['user_id']))
{
echo "this code can only be run by a connected user";
exit();
}
$user_id = $_SESSION['user_id'];
//echo "user = ". $user_id . "<br>";
$pathbdd = './../_local-connect/connect.php';
include($pathbdd );
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	

foreach ($_POST as $key => $value)
{
	if ($value == '') $value=NULL;
	switch ($key) 
	{
		case 'fede_id': 
			$fede_id = htmlspecialchars($value);
			//echo "fede_id = $fede_id <br/>"; //debug
			break;
		case 'member_firstname':
			$firstname = htmlspecialchars($value);
			//echo "firstname = $firstname <br/>";//debug
			break;
		case 'member_lastname':
			$lastname = htmlspecialchars($value);
			//echo "lastname = $lastname <br/>";//debug
			break;
		case 'mtype':
			$index = strval($value) - 1 ;
			$member_type = $cfg["type_names"][$index];
			//echo "type = $member_type <br/>";//debug
			break;
		case 'gender':
			$index = strval($value) - 1 ;
			$gender = $cfg["gender_names"][$index];
			//echo "type = $gender <br/>";//debug
			break;
		default:
		break;

	}
}

$update_mode = ($_POST['mode'] == 'u');
// {
// 	echo "<b>update mode </b><br/>";
// 	$update_mode=true;
// } else {
// 	echo "<b>create mode </b><br/>";
// 	$update_mode=false;
// }

if (!$update_mode) {
	/* check that provided fede_id does not already exist */
	$sql = "SELECT fede_id, lastname, firstname FROM members WHERE fede_id = ?";
	$stmt = $conn->prepare ($sql);
	$stmt -> execute ([$_POST['fede_id']]);
	if ($member = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$member_fede_id = $_POST['fede_id'];
		$member_firstname = $member["firstname"];
		$member_lastname = $member["lastname"];
		echo "Federal ID $member_fede_id already exists for $member_firstname $member_lastname ";
	} 
exit();

}


$rating_update_str="";
$rating_insert_str_name="";
$rating_insert_str_value="";
for ($i=0; $i<$cfg['Nb_rating'];$i++)
{
	$rating_update_str .= "rating" . strval($i+1) . " = :newrating" . strval($i+1) . ", ";
	$rating_insert_str_name .= "rating" . strval($i+1) . ", ";
	$rating_insert_str_value .= ":newrating" . strval($i+1) . ", ";
}
// echo $rating_update_str . "<br/>";
// echo $rating_insert_str_name . "<br/>";
// echo $rating_insert_str_value . "<br/>";

if ($update_mode) 
{ /* owner can change everything but fede_id */
	$sql = "UPDATE members SET
			firstname = :newfirstname,
			lastname = :newlastname,
			$rating_update_str
			m_owner = :newm_owner,
			member_type = :new_mtype,
			gender = :new_gender,
			upd_date = :new_date
			WHERE fede_id=:old_fede_id;
			";
	$stmt = $conn->prepare ($sql);
	$stmt -> BindParam(':old_fede_id', $fede_id); 
	// echo $sql . "<br/>";	
	
	// $stmt->execute(); //let's see if we can execute outside the if
} else {
	/* creation mode */
	$sql = "INSERT INTO members (fede_id, firstname,lastname, $rating_insert_str_name m_owner, member_type, gender, upd_date, club_id) 
			VALUES (:new_fede_id, :newfirstname, :newlastname, $rating_insert_str_value :newm_owner, :new_mtype, :new_gender, :new_date, :new_club )
			";
	$dummy_club = 0;
	$stmt = $conn->prepare ($sql);
	$stmt -> BindParam(':new_club', $dummy_club); 
	$stmt -> BindParam(':new_fede_id', $fede_id); 
}

$stmt -> BindParam(':newfirstname', $firstname); 
$stmt -> BindParam(':newlastname', $lastname); 
$stmt -> BindParam(':newm_owner', $user_id); 
$stmt -> BindParam(':new_mtype', $member_type); 
$stmt -> BindParam(':new_gender', $gender); 
$stmt -> BindParam(':new_date', $today_now_TXT); 


for ($i=0; $i<$cfg['Nb_rating'];$i++)
{
	$rating_n = 'rating' . strval($i+1);
	$param_to_bind = ':new' . $rating_n;
	$stmt -> BindParam($param_to_bind, $_POST[$rating_n]); 
	//echo $param_to_bind . " = " . $_POST[$rating_n] . "<br>";
}
$today_now = new DateTime("now");
$today_now_TXT = $today_now->format("Y-m-d h:i:s");
$success = $stmt->execute();
if ($success)
{
	echo "OK";
} else {
	echo "not saved";
}

// reste à veiller à ne pas créer de doublon sur le fede_id.
