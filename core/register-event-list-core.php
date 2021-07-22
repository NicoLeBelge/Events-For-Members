<!DOCTYPE HTML>  
<html>
<head>
<meta charset="UTF-8" />


</head>
<body>  

<?php

include ('../_local-connect/connect.php');
// Let's get the strings from the json !
$json = file_get_contents('./json/strings.json');
$str = json_decode($json,true);	
$json = file_get_contents('./json/config.json');
$cfg = json_decode($json,true);	
echo "<h3>" . $str['event_list_title'] . "</h3>";

$reponse = $conn->query('SELECT id, datestart, name from events ORDER BY datestart ASC');
?>
<!-- used to be eventlist-->
<div class="E4M_hoverable_list"> <!-- debug faudra probalement rendre paramétrable-->
<table>
	<tr>
		<th><?= $str['date_label'] ?></th>
		<th><?= $str['event_label'] ?></th>
	</tr>

<?php while ($event = $reponse->fetch()): ?> 
	<tr>
		<td> <?=$event['datestart']?> </td> 
		<td> <a href="<?=$cfg['event_page']?>?id=<?=$event['id']?>"><?=$event['name']?></a> </td>
	</tr>
<?php endwhile; ?>
<?php if (isset($_SESSION['user_id'])): ?>
	<tr>
		<td> </td>
		<td> <a href="<?=$cfg['create_event_page']?>"><?=$str['add_event']?></a> </td>
	</tr>
<?php endif; ?>

</table>
</div>
<?php
$reponse->closeCursor();	
?>

</body>
</html>
