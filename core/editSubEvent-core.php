<?php
session_start();
$pathbdd = '../../_local-connect/connect.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modification d'un sous-évènement</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Editer un sous-évènement">
		<meta name="keywords" content="tournois, inscription, modification">

		<link rel="stylesheet" href="../css/E4M.css" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    </head>

    <body >
		<?php
			include($pathbdd);
		?>
    </body>
</html>