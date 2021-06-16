<?php

	function getIp()
	{
		/*
			Action : This function returns the visitor\'s IP address 
			Return : $ip - String  
		*/
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	function modifAuthorization($conn,&$err)
	{
		/* 
			Action : This function checks if the visitor has the right to modify the event  
			Return : 		
			$tab_verif :
			    "message"  => $message,
			    "success " => $boolSuccess
		*/
		$message = "test";
		$boolSuccess = false;
		if(! empty($_SESSION))
		{
			$user_ip = $_SESSION['user_ip'];
			$user_id = $_SESSION['user_id'];
			if($user_ip == getIP() && ! empty($_GET))
			{
				$requete='SELECT * FROM `events`';
				$res= $conn->query($requete);
				if(!empty($res->error)) exit(0); // tue Php, arrête l'interprétation de la page   
				while ($row = $res->fetch()) 
				{
					if($row['id'] == $_GET['id'])
				    {
					    $owner = $row['owner'];
					    if($owner == $user_id)
					    {
							$message = "modification autorisée";
							$boolSuccess = true;
					    }
					    else
					    {
					    	$message = "Vous n'êtes pas autorisé à modifier cette Event";
					    	$err = 1;
					    }
				    }
				}
			}
			else
			{
				$message = "Erreur dans le choix de l'event";
				$err = 1;
			}
		}
		else
		{
			$message = "Erreur dans le chargement de la page.";
			$err = 1;
		}
		$tab_verif = array (
		    "message"  => $message,
		    "success" => $boolSuccess
		);	
		return($tab_verif);
	}
?>