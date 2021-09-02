<?php
/*   - comments to update !!
page to be included in a php page (edit-create-event.php or any name chosen by admin)
input : none
displays a form to enter new values for event creation

*/

/* lets get strings from json folder (strings displayed and configuration strings) */

$str = json_decode(file_get_contents('./json/strings.json'),true);	
$jsonstr = json_encode($str);

?>

<div class='E4M_maindiv'>
    <h2><?=$str["event_creation_title"]?></h2>
    <form action="./core/createEvent-Action-core.php" method="post">
    
            <label for="name"><?=$str["event_name_label"]?></label><span style="color: red"> *</span>
            <input type="text" id="name" name="name" required/>
            <label for="datestart"><?=$str["Date_of_place"]?></label>  
            <input type="date" name="datestart" required/><span style="color: red"> *</span>
            <label for="datelim"><?=$str["Date_until"]?></label>  
            <input type="date" name="datelim" />

            <p><?=$str["Event_secured_info"]?>
                <input type="radio" id="yes" name="secured" value="yes" checked>
                <label for="yes">yes</label>

                <input type="radio" id="no" name="secured" value="no">
                <label for="no">no</label>
            </p> 
                <label for="mail"><?=$str["Organizer_email"]?></label><span style="color: red"> *</span>
                <input type="email" id="mail" name="contact" required/>
                
            <label for="nbmax"><?=$str["Nb_max_participants"]?></label>   
            <input type="number" name="nbmax" />
            <br/>
            
            <label for="pos_lat"><?=$str["geoloc_lat_long"]?></label>   
            <input type="number" step="any" name="pos_lat" />
            <input type="number" step="any" name="pos_long" />
            

            <p><input type="submit" value="<?=$str["Validate"]?>" id="submitButton"></p>
            <input id="id" name="id" type="hidden" value=<?php echo $_SESSION['user_id'] ?>>
    </form>	
	
	
</div>
