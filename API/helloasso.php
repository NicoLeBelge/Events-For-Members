<?php
/**
 * problème en cours (apparemment)
 * si le subevent n'est pas identifié, ça chie.
 */
include ('hello-tools.php');
$Message = "";
// $Message .=  file_get_contents('php://input');
// echo "<pre>";
// var_dump(json_decode($Message));
// echo "</pre>";

$BR = "<br />";
$one = 1;
$event_OK = true;

if (is_null($_GET['t'])) 
{
	$Message .= 'Error : URL called without event id as a parameter'. $BR;
	$event_OK = false;
} else {
	$event_id_str = $_GET['t'];
}

if ($event_OK) {
	$items_arr = json_decode(file_get_contents('php://input'))->data->items;

	$p_arr = array();
	//$player = array();
	/* chaque item représente un inscrit, on va chercher le nom (objet "user") et les données (array customFields)) */


	foreach ($items_arr as $item) 
	{
		/**
		 * Attention, on dirait qu'il y a une inversion entre firstName et lastName dans le json qui vient de Helloasso
		 */
		$player = array(); // on réinitialise un nouveau player
		$player += ["firstName"=>$item->user->firstName];
		$player += ["lastName"=>$item->user->lastName];
		$player += ["licence"=>""];
		$player += ["subevent"=>""];

		$custfielsobj = $item->customFields;
		/* on balaye tous les objets de l'array customFields pour récupérer le numéro de licence, et éventuellement le tournoi*/
		foreach ($custfielsobj as $customField) 
		{
			if (stripos($customField->name, 'licence') <>0 ) $player["licence"] = $customField->answer;
			if (stripos($customField->name, 'tournoi') <>0 ) $player["subevent"] = $customField->answer;
		}
		array_push($p_arr, $player);
	}

	// echo "<pre>" ; var_dump($p_arr); echo "</pre>" ;

	/* Attention BUG Helloasso -> on inverse nom et prénom pour gérer */
	// $p1 = array("firstName" => "LAMBLAIN", "lastName" => "Nicolas", "licence" => "G04507", "subevent" => "Tournoi A"); // à restaurer si inversion Helloasso corrigée
	/* Pour Vitré
	$p1 = array("firstName" => "Nicolas", "lastName" => "LAMBLAIN", "licence" => "G04507", "subevent" => "Open A"); // à restaurer si inversion Helloasso corrigée
	array_push($p_arr, $p1);
	$p2 = array("firstName" => "Clément", "lastName" => "LAMBLAIN", "licence" => "K51245", "subevent" => "Open B");
	array_push($p_arr, $p2);
	$event_id_str = '43';
	*/
	/* Pour Tauxigny */
	// $p1 = array("firstName" => "Nicolas", "lastName" => "LAMBLAIN", "licence" => "G04508", "subevent" => "Master - Mixte"); // à restaurer si inversion Helloasso corrigée
	// array_push($p_arr, $p1);
	// $p2 = array("firstName" => "Clément", "lastName" => "LAMBLAIN", "licence" => "K51245", "subevent" => "Tournoi B");
	// array_push($p_arr, $p2);
	// $event_id_str = '3';

	/* Pour St Av (un seul subevent) */
	// $p1 = array("firstName" => "Nicolas", "lastName" => "LAMBLAIN", "licence" => "G04507", "subevent" => ""); // à restaurer si inversion Helloasso corrigée
	// array_push($p_arr, $p1);
	// $p2 = array("firstName" => "Clément", "lastName" => "LAMBLAIN", "licence" => "K51245", "subevent" => "");
	// array_push($p_arr, $p2);
	// $event_id_str = '20';
	
	include('../../_local-connect/connect.php'); // PDO connection required

	/* récupérons les données de l'event à traiter */

	
	$reponse = $conn->query("SELECT name, contact FROM events WHERE id=$event_id_str LIMIT 1;");

	// $event_name = $reponse->fetch(PDO::FETCH_ASSOC)['name']; // ça va vite, mais ça permet pas de récupérer le @
	$event_data = $reponse->fetch(PDO::FETCH_ASSOC);
	$event_name = $event_data['name']; 
	$event_contact = $event_data['contact']; 
	
	// var_dump($event_name, $event_contact); echo "<BR />";

	if (is_null ($event_name)){
		$Message .= "Festival non trouvé (event=" . $event_id_str . ") -> abandon <br />";
		$event_OK = false;
	} else {
		/* récupérons les données des subevents */
		$reponse = $conn->query("SELECT id, event_id, name FROM subevents WHERE event_id=$event_id_str;");
		$subevent_arr = $reponse->fetchAll(PDO::FETCH_ASSOC);
		// echo "récupération des données des subevents (loc1)" . $BR;
		// if ()is_null ()
		// foreach ($subevent_arr as $sub) { // debug
		// 	echo  . $BR;
		// }

		$NbSubs = sizeof($subevent_arr);
		if ($NbSubs == 0) 
		{
			$Message .= "Festival sans tournoi (event=" . $event_id_str . ") -> abandon <br />";
			$event_OK = false;
		}
	}

	if ($event_OK)
	{
		/* let's loop on each player from Helloasso*/
		foreach($p_arr as $p)
		{
			$Message .= "<hr>";
			$ValidPlayer = true;
			$AddPlayer = true;

			$Message .= "HelloAsso demande d'ajouter " . $p["firstName"] . " " . $p["lastName"];
			$Message .= " (" . $p["licence"] . ") dans <b>" . $event_name ;
			if ($p["subevent"] != "") 
			{
				$Message .= " / " . $p["subevent"] . "</b>" . $BR;
			} else {
				$Message .= "</b>" . $BR;
			}
			
			/* let's check if licence exists and name matches */
			$qtxt = "SELECT id, fede_id, firstname, lastname from members where fede_id='" . $p["licence"] . "';";
			// echo $qtxt; echo "<br />";
			$reponse = $conn->query($qtxt);
			//$player = array();
			$ffe_player = $reponse->fetch(PDO::FETCH_ASSOC);
			//var_dump($ffe_player);

			
			if (is_null($ffe_player))  // debug avant c'était sizeof
			{
				$ValidPlayer = false;
				$Message .= "Numéro de licence " . $p["licence"] . " non trouvé. Si le numéro de licence est valide, attendre une mise à jour de la base PUCE.";
			} else {
				/* ------------- Numéro de licence existe, on vérifie que les noms correspondent ------------------ */
				$ffe_p = $ffe_player;
				$player_id = $ffe_p['id'];
				if ($p["firstName"] == $ffe_p['firstname'] && $p["lastName"] == $ffe_p['lastname'])
				{
					$Message .= "Nom et Prénom concordent parfaitement avec le numéro de licence " . $BR;
				} else {
					/* matching problem, let's compare without case, without and accents, and with (firstname/lastname) inversion */
					
					$FIRSTLAST = unaccent_up($p["firstName"]) . unaccent_up($p["lastName"]);
					$LASTFIRST = unaccent_up($p["lastName"]) . unaccent_up($p["firstName"]);
					$FFE_FIRSTLAST = unaccent_up($ffe_p['firstname']) . unaccent_up($ffe_p['lastname']);
					if ($FIRSTLAST == $FFE_FIRSTLAST || $LASTFIRST == $FFE_FIRSTLAST )
					{
						$Message .= "Nom et Prénom concordent approximativement avec le numéro de licence " . $p["licence"] . $BR;
					} else {
						$ValidPlayer = false;
						$Message .= "Nom et Prénom ne concordent pas avec le numéro de licence " . $p["licence"] . $BR;
						$Message .= "Formulaire Helloasso = " . $p["firstName"] . " " .  $p["lastName"]. $BR;
						$Message .= "Base FFE = " . $ffe_p['firstname'] . " " . $ffe_p['lastname'] . $BR;
					}
				}
				/* ------------- si pas de problème trouvé sur le nom, on vérifie que le joueur n'est pas déjà inscrit ------------------ */
				if ($ValidPlayer)
				{
					
					/* détermination de l'id du subevent */
					$target_sub = 0;
					if ($NbSubs == 1) 
					{
						/* on prend l'unique subevent comme cible*/
						// echo "un seul subevent dans PUCE : " . $subevent_arr[0]['name'] . "(id=" . $subevent_arr[0]['id'] . ")" .$BR ;
						$target_sub = $subevent_arr[0]['id'];
						// echo "target sub of unique sub = " . $target_sub . $BR;
					} 
					if ($NbSubs !=0)
					{
						/* on cherche le subevent cible*/
						
						// echo "au moins deux subevents dans PUCE dont le premier : id= " . $subevent_arr[0]['id'] . $BR ;
						foreach ($subevent_arr as $sub) 
						{
							
							// if (is_null($sub['name'])) {
							// 	echo "sub sans nom" . $BR;
							// } else {
							//  var_dump ($sub['name']); echo $BR ;
							// }
							
							// echo "comparing " . $sub['name'] . " and ".  $p['subevent'] . $BR;
							if (unaccent_up($sub['name']) == unaccent_up($p['subevent'])) 
							{
								
								$target_sub = $sub['id'];
								$Message .= "tournoi correctement identifé" . $BR;
							}
						}
						
						if ($target_sub == 0) 
						{
							$Message .= "Impossible d'identifier le tournoi <b>" . $p["subevent"] . "</b> dans PUCE-inscription" . $BR;
							$AddPlayer = false;
							echo "target_sub = 0 c'est pas normal";
						} else {
							//echo "considering target_sub = " . $target_sub . $BR;
							/* tournoi trouvé, on regarde si le joueur n'y est pas déjà inscrit */
							//echo "on va regarder si l'id " . $player_id . " est déjà inscrit<br />";
							//echo $sub['name'] . " match " . $p['subevent'] . " (" . $target_sub . ")" . $BR ;
							$qtxt= "";
							$qtxt .= "SELECT id FROM registrations ";
							$qtxt .= "WHERE member_id=" . $player_id ;
							$qtxt .= " AND subevent_id=" . $target_sub . ";";
							//echo $qtxt . $BR;						
							$reponse = $conn->query($qtxt);
							$existing_reg = $reponse->fetch(PDO::FETCH_ASSOC);
							//var_dump($existing_reg) ; echo $BR ; // euh, t'est sûr que c'est sain ??? ça peut me donner false résultat requête vide !!
							// var_dump($existing_reg);
							
							// $Nbreg = count($existing_reg);
							// echo "Nbreg = $Nbreg" . $BR;
							if (!$existing_reg)
							{ 
								/* registration not found in this subevent for this player */
								$Message .= "Ce joueur n'était pas déjà inscrit dans ce tournoi, tout va bien" . $BR;

							} else {
								$AddPlayer = false;
								$Message .= "joueur déjà inscrit dans ce tournoi" .$BR;
								//echo "£existing_reg pas false -> il y a une réponse" . $BR;
							}
						}
					}
				} else {
					$AddPlayer = false;
				}
			}
			//$AddPlayer = false;

			if ($AddPlayer) 
			{
				//echo "ajoute dans la base " . $BR;
				$add_req=$conn->prepare("INSERT INTO registrations (member_id, subevent_id, confirmed)
										VALUES (:n_member_id, :n_sub_id ,:n_confirmed);
										");
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
			}
			
		}
		
	}
}

$Message .= $BR . $BR . $BR . $BR . $BR;
echo $Message; // à enlever après debug
$Message .=  "POST data <br /><hr>" . file_get_contents('php://input');

// $mailto= "echecsclubcorbas@free.fr, nicolas@lamblain.fr";
$mailto= "lamblain@gmail.com, $event_contact";
// echo $mailto . $BR;
$from  = "chessMOOC<noreply@chessmooc.org>";  // adresse MAIL OVH liée à ton hébergement.
$mailobject = "notification from HelloAsso"; 
$ReplyTo = "noreply@chessmooc.org";
$mail_Data = $Message;
$headers  = "MIME-Version: 1.0 \n";
$headers .= "Content-type: text/html; charset=UTF-8 \n";
$headers .= "From: $from  \n";
$headers .= "Reply-To: $ReplyTo  \n";
$CR_Mail = TRUE;
$CR_Mail = @mail ($mailto, $mailobject, $mail_Data, $headers);
/**
 * Attention, pour l'instant, ça inscrit quand c'est déjà inscrit.
 * apparaît visitblement si le nom du subevent est null.
 */

?>
