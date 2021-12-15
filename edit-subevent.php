<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modification d'un tournoi</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Editer un event">
		<meta name="keywords" content="tournois, inscription, modification">

        <link rel="stylesheet" href="../css/ChessMOOC-style.css" /> 
		<link rel="stylesheet" href="./css/E4M.css" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    </head>

    <body >

    <?php 
	
        include ('../head-foot/T0P-menu.php');
        
        echo "<div class='cent_block'> \n";
        echo "<br/> \n";
        $path = './core/edit-subevent-core.php';
        if(!empty($_GET['id'])) include($path);
    
        echo "</div> \n";
	
        echo "<div class='T0P-footer'> \n";
        include ('../head-foot/T0P-footer.php');
        echo "</div> \n";
        ?>
    </body>
</html>