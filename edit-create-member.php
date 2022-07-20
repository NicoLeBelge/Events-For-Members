/** This page will be used for the organizer to create a new member  */
/** duplicated from dummy, to be done */
<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Création d'un joueur</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Créer un joueur">
        <link rel="stylesheet" href="../css/ChessMOOC-style.css" /> 
		<link rel="stylesheet" href="./_css/E4M.css" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    </head>

    <body >
    <?php 
	
    include ('../head-foot/PUCE-menu.php');
	
	echo "<div class='cent_block'> \n";
	echo "<br/> \n";
	include ('./core/edit-create-event-core.php');
    
	echo "</div> \n";
	
	echo "<div class='T0P-footer'> \n";
	include ('../head-foot/T0P-footer.php');
	echo "</div> \n";
	?>
   
    </body>
</html>