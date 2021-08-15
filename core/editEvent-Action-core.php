<?php
	$ID = "id";
	$EMPTY_STRING = "";
	$pathbdd = '../../_local-connect/connect.php';
	$pathJson = '../json/strings.json';
	$values = json_decode(file_get_contents($pathJson),true);
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
				if($value != $EMPTY_STRING && $key != $ID)
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
					$res= $conn->query(htmlspecialchars($requete));
					echo $requete."<br />";
				}
			}
			$requete='SELECT * FROM `events` WHERE id='.$_POST['id'];
			$res= $conn->query(htmlspecialchars($requete));
		}
		else echo $values['Error_form'];
	}
	else echo $values['Absence_form']
 	
?>