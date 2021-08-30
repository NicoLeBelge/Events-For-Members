<?php
    $EMPTY_STRING = "";
    $HOUR_END = ' 23:59:59';
	$pathbdd = '../../_local-connect/connect.php';
	include($pathbdd);
	if(!empty($_POST))
	{
        $name = $EMPTY_STRING;
        $datestart = $EMPTY_STRING;
        $datelim = $EMPTY_STRING;
        $secured = $EMPTY_STRING;
        $contact = $EMPTY_STRING;
        $nbmax = $EMPTY_STRING; 
        $pos_long =  $EMPTY_STRING;
        $pos_lat = $EMPTY_STRING;
        $owner = $EMPTY_STRING;
        foreach($_POST as $key => $value)
            {
                if($value == '') $value='NULL';
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
                        if($value == 'NULL')
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
        $requete="INSERT INTO  `events` VALUES(NULL,'".$name."','".$datestart."','".$datelim."',".$secured.",'".$contact."',".$nbmax.",".$pos_long.",".$pos_lat.",".$owner.")";
        $res= $conn->query(htmlspecialchars($requete));
        echo $requete."<br />";
	}
	else echo "formulaire absent";	
?>