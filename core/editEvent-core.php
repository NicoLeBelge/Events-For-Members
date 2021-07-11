<?php
session_start();
$pathbdd = '../../_local-connect/connect.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modification d'évènement</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Editer un event">
		<meta name="keywords" content="tournois, inscription, modification">

		<link rel="stylesheet" href="../css/E4M.css" /> 
		<!--
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
		-->
		<script type="text/javascript" src="../JS/verifDataForm.js"></script>  
    </head>

    <body >
		<?php
			$err = 0; 
			include($pathbdd);
			include('editEvent-functions-core.php');
			if(modifAuthorization($conn,$err)['success'])
			{
				echo '<br />';
				echo(modifAuthorization($conn,$err)['message']);
				echo '<br />';
				include('editEvent-formulaire-core.php');
		?>
		 <script type="text/javascript">
		 	validate();
		</script> 
		<?php
			}
			else
			{
				echo '<br />';
				echo(modifAuthorization($conn,$err)['message']);
				echo '<br />';
				echo $err;
			}
		?>
    </body>
</html>
