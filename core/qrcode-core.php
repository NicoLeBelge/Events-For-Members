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
<script type="text/javascript" src="./JS/qrcode.min.js"></script>
<script>
	new QRCode(document.getElementById("qrcode"), "https://www.chessmooc.org/t.php?t=119");
</script>
