<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modification d'évènement</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Editer un event">
		<meta name="keywords" content="tournois, inscription, modification">

		<link rel="stylesheet" href="./css/E4M.css" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
		<script type="text/javascript" src="./JS/verifDataForm.js"></script>  
    </head>

    <body >

    <?php
        $path = './core/editEvent-core.php';
        if(!empty($_GET['id'])) include($path);
    ?>
    </body>
</html>