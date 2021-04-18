<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>liste des évènements annoncés</title>
		<meta charset="UTF-8" />
		<meta name="description" content="Inscrivez-vous en ligne aux tournois annoncés !">
		<meta name="keywords" content="tournois, inscriptions">
        <link rel="stylesheet" href="../css/ChessMOOC-style.css" /> 
		<link rel="stylesheet" href="./css/E4M.css" /> 
		<link rel="icon" type="image/png" href="../img/logo-3-96.png" />
        <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet"> 
    </head>

    <body >
	
	<?php 
	include ('./core/event-list-core.php')
	?>
	
    </body>
</html>
