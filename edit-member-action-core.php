<?php
$pathbdd = './../_local-connect/connect.php';
include($pathbdd );
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	
/*
comment
*/
foreach ($_POST as $key => $value)
{
	//echo "$key = $value <br/>";
	if ($value == '') $value=NULL;
	switch ($key) 
	{
		case 'fede_id':
			$fede_id = htmlspecialchars($value);
			echo "fede_id = $fede_id <br/>";
			break;
		case 'member_firstname':
			$firstname = htmlspecialchars($value);
			echo "firstname = $firstname <br/>";
			break;
		case 'member_lastname':
			$lastname = htmlspecialchars($value);
			echo "lastname = $lastname <br/>";
			break;
		
		
		default:
		break;

	}
}
if ($_POST['mode'] == 'e')
echo "euh, y'a pas crash, là ?";
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

{
	$sql = "UPDATE members SET
			firstname = :newfirstname,
			lastname = :newlastname,
			$rating_update_str
			
	
	";
}

/*
case update
UPDATE members SET 
firstname = :newfirstname,
...
WHERE fede_id=?

case insert
INSERT INTO events (name, datestart, datelim, secured, contact, nbmax, pos_long, pos_lat, owner) ";
$req_TXT .= "VALUES (:n_name, :n_datestart, :n_datelim, :n_secured, :n_contact, :n_nbmax, :n_pos_long, :n_pos_lat, :n_owner);";
*/


echo "<pre>";
var_dump($_POST);	
echo "</pre>";

