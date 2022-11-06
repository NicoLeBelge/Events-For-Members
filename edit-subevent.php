<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Subevent Modification</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		
		<link rel="stylesheet" href="./_css/E4M.css" /> 
        
    </head>

    <body >

    <?php 
        $path = './core/edit-subevent-core.php';
        if ( ! empty($_GET['id']) ) {
            include($path);
        } else {
            echo "event_id not set";
        }

        ?>
    </body>
</html>