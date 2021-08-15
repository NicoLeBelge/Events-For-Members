<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Création d'un évènement</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="Créer un event">
		<meta name="keywords" content="tournois, inscription, création">

		<link rel="stylesheet" href="../css/E4M.css" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    </head>

    <body >
        <form action="createEvent-Action-core.php" method="post">
            <label for="name">Nom de l'event :</label>  <input type="text" id="name" name="name"required/>
            <p>Date de début de l'évènement : <input type="date" name="datestart" required/></p>
            <p>Date de fin de l'évènement : <input type="date" name="datelim" /></p>
            <p>Sécutisation de l'évènement :
                <input type="radio" id="yes" name="secured" value="yes" checked>
                <label for="yes">yes</label>

                <input type="radio" id="no" name="secured" value="no">
                <label for="no">no</label>
            </p> 
                <label for="mail">e-mail de contact :</label>
                <input type="email" id="mail" name="contact" required/>

            <p>Nombre maximal d'inscrits : <input type="number" name="nbmax" required /></p>

            <p>Position longitude : <input type="number" step="any" name="pos_long" /></p>
            <p>Position latitude : <input type="number" step="any" name="pos_lat" /></p>

            <p><input type="submit" value="OK" id="submitButton"></p>
            <input id="id" name="id" type="hidden" value=<?php echo $_SESSION['user_id'] ?>>
        </form>
    </body>
</html>