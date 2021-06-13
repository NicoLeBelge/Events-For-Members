<?php
function clean_string($data) {
  $data = trim($data); // supprime les espaces en début et fin
  $data = stripslashes($data);// supprime les antisslashes
  $data = htmlspecialchars($data); // remplace les caractères spéciaux par leur entité html
  return $data;
}
function no_10_13 ($ch) { // supprime les CR et remplace les LF par un espace
	$n='';
	$l = mb_strlen($ch);
	for ($i=0; $i<=$l ;$i++) {
		if ($ch[$i]!=chr(13)){
			if ($ch[$i]!=chr(10)){
				$n .= $ch[$i];
			}else{
				$n .= " ";
			}
		}
	}
	return($n);
}
function no_tag ($ch) { // supprime toutes les balises html
	$n='';
	$pause=false;
	$l = strlen($ch);
	for ($i=0; $i<=$l ;$i++) {
		if ($ch[$i]=='<') $pause=true; // on pause dès qu'on voit un commentaire
		if ($pause and $i>=1 and $ch[$i-1]=='>') $pause=false;
		if(!$pause) $n .= $ch[$i];	
	}
	return($n);
}
function no_comment ($ch) {
	//echo "appel de la fonction no_comment  pour la string <strong>".$ch."</strong><br/>";
	$n='';
	$pause=false;
	$l = strlen($ch);
	for ($i=0; $i<=$l ;$i++) {
		if ($ch[$i]=='{') $pause=true; // on pause dès qu'on voit un commentaire
		if ($pause and $i>=1 and $ch[$i-1]=='}') $pause=false;
		if(!$pause) $n .= $ch[$i];	
	}
	return($n);
}
function no_cr($string) {// retourne une chaîne sans le CR, donc parfois plus courte (besoin pour fenlist --> liste de fens)
	$n='';
	$l = strlen($string);
	for ($i=0; $i<=$l-1 ;$i++) {
		if($string[$i]!==chr(13)) $n .= $string[$i];	
	}
	return($n);
}
function hiddenemail($email) { // cache la partie centrale de l'adresse
	$em = explode("@",$email); // $em[0]=avant@ et $em[1]=après@
	$name = $em[0];
	$len = strlen($name);
	$str_arr = str_split($name); // transforme une chaîne en un tableau
	for($k=2;$k<$len-2;$k++){
		$str_arr[$k] = '*';
		}
	$em[0] = implode('',$str_arr); 
	$new_name = implode('@',$em);	
	return $new_name;	
}
function RandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $rndstr = '';
    for ($i = 0; $i < $length; $i++) {
        $rndstr .= $characters[rand(0, $charactersLength - 1)];
    }
    return $rndstr;
}
?>
