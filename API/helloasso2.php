<?php
/**
 * API qui reçoit le JSON envoyé par Helloasso. 
 * Les data à traiter sont dans un JSON envoyé dans le body du POST (1).
 * On quitte si on n'a pas de numéro du tournoi (t) en paramètre GET (2). Si oui, on suppose que ça correspond bien au tournoi de l'organisateur.
 * On crée un tableau p_arr (3) contentant chaque joueur présent dans le JSON, ainsi que le subevent destinataire ("" si non défini)
 * On récupère (4) dans $subevent_arr la liste des subevents pour l'event passé en paramètre.
 * Pour chaque joueur de p_arr (5)
 * - on construit un message qui sera envoyé dans le mail.
 * - on cherche (6) dans la base PUCE le joueur qui correspond au numéro de licence
 * - Si le joueur n'est pas trouvé
 *  	alors info dans message {{{ à compléter par lien permettant d'ajouter le joueur }}}
 * 		sinon on évalue le degré de match (7) entre le nom saisi et le nom dans la base puis
 * 		on valide l'insertion dans le tournoi si ça match à plus de 0.5 (picto OK ou warning), on rejete sinon.
 * 		Si validé, on cherche (8) l'id du subevent où le joueur est à inscrire puis
 * 		si le subevent n'est pas trouvé (9a)
 *			alors on indique un message d'erreur
 *			sinon vérifie (9b) qu'il n'y est pas déjà inscrit. 
 * 			Si c'est bien le cas, on l'ajoute (10)
 * 
 */

function LogData ($pdo, $comment) {
	$add_req=$pdo->prepare("INSERT INTO logs (comment) VALUES (:n_comment);");
	if (strlen($comment) > 80 ) {
		$comment = substr($comment, 0, 80);
	}
	$add_req->BindParam(':n_comment', $comment);
	$add_req->execute();
}

include ('hello-tools.php');
include('../../_local-connect/connect.php'); // PDO connection required
if (is_null($_GET['key'])) //  (2)
{
	$LogMessage .= 'Helloasso APIL called without key';
	echo $LogMessage;
	LogData ($conn, $LogMessage);
	exit();
} else {
	$url_key = $_GET['key'];
}


$debugmode = false;
$Message = "";
$BR = "<br />";
$one = 1;
$is_event_GET = true; 
$event_OK = true; // event GET parameter correspond à un event avec un nom (mandatory)

if (is_null($_GET['t'])) //  (2)
{
	$LogMessage .= 'Helloasso API called without event id';
	echo $LogMessage;
	LogData ($conn, $LogMessage);
	exit();
} else {
	$event_id_str = $_GET['t'];
}

// exit if no api_key
if (is_null ($_GET['key'])){
	$LogMessage = 'Helloasso API called without api_key';
	LogData ($conn, $LogMessage);
	echo $LogMessage;
	exit();
}

	
/* regardons si c'est une notification de type payer (à traiter) ou order (à ignorer) json */
/* On quitte si on ne trouve pas de clé 'order' dans le json */
$json_full = json_decode(file_get_contents('php://input'));  // (1)
$json_eventType = $json_full->eventType;
//$json_data = $json_full->data;

$json_type = json_decode(file_get_contents('php://input'))->data;  // (1)
$type = $json_type->order ?? "NA"; // si on n'a pas de 'order' (alors c'est 'payer') → type=NA → on poursuit

if ($json_eventType <> "Order") {
	$LogMessage = "Notification de type Payer → ignored";
	LogData ($conn, $LogMessage);
	echo $LogMessage;
	exit();
}


/* construisons l'array p_arr à partir du json */
$items_arr = $json_full->data->items;  // (1)

$p_arr = item_array_to_player_array($items_arr);
//var_dump($p_arr);
//exit();
/* récupérons les données de l'event à traiter */
$stmt = $conn->prepare("SELECT name, contact, api_key FROM events WHERE id=:event_id_str LIMIT 1;");
$stmt->bindParam(':event_id_str', $event_id_str);
$stmt->execute();
$event_data = $stmt->fetch(PDO::FETCH_ASSOC);
$event_name = $event_data['name']; 
$event_contact = $event_data['contact']; 
$api_key = $event_data['api_key']; 

// exit if event doesn't exist
if (is_null ($event_name)){
	$LogMessage = "event" . $event_id_str . " not found";
	LogData ($conn, $LogMessage);
	echo $LogMessage;
	exit();
}
	
