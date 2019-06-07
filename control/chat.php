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

<body class="wimg"><h1>Chatnachrichten (Controlpanel)</h1><?php



$type=array('vm_id'=>'selection','m_id'=>'selection','date'=>'text','msg'=>'area');
$ptbl='m_chat';
$pkey='mid';

FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'cdel');




$allowedFilters=array('m_id','vm_id');


if (strlen($_GET['edit']) > 0) {


	list($vmid,$mid)=$allowedFilters;
	$vchoice=FormFV::getEmptySelect()+FormFV::getSelectAdv('SELECT vm_id, nname FROM vermieter'.(array_key_exists($vmid,$_GET) ? ' WHERE vm_id='.$_GET[$vmid] : ''));
	$mchoice=FormFV::getEmptySelect()+FormFV::getSelectAdv('SELECT m_id, nname FROM mieter'.(array_key_exists($mid,$_GET) ? ' WHERE m_id='.$_GET[$mid] : ''));
	

	$sql='SELECT v.* FROM '.$ptbl.' AS v WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';$row['date']=date('Y-m-d H:i:s');}
	
	$formGETParam=[];
	foreach ($allowedFilters as $paramKey) {
		if (array_key_exists($paramKey,$_GET)) {
			$formGETParam[$paramKey]=$_GET[$paramKey];
			$row[$paramKey]=$_GET[$paramKey];
		}
	}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?'.http_build_query($formGETParam).'" method="post"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('m_id'=>$mchoice,'vm_id'=>$vchoice),true,false),array('Vermieter','Mieter','Datum','Nachricht'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';


} else {

	GUI::printNotice('Filterung nach Vermieter oder Mieter über Link aus jeweiliger Übersicht.');

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'" method="post">';
	echo '<table cellpadding="2"><tr><th class="delcol">[&times;]</th><th>Mieter</th><th>Vermieter</th><th>Datum</th><th>Text...</th></tr>';

	$sqlFilter=[];
	foreach ($allowedFilters as $fVar) {
		if (array_key_exists($fVar,$_GET)) {
			$sqlFilter[]='c.'.$fVar.'='.$_GET[$fVar];
		}
	}


	$sql='SELECT c.mid, c.date, SUBSTRING_INDEX(msg, " ", 10) AS msg, m.nname AS vml, v.nname AS mml FROM m_chat AS c JOIN mieter AS m ON m.m_id=c.m_id JOIN vermieter AS v ON v.vm_id=c.vm_id'.(count($sqlFilter) > 0 ? ' WHERE '.implode(' AND ',$sqlFilter) : '').' ORDER BY c.vm_id, c.m_id, c.date DESC';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_object()) {
		
		echo '<tr><td><input type="checkbox" value="'.$row->mid.'" name="cdel[]"><td>'.$row->vml.'<td>'.$row->mml.'</td>';
		echo '<td>'.$row->date.'</td><td>'.$row->msg.' [...]</td><td><a href="?edit='.$row->mid.'&'.http_build_query($_GET).'">Bearbeiten</a></td></tr>'."\n";
		
	}

	echo '</table><input type="submit" value="Lösche markierte Nachrichten">';
	
	GUI::printInsert('<a href="?edit=new&'.http_build_query($_GET).'">Erstelle neue Chatnachricht</a>');


}

?></body></html>
 
