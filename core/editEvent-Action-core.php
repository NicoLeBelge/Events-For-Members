<?php
	$ID = "id";
	$EMPTY_STRING = "";
	$pathbdd = '../../_local-connect/connect.php';
	$pathJson = '../_json/strings.json';
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
			/* let's prepare and execute the update request */
			$name = str_replace('"', "'", $_POST['name']);
			$datestart = $_POST['datestart'];
			$datelim = $_POST['datelim'] . " 20:00:00";
			$secured = ($_POST['secured'] =='no') ? 0 : 1 ;
			$contact = $_POST['contact'];
			$nbmax = empty($_POST['nbmax']) ? NULL : intval($_POST['nbmax'],10);
			$pos_long = empty($_POST['pos_long']) ? NULL : floatval($_POST['pos_long']);
			$pos_lat = empty($_POST['pos_lat']) ? NULL : floatval($_POST['pos_lat']);
			$paylink = $_POST['paylink'];

			$reqE=$conn->prepare("UPDATE events SET name = :n_name, datestart=:n_datestart, 
			datelim=:n_datelim, secured=:n_secured, contact=:n_contact, nbmax=:n_nbmax, 
			pos_long=:n_pos_long, pos_lat=:n_pos_lat, paylink=:n_paylink    
			WHERE id=:searched_id;");
			$reqE->BindParam(':n_name', $name);
			$reqE->BindParam(':n_datestart', $datestart);
			$reqE->BindParam(':n_datelim', $datelim);
			$reqE->BindParam(':n_secured', $secured);
			$reqE->BindParam(':n_contact', $contact);
			$reqE->BindParam(':n_nbmax', $nbmax);
			$reqE->BindParam(':n_pos_long', $pos_long);
			$reqE->BindParam(':n_pos_lat', $pos_lat);
			$reqE->BindParam(':n_paylink', $paylink);
			$reqE->BindParam(':searched_id', $_POST['id']);
			$reqE->execute();
		}
		else echo $values['Error_form'];
	}
	else echo $values['Absence_form']
 	
?>

<?php
	sleep(1);
	header('Location: ..');
	exit();
?>