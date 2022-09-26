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
