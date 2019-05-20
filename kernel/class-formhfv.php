<?php


class FormFV {



public static $baselink='msdb';





public static function isFull (&$a) {$c=0; foreach ($a as $v) {if(strlen($v)) {++$c;}}return count($a)==$c;}
public static function getEmptySelect ($m=0) {return ($m > 1) ? array('0'=>'Leer') : array(''=>'--Auswahl--');}
public static function printVertical ($f,$a) {for ($i=0; $i < count ($f); $i++) {$out[]='<tr><td>'.$f[$i].'</td><th valign="top">'.$a[$i]."</th></tr>";} return implode("\n",$out);}
public static function addParam ($a) {$out=array(); while (list($k,$v)=each($a)) {$out[]=$k.'="'.$v.'"';} return implode("\x20",$out);}


public static function transformDate ($d,$p) {$part=explode("\32",$d);array_walk($part,function (&$v, $k, $p) {list($i,$e)=($p);$v=implode($i, array_reverse(explode($e, $v)));},$p);return implode("\32",$part);}

public static function date2Dat ($date) {return self::transformDate($date,array('-','.'));}
public static function date2Visual ($date) {return self::transformDate($date,array('.','-'));} 





// Spalte|Ausdruck, Mapping, Array(Spalte<>Wert ...) 
public static function getSelectAdv ($sql,$dsbld=array(),$grpexp=null,$grpmap=array(), $empty=false) {

$r=$GLOBALS[self::$baselink]->query($sql);
if ($empty) {$out=self::getEmptySelect($empty);} else {$out=array();}
$idane=is_array($dsbld) && count ($dsbld) > 0;

	while ($row=$r->fetch_assoc()) {
	if (empty($keycol) || empty($valcol)) {list($keycol,$valcol)=array_keys($row);}
	$key=$row[$keycol]; $val=$row[$valcol];

	if ($grpexp && $row[$grpexp] && $grpmap[$row[$grpexp]]) {$out['OPTGROUP'][$grpmap[$row[$grpexp]]][]=$key;}
	if ($idane) {

		foreach ($dsbld as $col => $neval) {
		if ($row[$col] && $neval <> $row[$col]) {$out['DISABLED'][]=$key; break;}
		}

	}

	$out[$key]=$val;

	}

return ($out);

}



public static function printRadioCheck ($type, $items, $label, $sticky, $shwlbl=false, $dsbld=array()) {


$isa=is_array($sticky);
if ($type=='checkbox' && $isa==false) {$items=array($items=>($shwlbl!==false ? $shwlbl : ''));}
if ($type=='checkbox' && $isa && strpos('[]',strrev($label))!==0) {$label.='[]';}
if ($type=='radio' && $isa) {return 'Fehler: Radio ist Auschluss-Element !';}


	$out=array();
	foreach ($items as $key => $val) {

	if ($isa) {$ischk=in_array($key,$sticky);} else {$ischk=($key==$sticky);}
	$out[]=($shwlbl ? '<tr><td>' : '').'<input '.($ischk ? 'checked ' : '').(in_array($key,$dsbld) ? 'disabled ' : '').' type="'.$type.'" name="'.$label.'" value="'.($key).'">'.($shwlbl ? '<td>'.$val.'</tr>' : '');

	}

return ($shwlbl ? '<table>' : '').implode("\n",$out).($shwlbl ? '</table>' : '');

}



public static function printOption ($items, $sticky, $dsbld=array(),$keys=null) {

// [] == NULL: TRUE (!!!)
if ($keys===null) {$keys=array_keys($items);}
$ismultiple=is_array($sticky); $out=array();

	foreach ($keys as $key) {

	$ischecked=($ismultiple ? in_array($key,$sticky) : ($key==(string)$sticky));
	$out[]='<option '.(in_array($key,$dsbld) ? 'disabled' : '').' value="'.$key.'" '.($ischecked ? 'selected' : '').'>'.htmlentities($items[$key], ENT_NOQUOTES | ENT_IGNORE, "UTF-8").'</option>';

	}

return implode("",$out);

}



public static function printSelect ($items, $label, $sticky, $attrlst=null, $dsbld=array(), $group=array()) {

$out=array();
$ismltpl=is_array($sticky);
if (count($group) > 0) {

$grpdidx=array_reduce($group,function($c,$v) { return $c+array_flip($v);},array());
$snlkeys=array_diff(array_keys($items), array_keys($grpdidx));
$out[]=self::printOption($items,$sticky,$dsbld,$snlkeys);

	foreach ($group as $grpl => $itemkey) {
	$out[]='<optgroup label="'.$grpl.'">';
	$out[]=self::printOption($items,$sticky,$dsbld,$itemkey);
	$out[]='</optgroup>';
	}

} else {

	$out[]=self::printOption($items,$sticky,$dsbld);

}


return '<select '.$attrlst.' name="'.$label.($ismltpl ? '[]" multiple="multiple' : '').'" size="'.($ismltpl ? count($items) : '1').'">'.implode("\n",$out).'</select>';

} # END-S





public static function updateDB($a,$k,$new,$table,$id,$delete=NULL) {

if (is_array($a[$delete])) {

$sql="DELETE FROM $table WHERE $id IN (".implode(',',$a[$delete]).")";
$GLOBALS[self::$baselink]->query($sql);

} // END-F



foreach ($k as $n=>$t) {

	if (is_array($a[$n])==false) {continue;}

	foreach ($a[$n] as $i=> $w) {

		# Ohne Typ: Array_Walk -> Callback
		if (substr($t,0,4)=='date') {$w=self::date2Dat($w);}

		if (is_array($w)==false) {$w=array($w);}
		foreach ($w as $si => $sw) {$u[$i][$si][$n]=$GLOBALS[self::$baselink]->escape_string($sw);}

	}

}


if (is_array($u)==false) {return null;}


$r=array();
foreach ($u as $i=>$a) {


	if ($i==$new) {

		foreach ($a as $sa) {
			if (self::isFull($sa)==false) {continue;}
			$sql="INSERT INTO $table (".implode(',',array_keys($sa)).") VALUES ('".implode("','",$sa)."')";
			$r[]=$GLOBALS[self::$baselink]->query($sql);
		}


	} else {

		foreach ($a as &$ta) {

			$tp=array(); foreach ($k as $n => $t) {if ($t=='checkbox' && array_key_exists($n,$ta)==false) {$ta[$n]=0;} $tp[]=$n."='".$ta[$n]."'";}
			$sql='UPDATE '.$table.' SET '.implode(',',$tp).' WHERE '.$id.' = "'.$i.'"';

		}

		$r[]=$GLOBALS[self::$baselink]->query($sql);

	}
}

return array_sum($r)==count($r);

}



public static function makeHTML ($daten,$type,$uid,$selected,$vertical=false,$delete=false) {

if ((!empty($delete) || $delete==false) && substr($uid,0,3)=='NEW') {$out[]='<code>Neu</code>';} elseif (!empty($delete)) {$out[]=self::printRadioCheck('checkbox', $uid, $delete.'[]', 0, false);}

	foreach ($type as $ident => $itype) {

	list($stype,$param)=explode('=',$itype,2); $narr=explode('|',$param);
	$narr=array_filter($narr,function ($val) {return trim($val) <> '';}); $varr=array(); # | >
	foreach ($narr as $val) {list($k,$v)=explode('>',$val); $varr[$k]=$v;}


		if ($stype=='text' || $stype=='number' || $stype=='range' || $stype=='hidden' || $stype=='url' || $stype=='search' || $stype=='color') {
		$out[]='<input '.self::addParam($varr).' type="'.$stype.'" name="'.$ident.'['.$uid.']" value="'.$daten[$ident].'">';

		} elseif ($stype=='email' || $stype=='mail') { # Fallback?!
		$out[]='<input '.self::addParam($varr).' type="email" style="font-variant:small-caps" name="'.$ident.'['.$uid.']" value="'.$daten[$ident].'">';

		} elseif ($stype=='password' || $stype=='file') {
		$out[]='<input '.self::addParam($varr).' type="'.$stype.'" name="'.$ident.'['.$uid.']">';

		} elseif ($stype=='area') {
		$out[]='<textarea '.self::addParam($varr).' name="'.$ident.'['.$uid.']">'.$daten[$ident].'</textarea>';

		} elseif ($stype=='datetime' || $stype=='datepicker' || $stype=='date' || $stype=='time') {
		if ($stype=='datepicker') {$stype='text'; $varr['class']='datepicker'; $varr['data-date-format']='d-m-y';} $daten[$ident]=self::date2Visual($daten[$ident]);
		$out[]='<input '.self::addParam($varr).' type="'.$stype.'" name="'.$ident.'['.$uid.']" value="'.$daten[$ident].'">';

		} elseif ($stype=='checkbox') {
		$out[]=self::printRadioCheck ($stype, $selected[$ident], $ident.'['.$uid.']', $daten[$ident], false);

		} elseif ($stype=='selection' || $stype=='select') {
		foreach (array('DISABLED','OPTGROUP') as $mode) {if (is_array($selected[$ident][$mode])) {$$mode=$selected[$ident][$mode];} else {$$mode=array();}unset($selected[$ident][$mode]);}
		$out[]=self::printSelect($selected[$ident],$ident.'['.$uid.']',$daten[$ident],self::addParam($varr),$DISABLED,$OPTGROUP);

		} elseif ($stype=='radio') {
		$out[]=self::printRadioCheck ($stype, $selected[$ident], $ident.'['.$uid.']', $daten[$ident], true);

		} elseif ($stype=='label') { #LABEL=TAG
		if (!empty($param)) {$out[]='<'.$param.'>'.$daten[$ident].'</'.$param.'>';} else {$out[]=$daten[$ident];}

		}

	}

if ($vertical == false) {return implode('</td><td>',$out);} else {return $out;}

} // END


}



?>