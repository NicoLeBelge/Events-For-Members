<?php
    session_start();	

    $str = json_decode(file_get_contents('./json/strings.json'),true);	
    $jsonstr = json_encode($str);

    $pathbdd = '../_local-connect/connect.php';
    include($pathbdd);
?>

<?php   
    echo '<h3> PHP List All Session Variables</h3> <pre style = "style="overflow:auto";">';
    foreach ($_SESSION as $key=>$val)
    {
        echo $key." ".$val."<br/>";
    }
    echo '</pre>';

    $EMPTY_STRING = "NULL";
    $ALL_STRING='*';
    $EMPTY_INT=0;

    $event_id = 3;
    $name = '"'.'Name of the sub-event'.'"';
    $datestart = "NULL";
    $nbmax = $EMPTY_INT;
    $link = $EMPTY_STRING;
    $rating_type = $EMPTY_INT;
    $gender ='"'.$ALL_STRING.'"';
    $rating_restriction = $EMPTY_INT;
    $rating_limit = "NULL";
    $rating_comp ="NULL";
    $cat = '"'.$ALL_STRING.'"';
    $type = '"'.$ALL_STRING.'"';





    $sql = "INSERT INTO `subevents` (event_id, name, datestart, nbmax, link, rating_type,gender,rating_restriction,rating_limit,rating_comp, cat,type) VALUES (3,\"Name of the sub-event\",NULL,0,NULL,0,\"*\",0,NULL,NULL,\"*\",\"*\")";;
    echo $sql.'<br>';
    $res= $conn->query($sql);

?>


<?php
    sleep(1);
    header('Location: register-event.php?id=3');
    exit();
?>