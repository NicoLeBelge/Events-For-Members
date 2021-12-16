<?php
	$pathJson = './json/strings.json';
	$values = json_decode(file_get_contents($pathJson),true);
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

	function modifAuthorization($conn)
	{
		global $values;
		/* 
			Action : This function checks if the visitor has the right to modify the event  
			Return : 		
			$tab_verif :
			    "message"  => $message,
			    "success " => $boolSuccess
		*/
		$message = "TestModification";
		$boolSuccess = false;
		if(! empty($_SESSION))
		{
			$user_ip = $_SESSION['user_ip'];
			$user_id = $_SESSION['user_id'];
			if($user_ip == getIP() && ! empty($_GET))
			{
				$requete='SELECT * FROM `events`';
				$res= $conn->query(htmlspecialchars($requete));
				if(!empty($res->error)) exit(0); // tue Php, arrête l'interprétation de la page   
				while ($row = $res->fetch()) 
				{
					if($row['id'] == $_GET['id'])
				    {
					    $owner = $row['owner'];
					    if($owner == $user_id)
					    {
							$message = '<p class="E4M_message_alerte"> modification autorisée, id : '.$user_id.'</p>';
							$boolSuccess = true;
					    }
					    else $message =$values["Error_choice_event"];
				    }
					else $message = $values["Error_edition_event_id"];
				}
			}
			else $message = $values["Error_modif_event"];
		}
		else $message = $values["Error_chargement_event"];

		$tab_verif = array (
		    "message"  => $message,
		    "success" => $boolSuccess
		);	
		return($tab_verif);
	}
	function user_is_owner($conn)
	{
		global $values;
		/* 
			Action : This function checks if the visitor has the right to modify the event  
			Return : 		
			$tab_verif :
			    "message"  => $message,
			    "success " => $boolSuccess
		*/
		$message = "TestModification";
		$boolSuccess = false;
		if(! empty($_SESSION))
		{
			$user_ip = $_SESSION['user_ip'];
			$user_id = $_SESSION['user_id'];
			if($user_ip == getIP() && ! empty($_GET))
			{
				$requete='SELECT * FROM `events`';
				$res= $conn->query(htmlspecialchars($requete));
				if(!empty($res->error)) exit(0); // tue Php, arrête l'interprétation de la page   
				while ($row = $res->fetch()) 
				{
					var_dump($row['id']); echo " dans user_is_owner<br/>";
					if($row['id'] == $_GET['id'])
				    {
					    $owner = $row['owner'];
					    if($owner == $user_id)
					    {
							$message = '<p class="E4M_message_alerte"> modification autorisée, id : '.$user_id.'</p>';
							$boolSuccess = true;
					    }
					    else $message =$values["Error_choice_event"];
				    }
					else $message = $values["Error_edition_event_id"];
				}
			}
			else $message = $values["Error_modif_event"];
		}
		else $message = $values["Error_chargement_event"];

		$tab_verif = array (
		    "message"  => $message,
		    "success" => $boolSuccess
		);	
		return(false);
	}
?>