<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	
	/** Let's check if event_id is valid (we're already know $_GET['id'] isset)
	 * if not let's write an error message
	 * if so, then let's check if current user is owner of this subevent
	 * abnormal access to this page is unlikely --> error messages in english
	 */
	$message="";
	
	

	$subeventId = $_GET['id'];
	$requete="SELECT owner 
	FROM events
	INNER JOIN subevents
	ON subevents.event_id = events.id	
	WHERE subevents.id=$subeventId";
	echo "<br/>";
	$res= $conn->query(htmlspecialchars($requete));
	$display_form = false;
	if ($res->rowCount() == 0) { // subevent not found
		$message="event_id $subeventId not found";
	} else {
		$owner = $res->fetch();
		if (! isset($_SESSION['user_id'])) {
			$message="you must be connected to access this page";
		} else { // visitor is connected
			if ($owner[0] <> $_SESSION['user_id']) {
				$message="Only owner of this subevent can edit it";
			} else { // visitor is the owner
				if ( $_SESSION['user_ip'] <> getIp() ){	
					$message="IP has change since last login. Log out and log back in";
				} else {
					$display_form = true;
				}
			}
		}
		
	}
	if ($message <>"") {
		echo "<h1>".$message."</h1>";
	}

	if ($display_form) {
		$requete="SELECT * FROM subevents WHERE id=$subeventId;";
		$res= $conn->query(htmlspecialchars($requete));
		$array_old = $res->fetch();
		var_dump($array_old["name"]);
	}
	
?>
<?php if($is_owner): ?>
<h1>is_owner is true</h1>

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
<p> purée ça bugge plus !! <p>


<script type="text/javascript">
	let e=document.getElementById("name");
	e.value=`<?=$array_old['name']?>`;
</script> 
<?php endif; ?>
