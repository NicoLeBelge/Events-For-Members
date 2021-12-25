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
    echo $_GET['event_id'];
    echo '</pre>';

    $EMPTY_STRING = "NULL";
    $ALL_STRING='*';
    $EMPTY_INT=0;
    $SPACE = ",";
    $event_id = $_GET['event_id'];
    $name = '"'. $str["default_subevent_name"] . '"';
    $datestart = "NULL";
    $nbmax = $EMPTY_INT;
    $link = $EMPTY_STRING;
    $rating_type = 1;
    $gender ='"'.$ALL_STRING.'"';
    $rating_restriction = $EMPTY_INT;
    $rating_limit = "NULL";
    $rating_comp ="NULL";
    $cat = '"'.$ALL_STRING.'"';
    $type = '"'.$ALL_STRING.'"';





    $sql = "INSERT INTO `subevents` (event_id, name, datestart, nbmax, link, rating_type,gender,rating_restriction,rating_limit,rating_comp, cat,type) 
    VALUES (".$event_id.$SPACE.$name.$SPACE.$datestart.$SPACE.$nbmax.$SPACE.$link.$SPACE.$rating_type.$SPACE.$gender.$SPACE.$rating_restriction
    .$SPACE.$rating_limit.$SPACE.$rating_comp.$SPACE.$cat.$SPACE.$type.")";;
    echo $sql.'<br>';
    $res= $conn->query($sql);

?>


<?php
    sleep(1);
    header('Location: register-event.php?id='.$_GET['event_id']);
    exit();
?>