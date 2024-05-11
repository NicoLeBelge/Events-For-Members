
<?php
	$str = json_decode(file_get_contents('./_json/strings.json'),true);	
	if (isset($_GET['url'])) {
		$url = $_GET['url']; 
	} else {
		echo "this page needs parameter";
		exit();
	}
?>
    <div id="qrcode"></div>
	<br>
	<a href="<?=$return_page?>"><button><?=$str["Back_to_event"]?></button></a>
<script type="text/javascript" src="./JS/qrcode.min.js"></script>
<script>
	const url="<?=$url?>";
	new QRCode(document.getElementById("qrcode"), url);
</script>
