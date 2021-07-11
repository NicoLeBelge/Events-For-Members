<?php
	$pathbdd = '../../_local-connect/connect.php';
	include($pathbdd);
	if(!empty($_POST))
	{
    $name = "";
    $datestart = "";
    $datelim = "";
    $secured = "";
    $contact = "";
    $nbmax = ""; 
    $pos_long =  "";
    $pos_lat = "";
    $owner = "";
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
                        $datelim = $datestart.' 21:00:00';
                    }
                    else
                    {
                        $datelim = $value.'21:00:00';
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
    $res= $conn->query($requete);
    echo $requete;
    echo "<br />";
	}
	else echo "formulaire absent";	
?>