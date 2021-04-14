<?php
$json = file_get_contents('strings.json');
$str = json_decode($json,true);	
?>
<!DOCTYPE html>
<html>
<!-- on met toute la construction du tableau en externe -->
<head>
    <title>Test Consommation API</title>
	<meta charset="UTF-8" />
	<script src="./JS/scripts-tournois.js"></script>
	<link rel="stylesheet" href="../css/ChessMOOC-style.css" /> 
	<link rel="stylesheet" href="./css/trn.css" /> 
	<link rel="icon" type="image/png" href="../img/logo-3-96.png" />
    
</head>
<body>
	<div class='form800'>
	<form id='myForm'>
		<label for="namestart"><?= $str['enter_start_name'] ?></label>
		<input type="text" name="identifier" id="namestart" required>
	</form> 
	<button onclick = trouve() ><?= $str['search'] ?></button>
	<br/><br/>
	<div id="playertable"></div>
	
	</div>
	
<script type="text/javascript">
	var request = new XMLHttpRequest();
	function trouve(){
		var myForm = document.getElementById('myForm');
		formData = new FormData(myForm);
		var start = document.getElementById('namestart').value;
		
		var requestURL = './API/get-memberlist-by-namestart.php?start=' + start;
		console.log(requestURL);
		request.open('GET', requestURL);
		request.responseType = 'json';
		request.send();
	}

	request.onreadystatechange  = function() {
		if (this.readyState == 4 && this.status == 200) {
			var players = this.response;
			console.log(players);
			var tch = PlayersObjToTable(players);
			var e = document.getElementById('playertable');
			e.innerHTML = tch;
		}
	}	
</script >
</body>
</html>
