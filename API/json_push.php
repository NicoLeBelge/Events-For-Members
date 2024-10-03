<?php 

$jsonString = file_get_contents('test.json');	
$jsonData = json_decode($jsonString, true);
// var_dump($jsonData); // ça, ça retourne bien un array(2)

//$jsdata = $jsonData->data;
$jsdata = $jsonData["data"]; // Je sais pas pourquoi $jsonData->data retourne NULL
// var_dump($jsdata); // retourne bien un array(n), chacun contenant un array associatif


// Initialize cURL session
// $url = 'API-add-members.php'; // !!!!!!!!!!!!!!!!! pas de fichier → faut mettre en localhost.

$url = 'https://www.chessmooc.org/web/PUCE-ins/API/helloasso2.php?t=136&key=TlfjQYm7PZ'; 
$ch = curl_init($url);


// Set the Content-Type header to application/json
$headers = array(
    'Content-Type: application/json', 
    'Content-Length: ' . strlen($jsonString)
);

// Set cURL options
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CAINFO, 'C:/util/cert/cacert.pem');

// Execute the cURL request
$response = curl_exec($ch);
// var_dump ($response);
// // 

echo "curl_execute done";
if ($content === false) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    echo $response;
}
// exit();

curl_close($ch);

?>
