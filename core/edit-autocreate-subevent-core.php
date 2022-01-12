<?php
    $str = json_decode(file_get_contents('./_json/strings.json'),true);	
    $cfg = json_decode(file_get_contents('./_json/config.json'),true);	
    $jsonstr = json_encode($str);
    $pathbdd = '../_local-connect/connect.php';
    include($pathbdd);
    $EMPTY_STRING = "NULL";
    $ALL_STRING='*';
    $EMPTY_INT=0;
    $SPACE = ",";
    $event_id = $_GET['event_id'];
    $name = $str["default_subevent_name"];
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


    $reqS=$conn->prepare("INSERT INTO subevents (event_id, name) VALUES (:n_event_id, :n_name)");
    $reqS->BindParam(':n_event_id', $event_id);
    $reqS->BindParam(':n_name', $name);
    $reqS->execute();

    sleep(1);
    echo "<script type='text/javascript'>document.location = '" . $cfg['event_page'] . "?id=$event_id'</script>";