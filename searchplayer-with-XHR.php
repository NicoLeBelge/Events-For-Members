<!DOCTYPE html>
<html>
<!-- on met toute la construction du tableau en externe -->
<head>
    <title>Test Consommation API</title>
	<meta charset="UTF-8" />
	<script src="scripts-tournois.js"></script>
	<link rel="stylesheet" href="../ChessMOOC-style.css" /> 
	<link rel="icon" type="image/png" href="../img/logo-3-96.png" />
    <style>
		tr:nth-child(even) {
			background: white;
		}
		table, th, td {
			color : #505050;
			border-collapse: collapse;
			padding-left: 10px;
			padding-right: 10 px;
		}
		table td:nth-child(1) {
			width: 25%;
		}		
		table td:nth-child(2) {
			width: 20%;
		}		
		table td:nth-child(3) {
			width: 5%;
		}
		table td:nth-child(4) {
			width: 7%;
		}
		table td:nth-child(5) {
			width: 23%;
		}
		table td:nth-child(6) {
			width: 20%;
		}		
		tr:hover {
			font-weight: bold;
			cursor: pointer;
		}
	</style>
</head>
<body>
	<div class='form800'>
	<form id='myForm'>
		<label for="namestart">👤 entrez le début du nom</label>
		<input type="text" name="identifier" id="namestart" required>
	</form> 
	<button onclick = trouve() >trouve-moi ça ! </button>
	<br/><br/>
	<div id="playertable"></div>
	
	</div>
	
<script type="text/javascript">
	var request = new XMLHttpRequest();
	function trouve(){
		var myForm = document.getElementById('myForm');
		formData = new FormData(myForm);
		var start = document.getElementById('namestart').value;
		var requestURL = 'get-player-list-from-start.php?start=' + start;
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