// exit if wrong api_key
if ($_GET['key'] <> $api_key){
	$LogMessage = "event " . $event_id_str . " and api_key " . $_GET['key'] .  " mismatch";
	LogData ($conn, $LogMessage);
	echo $LogMessage;
	exit();
}


/* récupérons les données des subevents */
$stmt = $conn->prepare("SELECT id, event_id, name FROM subevents WHERE event_id=:event_id_str;");
$stmt->bindParam(':event_id_str', $event_id_str);
$stmt->execute();
$subevent_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
$NbSubs = sizeof($subevent_arr);
if ($NbSubs == 0) 
{
	$Message .= "Festival sans tournoi (event=" . $event_id_str . ") -> abandon <br />";
	$event_OK = false;
}


/* let's loop on each player from Helloasso*/

$mail_obj = "";
if ($debugmode) echo "\n** event OK, on va maintenant boucler sur chaque joueur  ******\n";


foreach($p_arr as $p) // (5)
{
	$Message .= "<hr>";
	$ValidPlayer = true;
	$AddPlayer = true;
	$mail_obj .= $p["firstName"] . " " . $p["lastName"];
	$Message .= "$event_name : " . $p["firstName"] . " " . $p["lastName"];
	$Message .= " (" . $p["licence"] . ") à ajouter " ;
	if ($p["subevent"] != "") 
	{
		$Message .= " dans " . $p["subevent"] .  $BR;
	} else {
		$Message .=  $BR;
	}
	
	/* let's check if licence exists and name matches */
	/* parameters come from the database, no need to use prepared statement for security */
	/* but we never know, licence might be corrupted ! */
	$qtxt = "SELECT id, fede_id, firstname, lastname from members where fede_id='" . $p["licence"] . "';";

	// echo $qtxt; echo "<br />";
	$reponse = $conn->query($qtxt);
	$ffe_player = $reponse->fetch(PDO::FETCH_ASSOC);
	
	if ($debugmode) var_dump($ffe_player); // false si joueur pas trouvé dans la base
	$matching_sign = ""; // deviendra ✅ | ❌ | ⚠️
	
	if (!$ffe_player)  // ffe_player false si rien trouvé
	
	{
		/* licence pas trouvée dans la base PUCE */
		if ($debugmode) echo "licence pas trouvée dans la base";
		$ValidPlayer = false;
		$AddPlayer = false;
		$matching_sign = "❌"; 
		$add_url = "https://www.chessmooc.org/web/PUCE-ins/edit-member.php?";
		$add_url .= "fid=" . $p["licence"];
		$add_url .= "&f=" . urlencode($p["firstName"]);
		$add_url .= "&l=" . urlencode($p["lastName"]);
		
		$Message .= "Numéro de licence " . $p["licence"] . " non trouvé". $BR;
		$Message .= "Vous pouvez ajouter le joueur dans la base PUCE". $BR;
		$Message .= "① Vérifiez sur le <a href='http://www.echecs.asso.fr/ListeJoueurs.aspx?Action=FFE'>site FFE</a> que le numéro de licence saisi est correct". $BR;
		$Message .= "② Connectez-vous sur le <a href='https://chessmooc.org/web/user/login.php'>site PUCE</a>". $BR;
		$Message .= "③ Cliquez sur <a href='$add_url'>ce lien </a> pour accéder au formulaire d'ajout". $BR;
		/* TBD : préparer le message d'ajout  */
	} else {
		/* ------------- Numéro de licence existe, on vérifie que les noms correspondent ------------------ */
		
		$score_match = person_match( $p["firstName"], $p["lastName"], $ffe_player['firstname'],  $ffe_player['lastname'] );
		
		$player_id = $ffe_player['id'];
		if ($debugmode) {
			$debugstring = $p["firstName"] . " " . $p["lastName"] . " vs " . $ffe_player['firstname'] . " " . $ffe_player['lastname'];
			$debugstring .= " --> scorematch = $score_match" ."\n";
			echo $debugstring;
		}
		
		$matching_sign = match_rate_to_match_sign($score_match);
		if ($debugmode) echo $matching_sign;
		$ValidPlayer = ($score_match >= 0.5 ); // Attention, doit être en cohérence avec la limite basse de la fonction
		/* ------------- si pas de problème trouvé sur le nom, on vérifie que le joueur n'est pas déjà inscrit ------------------ */
		if ($ValidPlayer)
		{
			/* détermination de l'id du subevent */
			$target_sub = 0;
			if ($NbSubs == 1) 
			{
				/* on prend l'unique subevent comme cible*/
				if ($debugmode) echo "un seul subevent dans PUCE : " . $subevent_arr[0]['name'] . "(id=" . $subevent_arr[0]['id'] . ")" .$BR ; // debug
				$target_sub = $subevent_arr[0]['id'];
				if ($debugmode) echo "target sub of unique sub = " . $target_sub . $BR;
			} 
			if ($NbSubs !=0)
			{
				/* on cherche le subevent cible (8)*/
				if ($debugmode) echo "au moins deux subevents dans PUCE : " .$BR ; // debug
				foreach ($subevent_arr as $sub) 
				{
					if (unaccent_up($sub['name']) == unaccent_up($p['subevent'])) 
					{
						$target_sub = $sub['id'];
						$Message .= "tournoi correctement identifé" . $BR;
					}
				}
				if ($debugmode) echo "target_sub = $target_sub \n";
				if ($target_sub == 0) 
				{
					/* tournoi non identfié */
					$tournoi_non_trouvé = $p["subevent"];
					$Message .= "Impossible d'identifier le tournoi <b>" . $tournoi_non_trouvé . "</b> dans PUCE-inscription" . $BR;
					$AddPlayer = false;
				} else {
					/* regardons si le joueur est déjà inscrit dans le tournoi identifié (9b) */
					$qtxt = "SELECT id FROM registrations WHERE member_id=" . $player_id . " AND subevent_id=" . $target_sub . ";";
					
					$reponse = $conn->query($qtxt);
					$existing_reg = $reponse->fetch(PDO::FETCH_ASSOC);
					// var_dump($existing_reg);
					if (!$existing_reg)
					{ 
						/* registration not found in this subevent for this player */
						$Message .= "Ce joueur a correctement été inscrit dans le tournoi" . $BR;

					} else {
						$AddPlayer = false;
						$Message .= "joueur déjà inscrit dans ce tournoi" .$BR;
						//echo "£existing_reg pas false -> il y a une réponse" . $BR;
					}
				}
			}
		} else {
			$AddPlayer = false;
			$Message .= "Nom / Prénom ne correspondent pas au numéro de licence" .$BR;
			if ($debugmode) echo "score_match plus petit que 0.5 !!"; // à enlever après debug
		}
	}
	// echo "should we add player : "; var_dump($AddPlayer); echo "\n";
	if ($AddPlayer) 
	{
		/* ajoutons le joueur dans le tournoi (10) */
		$add_req=$conn->prepare("INSERT INTO registrations (member_id, subevent_id, confirmed)
								VALUES (:n_member_id, :n_sub_id ,:n_confirmed);"
								);
		$add_req->BindParam(':n_member_id', $player_id);
		$add_req->BindParam(':n_sub_id', $target_sub);
		$add_req->BindParam(':n_confirmed', $one);
		if ($add_req->execute()){
			$Message .= "L'inscription a bien été prise en compte" .$BR;
		} else {
			$Message .= "La tentative d'inscription a échoué. Contactez l'administrateur" .$BR;
		}
	} else {
		$Message .= "La demande d'inscription est ignorée" .$BR;
		$matching_sign = "❌";
	}
	//if ($debugmode) echo "\n- $matching_sign  -----------------------------\n";
	$mail_obj .= $matching_sign . " ";
}
$ShortMessage = $Message;
//echo $mail_obj . "\n". $Message; // à enlever après debug
$Message .= "Cliquez sur <a href='https://www.chessmooc.org/t.php?t=$event_id_str' > ce lien </a> pour accéder à la liste des inscrit(e)s" ;
$Message .= $BR . $BR . $BR . $BR . $BR;
//echo $Message;
$Message .=  "POST data <br /><hr>" . file_get_contents('php://input');
$mailto= "lamblain@gmail.com, $event_contact";
$from  = "PUCE<noreply@chessmooc.org>";  // adresse MAIL OVH liée à ton hébergement.
$ReplyTo = "noreply@chessmooc.org";
$mail_Data = $Message;
$headers  = "MIME-Version: 1.0 \n";
$headers .= "Content-type: text/html; charset=UTF-8 \n";
$headers .= "From: $from  \n";
$headers .= "Reply-To: $ReplyTo  \n";
$CR_Mail = TRUE;
$CR_Mail = @mail ($mailto, $mail_obj, $mail_Data, $headers);
if ($debugmode) echo $Message;
$LogMessage = "mail sent to " . $event_contact;
LogData ($conn, $LogMessage);
echo $LogMessage;
echo $ShortMessage;

?>