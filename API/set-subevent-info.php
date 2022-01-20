<?php
/*
change the data of a subevent
input  (POST)	: subevent data(id, event_id,...)
action authorized only if user_id (SESSION) = owner
*/
session_start();
function getIp()
	{
		/*
			Action : This function returns the visitor\'s IP address 
			Return : $ip - String  
		*/
		if(!empty($_SERVER['HTTP_CLIENT_IP']))	$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
	}
$response="";
$do_change = false;
if (isset($_POST['event_id'])  && isset($_SESSION['user_id'])) { 
	/* we have data to check if modification request is made by the owner */
	$pathbdd = '../../_local-connect/connect.php';
	include($pathbdd);
	$eventId = $_POST['event_id'];
	$subeventId = $_POST['subevent_id'];
	$requete="SELECT owner FROM events WHERE id=$eventId";
	$res= $conn->query(htmlspecialchars($requete));
	if ($res->rowCount() == 0) { // event not found
		$response="event_id $eventId not found";
	} else {
		$owner = $res->fetch();
		if ($owner[0] <> $_SESSION['user_id']) {
			$response="Only owner of this subevent can edit it";
		} else { // visitor is the owner
			$current_IP = getIp();
			if ( $_SESSION['user_ip'] <> $current_IP ){	
				$message="IP has change since last login. Log out and log back in ";
			} else {
				$do_change = true;
				$response = "OK";
			}
		}
	}
} else {
	$response =  "wrong call to this API";
}
if ($do_change){
	var_dump($_POST);
	
	//$new_name = $_POST["subname"];
	$new_name = str_replace('"', "'", $_POST['subname']);
	$new_nbmax = ($_POST["nbmax"] == "") ? NULL : intval($_POST["nbmax"],10);
	$new_link = ($_POST["sublink"] == "") ? NULL : $_POST["sublink"];
	$new_rating = $_POST["rating-select"];
	$new_restriction = ($_POST["restriction"] == "1") ? 1 : 0;
	$new_comp = $_POST["comparator"];
	$new_limit = ($_POST["limit"] == "") ? NULL : intval($_POST["limit"],10);
	$new_rating_type = $_POST["rating-select"];
	$new_cat = $_POST["cat_list"];
	$new_gender = $_POST["gen_list"];
	$new_type = $_POST["typ_list"];

	$req=$conn->prepare("UPDATE subevents 
						SET 
							name = :new_name,
							nbmax=:new_nbmax,
							rating_type=:new_rating,
							rating_restriction=:new_rating_restriction,
							rating_comp=:new_comp,
							rating_limit=:new_limit,
							rating_type=:new_rating_type,
							cat=:new_cat,
							gender=:new_gender,
							type=:new_type,
							link = :new_link
						WHERE id = :target_id
						LIMIT 1;");  
	
		$req->BindParam(':new_name', $new_name);
		$req->BindParam(':new_link', $new_link);// no need to add PDO::PARAM_NULL, 
		$req->BindParam(':new_nbmax', $new_nbmax);  
		$req->BindParam(':target_id', $subeventId);
		$req->BindParam(':new_rating', $new_rating);
		$req->BindParam(':new_rating_restriction', $new_restriction);
		$req->BindParam(':new_comp', $new_comp);
		$req->BindParam(':new_limit', $new_limit);
		$req->BindParam(':new_rating_type', $new_rating_type);
		$req->BindParam(':new_cat', $new_cat);
		$req->BindParam(':new_gender', $new_gender);
		$req->BindParam(':new_type', $new_type);

        $req->execute();
	}
echo $response;