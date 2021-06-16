<?php
	$pathbdd = '../../../_local-connect/connect.php';
	include($pathbdd);
	if(!empty($_POST))
	{
		if
		(
			!empty($_POST['datestart']) || !empty($_POST['datelim']) 
			||  !empty($_POST['secured']) ||  !empty($_POST['nbmax'])
			|| !empty($_POST['name']) || !empty($_POST['contact'])
		)
		{
			foreach($_POST as $key => $value)
			{
				if($value != "" && $key != "id")
				{
					switch ($key) 
					{
						case 'name':
						$value = addcslashes($value,"'");
						$requete="UPDATE `events` SET ".$key."='".$value. "' WHERE id=".$_POST['id'];
						break;

						case 'secured':
						if($value =='no') $value = 0;
						else $value = 1;
						$requete="UPDATE `events` SET ".$key."=".$value." WHERE id=".$_POST['id'];
						break; 

						default:
						$requete="UPDATE `events` SET ".$key."= '".$value. "' WHERE id=".$_POST['id'];
						break;
					}
					$res= $conn->query($requete);
					echo $requete;
					echo "<br />";
				}
			}
			$requete='SELECT * FROM `events` WHERE id='.$_POST['id'];
			$res= $conn->query($requete);
			//echo $res->fetch()['name'];
		}
		else
		{
			echo "erreurs dans le formulaire";
		}
	}
	else
	{
		echo "formulaire absent";
	}
	
?>