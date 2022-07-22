<?php
include ('../_local-connect/connect.php');
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$cfg = json_decode(file_get_contents('./_json/config.json'),true);	

/** handles date to display only future events if settings are such */

if ($cfg["show_only_future"]) {
	$yesterday = new DateTime("now");
}
else {
	$yesterday = new DateTime("2000-01-01");
}
$yesterday->sub(new DateInterval('P1D'));
$yesterdayTXT = $yesterday->format("Y-m-d h:i:s");
$user_id="0";
if ( isset($_SESSION['user_id']) ) {
	$user_id=$_SESSION['user_id'];
}

$qtxt="SELECT * FROM events 
	WHERE datestart >= '$yesterdayTXT' 
	OR owner = $user_id
	ORDER BY datestart DESC";

$reponse = $conn->query($qtxt);
$reponse->closeCursor();	


$reponse = $conn->query($qtxt);
$event_list = $reponse->fetchAll(PDO::FETCH_ASSOC);
$event_list_str = json_encode ($event_list);
$str = json_decode(file_get_contents('./_json/strings.json'),true);	
$jsonstr = json_encode($str);
?>

<table id="event_list" class="E4M_regtable E4M_hoverable_list"></table>
<script type="text/javascript" src="./JS/E4M_class.js"></script>
<script type="text/javascript">
	let event_list = JSON.parse(`<?=$event_list_str?>`);
	let str = JSON.parse(`<?=$jsonstr?>`);
	let EventsTableSettings = {
		"headArray" : [str["date_label"], str["event_label"]],
		"activeHeader" :"",
		"colData" : ["datestart_typed", "name"],
		"active" : false,
		"colSorted" : -1
	};
	/* let's add .rowLink to allow click on a row */
	event_list.forEach((element) =>{
		let dest = "<?=$cfg['event_page']?>?id=" + element.id.toString(10);
		element.rowLink=dest;
		let truedate = new Date(element.datestart);
        element.datestart_typed = truedate;
	});
	var EventsTable = new smartTable (
		"event_list", 
		event_list,
		EventsTableSettings
	);
</script>
<?php if (isset($_SESSION['user_id'])): ?>
	<br/>
	<a href="<?=$cfg['create_event_page']?>"><button><?=$str['add_event']?></button></a> 
<?php endif; ?>