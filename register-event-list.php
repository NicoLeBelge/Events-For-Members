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
		<link rel="stylesheet" href="./_css/E4M.css" /> 
		<link rel="icon" type="image/png" href="../img/logo-3-96.png" />
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet"> 
    </head>
    <body >
	<?php 
	include ('../head-foot/T0P-menu.php');
	
	echo "<div class='cent_block'> \n";
	include ('./core/register-event-list-core.php');
	echo "</div> \n";
	
	echo "<div class='T0P-footer'> \n";
	include ('../head-foot/T0P-footer.php');
	echo "</div> \n";
	$_SESSION["comeback"] = "../ProjectTest/register-event-list.php";
	?>
    </body>
</html>
