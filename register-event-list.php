<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>events</title>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="./_css/E4M.css" /> 
		<link rel="icon" type="image/png" href="../img/logo-3-96.png" />
    </head>
    <body >
	<?php 
		include ('./core/register-event-list-core.php');
	?>
	<br/>
	<!-- customize the below example to allows the user to log in -->
	<a href="../user/login.php"> Log in </a>
    </body>
</html>
