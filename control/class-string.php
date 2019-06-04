<?php

class stringNormalize {


public static function normalizeURL ($str) {

setlocale(LC_COLLATE, array('de_DE.utf8','deu.utf8'));
$a=preg_split('#[^\\pL\d]+#u',$str,null,PREG_SPLIT_NO_EMPTY);

$str=iconv('utf-8', 'us-ascii//TRANSLIT', implode('-',$a) );
$str=preg_replace('#\"([oua])#iu', '\1e', $str);
$str=preg_replace('#[^-\w]+#', '', $str);

return empty($str) ? 'no-mark' : strtolower($str);

} # END-F


}


class StringMatch {

private $word = array();

public function findClosest($input) {

$shortest = -1;

foreach ($this->word as $word) {

	$lev = levenshtein($input, $word);

	if ($lev == 0) {
	$closest = $word;
	$shortest = 0;
	break;
	}

	if ($lev <= $shortest || $shortest < 0) {
	$closest  = $word;
	$shortest = $lev;
	}

}

	if ($shortest == 0) {
	echo "Exact: $closest";

	} else {
	echo "Meant: $closest?";

	}

}

}

?>