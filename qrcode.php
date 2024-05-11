<?php
	session_start();
    $return_page = $_SESSION['return_page'];
?>

<!DOCTYPE html>
<html>
    <head>
        <title>check-in</title>
		<meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="./_css/E4M.css" /> 
    </head>

    <body >
    <?php 
        $path = './core/qrcode-core.php';
        include($path);
    ?>
    </body>
</html>