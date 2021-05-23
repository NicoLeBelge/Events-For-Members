<?php
/*
page to be included in a php page (register-check.php or any name chosen by admin - see config.json)
input (POST) : subevent id + member id
outpu : registration of the member for the mentionned subevent
*/

/* lets get strings from json folder (strings displayed and configuration strings) */
// debug --> enlever ce qui est inutile
$json = file_get_contents('./json/config.json'); 
$cfg = json_decode($json,true);	

$subevent_link_icon_str = json_encode($cfg['subevent_link_icon']);
$registration_check_page = json_encode($cfg['registration_check_page']); // debug --> à garder
$cat_names_str = json_encode($cfg['cat_names']);
$gender_names_str = json_encode($cfg['gender_names']);
$rating_names_str = json_encode($cfg['rating_names']);
$type_names_str = json_encode($cfg['type_names']);
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$jsonstr = json_encode($str);	
$subs_data_set_str = $_SESSION['subs_data_set'];

/* this page is supposed to be called with event id and member id */
var_dump($_POST['member_id']);
var_dump($_POST['sub_id']);
if(isset($_POST['member_id']) && isset($_POST['sub_id'])){ 
	$subevent_id = $_POST['sub_id'];
	$member_id = $_POST['member_id'];
	var_dump($subevent_id);
	var_dump($member_id);
	$message="OK";
} else {
	echo "Unothorized access to this page";
}
?>

<div class='E4M_maindiv'>
<div id="E4M_message"></div>
</div>
<script src="./JS/E4M.js"></script>
<script type="text/javascript">
/*
vérifier que
	le joueur n'est pas déjà inscrit dans ce tournoi
	l'email est fourni si tournoi sécurisé 
 */
	var message = `<?=$message?>`;
	
	document.getElementById('E4M_message').innerHTML = "<p>" + message + "</p>";
	
</script>
