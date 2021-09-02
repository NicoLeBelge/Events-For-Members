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
    
            <label for="name"><?=$str["event_name_label"]?></label>  <input type="text" id="name" name="name"required/>
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
	
	
</div>
