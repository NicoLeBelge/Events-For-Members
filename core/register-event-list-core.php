<?php
include ('../_local-connect/connect.php');
$str = json_decode(file_get_contents('./json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./json/config.json'),true);	
echo "<h3>" . $str['event_list_title'] . "</h3>";
$yesterday = new DateTime("now");
$yesterday->sub(new DateInterval('P1D'));
$yesterdayTXT = $yesterday->format("Y-m-d h:i:s");

$qtxt="SELECT id, datestart, name FROM events WHERE datestart >= '$yesterdayTXT' ORDER BY datestart ASC";

$reponse = $conn->query($qtxt);

?>
<div class="E4M_hoverable_list">
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
