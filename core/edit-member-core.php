<?php
/*
This page allows to create (from scratch or from GET parameters) or edit new member 
if GET parameters 'id' provided
 	then existing member with id is used for default values.
	else we use hardcode defined parameters unless GET is specified (f=firstname, l=lastname, fid=fede_id)
action is called with POST parameter 'mode' = e for edit or c for create (destination page will check if fede_id does not already exist)
if called by non connected user, cancel and display error message
the page is process if the user is connected / user_id is put in hidden field of form to store origin of member
*/

$pathbdd = './../_local-connect/connect.php';
include($pathbdd );
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	
$NbRating = $cfg["Nb_rating"];

if (isset($_GET['id']))
{
	$id = $_GET['id'];
	$mode = 'u';
	$update_mode = true;
} else {
	$mode = 'c'; // with our without default parameters
	$update_mode = false;
	$fede_id = isset($_GET['fede_id']) ? $_GET['fede_id'] : "";
	$firstname = isset($_GET['firstname']) ? $_GET['firstname'] : "";
	$lastname = isset($_GET['lastname']) ? $_GET['lastname'] : "";

}

/* creates array with rating names */
$rating_arr = array();
for ($i = 1; $i<=$NbRating; $i++) 
{
	$key = "rating". strval($i);
	$rating_data=array();
	$rating_data["rating_name"] = $cfg["rating_names"][$i-1];
	$rating_data["database_id"] = "rating" . strval($i);
	$rating_data["rating_value"] = 0;
	array_push($rating_arr, $rating_data);
}

/* creates array with types */
$type_arr = array();
for ($i=0; $i<count($cfg["type_names"]); $i++) 
{
	$type_data=array();
	$type_data["number"]=$i+1;
	$type_data["name"] = $cfg["type_names"][$i];
	array_push($type_arr, $type_data);
}
// echo "<pre>";
// var_dump($type_arr);
// echo "</pre>";

/* creates array with genders */
$gender_arr = array();
for ($i=0; $i<count($cfg["gender_names"]); $i++) 
{
	$gender_data=array();
	$gender_data["number"]=$i+1;
	$gender_data["name"] = $cfg["gender_names"][$i];
	array_push($gender_arr, $gender_data);
}

/* creates array with categories */
$cat_arr = array();
for ($i=0; $i<count($cfg["cat_names"]); $i++) 
{
	$cat_data=array();
	$cat_data["number"] = $i+1;
	$cat_data["name"] = $cfg["cat_names"][$i];
	// if ( $cat == $cfg["cat_names"][$i]) {
	// 	$cat_data["select"] = "selected";
	// } else {
	// 	$cat_data["select"] = "";
	// }
	array_push($cat_arr, $cat_data);
}
$gender=$cfg["gender_names"][0];
$mtype=$cfg["type_names"][0];


if ($update_mode) 
{
	/* we get data from selected member and we overwrite default values*/
	$stmt= $conn->prepare("SELECT *  FROM `members` WHERE id=?;");
	$stmt->execute([$id]);
	if ($member = $stmt->fetch(PDO::FETCH_ASSOC)) 
	{
		$member_found = true;
		$firstname = $member["firstname"];
		$lastname = $member["lastname"];
		$fede_id = $member["fede_id"];
		$cat = $member["cat"];
		$gender = $member["gender"];
		$mtype = $member["member_type"];
		
		// get rating
		for ($i = 1; $i<=$NbRating; $i++) {
			$key = "rating". strval($i);
			$rating_arr[$i-1]["rating_value"] = $member[$key];
		}
		
		/* prepare the input select for gender */
		for ($i=0; $i<count($cfg["gender_names"]); $i++) 
		{
			if ( $cfg["gender_names"][$i] == $gender) {
				$gender_arr[$i]["select"] = "selected";
			} else {
				$gender_arr[$i]["select"] = "";
			}
		}
		
		/* prepare the selected category */
		for ($i=0; $i<count($cfg["cat_names"]); $i++) 
		{
			if ( $cfg["cat_names"][$i] == $cat) {
				$cat_arr[$i]["select"] = "selected";
			} else {
				$cat_arr[$i]["select"] = "";
			}
		}
		
		/* prepare the selected type */
		for ($i=0; $i<count($cfg["type_names"]); $i++) 
		{
			if ( $cfg["type_names"][$i] == $mtype) {
				$cat_arr[$i]["select"] = "selected";
			} else {
				$cat_arr[$i]["select"] = "";
			}
		}

	} else {
		$member_found = false; 
		echo "member not found with this id";
		exit();
	}
} else {
	/* set default values from GET if provided */
	if (isset($_GET['first'])) $firstname=urldecode($_GET['first']);
	if (isset($_GET['last'])) $lastname=urldecode($_GET['last']);
	if (isset($_GET['fid'])) $fede_id=urldecode($_GET['fid']);
}
?>

<form action="./edit-member-action.php" method="post">
	<div><label for="fede_id"><?=$str["fede_id"] ?></label></div>
	<div><input type="text" id="fede_id" name="fede_id" value="<?=$fede_id?>"required ></div>
	
	<div><label for="member_firstname"><?=$str["firstname"] ?></label></div>
	<div><input type="text" id="member_firstname" name="member_firstname" maxlength="80" value="<?=$firstname ?>"required ></div>

	<div><label for="member_lastname"><?=$str["lastname"] ?></label>  </div>
	<div><input type="text" id="member_lastname" name="member_lastname" maxlength="80" value="<?=$lastname ?>"required ></div>

<?php foreach ($rating_arr as $rating): ?>
	<div><label for="<?=$rating["database_id"] ?>"><?=$rating["rating_name"] ?></label>   </div>
	<div><input type="number" name="<?=$rating["database_id"] ?>" value=<?=$rating["rating_value"] ?> max="3000" /> </div>
<?php endforeach; ?>

<div><label for="cat"><?=$str["Category"] ?></label>  </div>
<select name="cat" id="cat"> 
<?php foreach ($cat_arr as $cat): ?>
	<option <?=$cat['select'] ?> value="<?=$cat['number'] ?>"><?=$cat['name'] ?></option>
<?php endforeach; ?>
</select>

<div><label for="mtype"><?=$str["Type_header"] ?></label>  </div>
<select name="mtype" id="mtype"> 
<?php foreach ($type_arr as $member_type): ?>
	<option <?=$member_type['select'] ?> value="<?=$member_type['number'] ?>"><?=$member_type['name'] ?></option>
<?php endforeach; ?>
</select>


<div><label for="gender"><?=$str["Gender"] ?></label>  </div>
<select name="gender" id="gender"> 
<?php foreach ($gender_arr as $gender): ?>
	<option <?=$gender['select'] ?> value="<?=$gender['number'] ?>"><?=$gender['name'] ?></option>
<?php endforeach; ?>
</select>

<div><input type="submit" value="Sauvegarder" id="submitButton"></div>
<div><input id="owner_id" name="owner_id" type="hidden" value=12></div>
<div><input id="mode" name="mode" type="hidden" value=<?=$mode ?>></div>
</form>

