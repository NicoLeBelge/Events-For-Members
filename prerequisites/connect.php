<?php	
//This file must be located in the folder /_local-connected at the same level as /Events-For-Members

$secrets_path = dirname(__DIR__) . "/_secrets/secrets.json";
$secrets = json_decode(file_get_contents($secrets_path,true));	
$servername = $secrets->server_name;	
$username = $secrets->user_name;	
$password = $secrets->db_password;		
$db_name = $secrets->db_name;	

try {  $conn = new PDO("mysql:host=$servername;dbname=$db_name;charset=utf8", $username, $password);  
	/* set the PDO error mode to exception  */  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
	} 
catch(PDOException $e) {  
	echo "Connection failed: " . $e->getMessage() . "<br/>";
	}
?>