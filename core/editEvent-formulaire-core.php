<?php
	$ID = $_GET['id']; 
	$pathbdd = './../_local-connect/connect.php';
	include($pathbdd);
	$requete='SELECT * FROM `events` WHERE id='.$ID;
	$res= $conn->query(htmlspecialchars($requete));
	$array_old = $res->fetch();
?>
<form action="./core/editEvent-Action-core.php" method="post">
	<label for="name">Nom de l'event :</label>  <input type="text" id="name" name="name" onchange="validate()"  value=<?="'".$array_old['name']."'"?> />
	<p>Date de début de l'évènement : <input type="date" name="datestart" onchange="validate()" value=<?=$array_old['datestart'] ?> /></p>
	<p>Date de fin d'inscription : <input type="date" name="datelim" onchange="validate()" value=<?=$array_old['datelim'] ?> /></p>
	<p>Sécurisation de l'évènement :
		<input type="radio" id="yes" name="secured" value="yes" <?php if($array_old["secured"]) echo "checked";  ?> />
		<label for="yes">yes</label>

		<input type="radio" id="no" name="secured" value="no" <?php if(!$array_old["secured"]) echo "checked";  ?> />
		<label for="no">no</label>
	</p> 
        <label for="mail">e-mail de contact :</label>
        <input type="email" id="mail" name="contact" onchange="validate()" value=<?=$array_old['contact'] ?>  />

	<p>Nombre maximal d'inscrits : <input type="number" name="nbmax" onchange="validate()" value=<?=$array_old['nbmax'] ?> /></p>

	<p>Position longitude : <input type="number" step="any" name="pos_long" onchange="validate()" value=<?=$array_old['pos_long'] ?> /></p>
	<p>Position latitude : <input type="number" step="any" name="pos_lat" onchange="validate()" value=<?=$array_old['pos_lat'] ?> /></p>

	<p><input type="submit" value="OK" id="submitButton"></p>
	<input id="id" name="id" type="hidden" value=<?php echo $_GET['id'] ?>>
</form>

