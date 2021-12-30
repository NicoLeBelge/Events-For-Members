<?php
    $EMPTY_STRING = "";
    $HOUR_END = ' 20:00:00';
	$pathbdd = '../../_local-connect/connect.php';
	include($pathbdd);
    $str = json_decode(file_get_contents('../json/strings.json'),true);	
	if(!empty($_POST))
	{
        $name = $EMPTY_STRING;
        $datestart = $EMPTY_STRING;
        $datelim = $EMPTY_STRING;
        $secured = $EMPTY_STRING;
        $contact = $EMPTY_STRING;
        $nbmax = NULL; 
        $pos_long =  NULL;
        $pos_lat = NULL;
        echo "avant foreach : "; var_dump($nbmax); echo "<br/>";
        $owner = $EMPTY_STRING;
        foreach($_POST as $key => $value)
            {
                if($value == '') $value=NULL;
                switch ($key) 
                {
                    case 'name':
                    $name = addcslashes($value,"'");
                    break;

                    case 'secured':
                        if($value =='no') $secured = 0;
                        else $secured = 1;
                    break; 

                    case 'datestart':
                        $datestart = $value;
                    break;

                    case 'datelim':
                        if($value == NULL)
                        {
                            $datelim = $datestart.$HOUR_END;
                        }
                        else
                        {
                            $datelim = $value.$HOUR_END;
                        }
                    break;

                    case 'contact':
                        $contact = $value;
                    break;

                    case 'nbmax':
                        $nbmax = $value;
                    break;

                    case 'pos_long':
                        $pos_long = $value;
                    break;

                    case 'pos_lat':
                        $pos_lat = $value;
                    break;

                    case 'id':
                        $owner = $value;
                    break;

                    default:
                    break;

                }
            }
            echo "après foreach : "; var_dump($nbmax); echo "<br/>";
        $reqE=$conn->prepare("INSERT INTO events (name, datestart, datelim, secured, contact, nbmax, pos_long, pos_lat, owner) 
						VALUES (:n_name, :n_datestart, :n_datelim, :n_secured, :n_contact, :n_nbmax, :n_pos_long, :n_pos_lat, :n_owner)");
		$reqE->BindParam(':n_name', $name);
		$reqE->BindParam(':n_datestart', $datestart);
		$reqE->BindParam(':n_datelim', $datelim);
		$reqE->BindParam(':n_secured', $secured);
        $reqE->BindParam(':n_contact', $contact);
		$reqE->BindParam(':n_nbmax', $nbmax);
		$reqE->BindParam(':n_pos_long', $pos_long);
		$reqE->BindParam(':n_pos_lat', $pos_lat);
		$reqE->BindParam(':n_owner', $owner);
        $reqE->execute();
        
        /* let's get from the database the id of the event created */
        $reponse = $conn->query("SELECT id from events ORDER BY id DESC LIMIT 1");
        $new_event_id_car = $reponse->fetch();
        $new_event_id = intval($new_event_id_car[0]); // we assume no event will be created between INSERT and SELECT
        
        $reqS=$conn->prepare("INSERT INTO subevents (event_id, name) VALUES (:n_event_id, :n_name)");
        $reqS->BindParam(':n_event_id', $new_event_id);
        $reqS->BindParam(':n_name', $default_subevent_name);
        $default_subevent_name = $str["default_subevent_name"];
        $reqS->execute();
        /* http://localhost/web/E4M/register-event.php?id=1 */
        echo "<script type='text/javascript'>document.location = '../register-event.php?id=$new_event_id'</script>";
	}
	else echo "formulaire absent";	
?>