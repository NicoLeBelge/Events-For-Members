<?php
/*
change the status of a registration
input  (POST)	: registration id (reg_id)
				action_code (c | d)
c : confirmed = false --> true
d  --> delete registration
u : change registration status wait 1 --> 0
action authorized only if user_id (SESSION) = owner
*/
session_start();
$do_change = false;
if (isset($_POST['reg_id'])  && isset($_SESSION['user_id'])) { 
	$uid = $_SESSION['user_id'];
	$regid = $_POST['reg_id'];
	$action = $_POST['action_code'];
	$do_change = true;

} else {
	echo "this page can only be called from register-change-status page";
}
if ($do_change){
	include('../../_local-connect/connect.php');
	$stmt = $conn->prepare("SELECT id, wait, confirmed FROM registrations WHERE id = ?");
	$stmt->execute([$regid]);
	
	if ($regline = $stmt->fetch(PDO::FETCH_ASSOC)) 
	{ 
		switch ($action){
			case 'c':
				/* change status confirmed to 1 */
				$req=$conn->prepare("UPDATE registrations SET confirmed=1 WHERE id = ?");
				$req->execute([$regid]);
				echo "$regid confirmation effectuée";
				break;
			case 'd':
				/* delete registration */
				$req=$conn->prepare("DELETE FROM registrations WHERE id = ?");
				$req->execute([$regid]);
				echo "suppression effectuée";
				break;
			case 'u':
				/* unwait registration */
				$req=$conn->prepare("UPDATE registrations SET wait=0 WHERE id = ?");
				$req->execute([$regid]);
				echo "suppression effectuée";
				break;
		}
	} else {
		echo "registration not found";
	}
}

