<?php

//require('core-auth.php');


require('../core-mysqla.php');

require('../kernel/class-formhfv.php');
require('../kernel/class-ui.php');


header('Content-Type: text/html; charset=UTF-8');


?><!DOCTYPE HTML><html><head>
<title>HSF-MScAI-TP - StudyHome</title>
<link rel="stylesheet" href="../client/layout/base-messages.css">
<link rel="stylesheet" href="control.css"></head>

<body class="wimg"><h1>Favoriten der Mieter (Controlpanel)</h1><?php



$iDelKey='fdel';
if (array_key_exists($iDelKey,$_POST)) {

	foreach ($_POST[$iDelKey] as $compoundKey) {
		
		list($mid,$wid)=explode('-',$compoundKey);
		$msdb->query('DELETE FROM m_favorit WHERE wohn_id='.$wid.' AND m_id='.$mid);	
	
	}

}


$ufKey='score';
$keyNewWohnID='nwid';
$keyNewScore='nscore';

if (strlen($_POST[$keyNewWohnID]) > 0 && strlen($_POST[$keyNewScore]) > 0) {
	
	$newCKey=$_GET['m_id'].'-'.$_POST[$keyNewWohnID];
	$_POST[$ufKey][$newCKey]=$_POST[$keyNewScore];
	list($mid,$wid)=explode('-',$newCKey);
	
	$msdb->query('INSERT IGNORE INTO m_favorit (m_id,wohn_id) VALUES ('.$mid.', '.$wid.')');

}



if (array_key_exists($ufKey,$_POST)) {
	foreach ($_POST[$ufKey] as $compoundKey=>$score) {
		
		list($mid,$wid)=explode('-',$compoundKey);
		$msdb->query('UPDATE m_favorit SET score='.$score.', cdate=NOW() WHERE wohn_id='.$wid.' AND m_id='.$mid);
	
	}

}






if (strlen($_GET['edit']) > 0) {



} else {

	GUI::printNotice('Filterung nach Wohnung oder Mieter über Link aus jeweiliger Übersicht.');

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'" method="post">';
	echo '<table cellpadding="2"><tr><th class="delcol">[&times;]</th><th>Mieter</th><th>Wohnung</th><th>Score</th><th>mDate</th></tr>';

	$sqlFilter=[];
	$allowedFilters=array('m_id','wohn_id');
	foreach ($allowedFilters as $fVar) {
		if (array_key_exists($fVar,$_REQUEST)) {
			$sqlFilter[]='f.'.$fVar.'='.$_REQUEST[$fVar];
		}
	}
	
	$sql='SELECT f.*, m.nname, w.name FROM m_favorit AS f JOIN mieter AS m ON m.m_id=f.m_id JOIN wohnung AS w ON w.wohn_id=f.wohn_id'.(count($sqlFilter) > 0 ? ' WHERE '.implode(' AND ',$sqlFilter) : '').' ORDER BY f.cdate DESC';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_object()) {
		
		echo '<tr><td><input type="checkbox" value="'.$row->m_id.'-'.$row->wohn_id.'" name="fdel[]"><td>'.$row->nname.'<td>'.$row->name.'<td>';
		
		if (count($sqlFilter) == 2) {
			echo '<input type="number" name="score['.$row->m_id.'-'.$row->wohn_id.']" value="'.$row->score.'">';	
		} else {
			echo $row->score;	
		}
		
		echo '<td>'.$row->cdate.'</td></tr>'."\n";
		
	}

	echo '</table>';

	if (array_key_exists('m_id',$_REQUEST)) {
		$wchoice=FormFV::getEmptySelect()+FormFV::getSelectAdv('SELECT wohn_id, name FROM wohnung');
		
		echo '<p><fieldset style="display:inline-block"><legend>Erstelle neuen Favoriten für aktuellen Mieter</legend>';
		echo FormFV::printSelect($wchoice,'nwid',null);
		echo '<input type="range" min="0" max="5" name="nscore" value="3">';
		echo '</fieldset></p>';
		
	}
	
	echo '<input type="submit" value="Erstellen &middot; Aktualisieren &middot; Löschen">';

}

?></body></html>
