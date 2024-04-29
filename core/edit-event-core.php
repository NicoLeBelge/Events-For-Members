<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	$str = json_decode(file_get_contents('./_json/strings.json'),true);	
	$ID = $_GET['id']; 
	$requete='SELECT * FROM `events` WHERE id=?';
	$res= $conn->prepare($requete);
	$res->execute([$ID]);
	$array_old = $res->fetch();
	/*check if datelim is past and if so, add `code` field for check-in*/
	$datelim = $array_old["datelim"];
	$datelimit = new DateTime($array_old["datelim"]);
	$now = new DateTime();
	$datelim_past = !($datelimit > $now);
	$url_back = "https://www.chessmooc.org/web/PUCE-ins/API/helloasso2.php?t=" . $ID . "&key=" . $array_old['api_key'];
	
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
	<img src="./_img/helloasso-h25.png" alt="cliquez pour copier le lien à communiquer à Helloasso" style="display : none;"/> <span id="E4M_instruction"></span><br/><br/>
	
	<label ><?=$str["url_callback"]?></label> <br/>  
	<p id="url_callback"> <?=$url_back ?>
	<img id="copy_icon" src="./_img/copy-icon.svg" alt="cliquez pour copier le lien à communiquer à Helloasso" height="18" style="display : none;"/> <span id="E4M_instruction"></span><br/><br/>
	</p>
	

	<?php if($datelim_past): ?>
		<br>
		<label for="code"><?=$str["checkin_code"]?></label>   
		
		<input type="text" id="code" value= "<?=$array_old['code'] ?>" name="code"/>
	<?php endif; ?>
	<br>

	<p><input type="submit" value="<?=$str["Save"]?>" id="submitButton"></p>
	<input id="id" name="id" type="hidden" value=<?php echo $_GET['id'] ?>>
</form>
<script type='text/javascript'> 
const Hello_input = document.getElementById("paylink")
const icon = document.getElementById("url_callback");
const copy_icon = document.getElementById("copy_icon");
display_hello_if_needed();

Hello_input.addEventListener ('keyup', e => {
	let targetstring="helloasso";
	let contains_target = e.target.value.includes(targetstring);
	icon.style.display = contains_target ? "inline-block" : "none";
	}); 
icon.addEventListener ('click', () => {
	let callback_url = "<?=$url_back ?>";

	document.getElementById("copy_icon").src = "./_img/copied-icon.svg";
	
	navigator.clipboard.writeText(callback_url).then(function(){
		alert(callback_url + " copié dans le presse papier");
	});
});
function display_hello_if_needed() {
	let targetstring="helloasso";
	let contains_target = Hello_input.value.includes(targetstring);
	
	icon.style.display = contains_target ? "inline-block" : "none";
	copy_icon.style.display = contains_target ? "inline-block" : "none";
	
}
</script>