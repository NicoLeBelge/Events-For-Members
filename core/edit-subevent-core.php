<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	if(modifAuthorization($conn)['success'])
	{
?>
	<?php
	$ID = $_GET['id']; 
	$pathbdd = './../_local-connect/connect.php';
	include($pathbdd);
	$requete='SELECT * FROM `subevents` WHERE id='.$ID;
	$res= $conn->query(htmlspecialchars($requete));
	$array_old = $res->fetch();
?>
<!-- debug - onchange="validate()" temporarily suppressed | should be added with JS and implemented in a customized way-->
<form action="./core/editsubevent-action-core.php" method="post">
	<label for="subname">Nom du subevent :</label>  <input type="text" id="subname" name="subname" />
	<p>Date de début de l'évènement : <input type="date" name="datestart" value=<?=$array_old['datestart'] ?> /></p>
	<p>Date de fin d'inscription : <input type="date" name="datelim" value=<?=$array_old['datelim'] ?> /></p>
	<p>Sécurisation de l'évènement :
		<input type="radio" id="yes" name="secured" value="yes" <?php if($array_old["secured"]) echo "checked";  ?> />
		<label for="yes">yes</label>

		<input type="radio" id="no" name="secured" value="no" <?php if(!$array_old["secured"]) echo "checked";  ?> />
		<label for="no">no</label>
	</p> 
        <label for="mail">e-mail de contact :</label>
        <input type="email" id="mail" name="contact" value=<?=$array_old['contact'] ?>  />

	<p>Nombre maximal d'inscrits : <input type="number" name="nbmax" value=<?=$array_old['nbmax'] ?> /></p>

	<p>Position longitude : <input type="number" step="any" name="pos_long" value=<?=$array_old['pos_long'] ?> /></p>
	<p>Position latitude : <input type="number" step="any" name="pos_lat" value=<?=$array_old['pos_lat'] ?> /></p>

	<p><input type="submit" value="OK" id="submitButton"></p>
	<input id="id" name="id" type="hidden" value=<?php echo $_GET['id'] ?>>
</form>



<script type="text/javascript">
	let e=document.getElementById("name");
	e.value=`<?=$array_old['name']?>`;
</script> 
<?php
	}
	else echo('<br />'.modifAuthorization($conn)['message'].'<br />');
?>

