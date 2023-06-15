<?php

/**
 * Unaccent the input string string. An example string like `ÀØėÿᾜὨζὅБю`
 * will be translated to `AOeyIOzoBY`
 *  
 * @param string $str
 * 
 * @return string unaccented string
 */
function unaccent_up( $str )
{
  $transliteration = array(
    'Ö' => 'O','Ü' => 'U','ä' => 'a','æ' => 'a',
    'ö' => 'o','œ' => 'o','ü' => 'u',
    'È' => 'E',
    'É' => 'E','Ê' => 'E','Ë' => 'E',
    'Ô' => 'O',
    'è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
    'î' => 'i','ï' => 'i',
    'ñ' => 'n',
    'ô' => 'o'
    );
    $str = str_replace( array_keys( $transliteration ),
                        array_values( $transliteration ),
                        $str);
    $str = trim($str);
    return strtoupper($str);
}

/**
 * Compares Person name 1 and Person name 2
 * returns a score depending on comparison result
 *    strictly identical --> 1
 *    identical after unaccented_up --> 0.95
 *    identical after unaccented_up and swap firstname/lastname --> 0.90
 *    different --> average (1-levenshtein/length)
 * 
 * @param string $FirstName1
 * @param string $LastName1
 * @param string $FirstName2
 * @param string $LastName2
 * 
 * @return float score
 */

function person_match( $FirstName1 =" ", $LastName1=" ", $FirstName2=" ", $LastName2=" " )
{
  $score = 0;
  if ($FirstName1 == $FirstName2 && $LastName1 == $LastName2) {
    /* perfect match */ 
    $score = 1; 
  } else {
    $u_First1 = unaccent_up($FirstName1);
    $u_First2 = unaccent_up($FirstName2);
    $u_Last1 = unaccent_up($LastName1);
    $u_Last2 = unaccent_up($LastName2);
    
    if (($u_First1 == $u_First2 AND $u_Last1 == $u_Last2) || ($u_First1 == $u_Last2 AND $u_Last1 == $u_First2)){
      /* near perfect match */ 
      $score = 0.95; 
    } else {
      if ( $u_First1 == $u_Last2 && $u_First2 == $u_Last1) {
        /* close match */ 
        $score = 0.90; 
      } else {
        /* let's calculate the distance  */
        $lev_first = levenshtein($u_First1,$u_First2);
        $lev_Last = levenshtein($u_Last1,$u_Last2);
        $score = 1- 0.5 * ($lev_first/strlen($u_First1) + $lev_Last/strlen($u_Last1) );
      }
    }
  }
  return $score;
}
function item_array_to_player_array ($items_array)
{
  $player_arr = array();
  
  foreach ($items_array as $item) // (3)
	{
		$player = array(); // on réinitialise un nouveau player
		$player += ["firstName"=>$item->user->firstName];
		$player += ["lastName"=>$item->user->lastName];
		$player += ["licence"=>""];
		$player += ["subevent"=>""];

		$custfielsobj = $item->customFields;
		/* on balaye tous les objets de l'array customFields pour récupérer le numéro de licence, et éventuellement le tournoi*/
		foreach ($custfielsobj as $customField) 
		{
			if (stripos($customField->name, 'licence') <>0 ) $player["licence"] = $customField->answer;
			if (stripos($customField->name, 'tournoi') <>0 ) $player["subevent"] = $customField->answer;
		}
		array_push($player_arr, $player);
	}
  return $player_arr;
}

function match_rate_to_match_sign ($rate)
{
    $matching_sign = "";
    if ( $rate > 0.8 )
    {
      $matching_sign = "✅";
    } else {
      if ( $rate > 0.5 ){
        $matching_sign = "⚠️";
      } else {
        $matching_sign = "❌"; 
      }
    }
    return $matching_sign;
}