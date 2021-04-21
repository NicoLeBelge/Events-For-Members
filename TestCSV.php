<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
    </head>

    <body >
	<button onclick="download()">click</button>	
	
<script type="text/javascript">
	
	function download() {
	var csvstring ="pipo";
	var filename ="pipo.csv";
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csvstring));
	element.setAttribute('download', filename);
	element.style.display = 'none';// necessary ??
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element); // necessary ??
	}
</script>

</body>
</html>
