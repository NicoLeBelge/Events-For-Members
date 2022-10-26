<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	$str = json_decode(file_get_contents('./_json/strings.json'),true);	
	$ID = $_GET['id']; 
	$pathbdd = './../_local-connect/connect.php';
	include($pathbdd);
	$requete='SELECT * FROM `events` WHERE id='.$ID;
	$res= $conn->query(htmlspecialchars($requete));
	$array_old = $res->fetch();
	
?>

<form action="./core/editEvent-Action-core.php" method="post">
	<label for="name"><?=$str["event_name_label"] ?></label>  
	<input type="text" id="name" name="name" value="<?=$array_old["name"] ?>" maxlength="80"/>
	<div class="E4M_dategrid">
		<div><label for="datestart"><?=$str["date_label"] ?></label> </div>
		<div><input type="date" id="datestart" name="datestart" value="<?=$array_old["datestart"] ?>"/></div>
		<div><label for="datelim"><?=$str["Date_until"] ?></label> </div>
		<div><input type="date" id="datelim" name="datelim" value= <?=$array_old['datelim'] ?>/></div>
	</div>
	
	<br>
	
	<label for="secured"><?=$str["Event_secured_info"] ?></label> 
	<br>
	
	<input type="radio" id="yes" name="secured" value="yes" <?php if($array_old["secured"]) echo "checked";  ?> />
	<label for="yes"><?=$str["yes"] ?> </label>

	<input type="radio" id="no" name="secured" value="no" <?php if(!$array_old["secured"]) echo "checked";  ?> />
	<label for="no"><?=$str["no"] ?></label>
	<br>
	<br>
	<label for="mail"><?=$str["Organizer_email"] ?></label>
    <input type="email" id="mail" name="contact" value=<?=$array_old['contact'] ?> maxlength="80" />
	<br>
	<label for="nbmax"><?=$str["Nb_max_participants"]?></label>   
	<input type="number" name="nbmax" value=<?=$array_old['nbmax'] ?> max="9999" />
	<br/><br/>
	<label for="pos_lat"><?=$str["geoloc_lat_long"]?></label>
	<input type="number" step="any" name="pos_lat" value=<?=$array_old['pos_lat'] ?> min="-90" max="90"/>
	<input type="number" step="any" name="pos_long" value=<?=$array_old['pos_long'] ?> min="-180" max="180" />
	<br><br>
	<label for="paylink"><?=$str["paylink_label"]?></label>   
	<input type="text" id="paylink" value= "<?=$array_old['paylink'] ?>" name="paylink"/>
	
	<br>

	<p><input type="submit" value="OK" id="submitButton"></p>
	<input id="id" name="id" type="hidden" value=<?php echo $_GET['id'] ?>>
</form>
