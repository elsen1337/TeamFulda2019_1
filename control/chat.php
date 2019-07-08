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


// FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'cdel');


$akey='msgdir';
if (is_array($_POST[$akey])) {

	require('../kernel/class-chat.php');

	foreach ($_POST[$akey] as $mid => $dir) {
		
		// insertMessageFromLessor2Tenant();
		$callFunc=array('Chat','insertMessageFrom'.$dir);
		
		if (is_callable($callFunc)) {
			$callFunc($_POST['vm_id'][$mid],$_POST['m_id'][$mid],$_POST['msg'][$mid],$mid);
		}
			
	}

}



$allowedFilters=array('m_id','vm_id');


if (strlen($_GET['edit']) > 0) {


	list($vmid,$mid)=$allowedFilters; $objid=$_GET['edit'];
	
	$vchoice=FormFV::getEmptySelect()+FormFV::getSelectAdv('SELECT vm_id, nname FROM vermieter'.(array_key_exists($vmid,$_GET) ? ' WHERE vm_id='.$_GET[$vmid] : ''));
	$mchoice=FormFV::getEmptySelect()+FormFV::getSelectAdv('SELECT m_id, nname FROM mieter'.(array_key_exists($mid,$_GET) ? ' WHERE m_id='.$_GET[$mid] : ''));
	
	$dchoice=FormFV::getEmptySelect()+array('Lessor2Tenant'=>'Vermieter > Mieter','Tenant2Lessor'=>'Mieter > Vermieter');
	

	$sql='SELECT c.mid, c.msg, c.date, ABS(c.m_id) AS m_id, ABS(c.vm_id) AS vm_id, IF (c.vm_id < 0 AND c.m_id > 0,"Lessor2Tenant","Tenant2Lessor") AS msgdir, IF(c.vm_id < 0, c.vm_id, c.m_id) AS senderid,  IF(c.m_id > 0, c.m_id, c.vm_id) AS recieverid FROM '.$ptbl.' AS c WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';$row['date']=date('Y-m-d H:i:s');}
	
	$formGETParam=[];
	foreach ($allowedFilters as $paramKey) {
		if (array_key_exists($paramKey,$_GET)) {
			$formGETParam[$paramKey]=$_GET[$paramKey];
			$row[$paramKey]=$_GET[$paramKey];
		}
	}
	
	GUI::printNotice($row['msgdir']);

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?'.http_build_query($formGETParam).'" method="post"><table>';
	
	echo '<td>'.FormFV::printSelect($dchoice,$akey.'['.$objid.']',$row['msgdir']).'</td><th>Richtung</th>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('m_id'=>$mchoice,'vm_id'=>$vchoice),true,false),array('Vermieter','Mieter','Datum','Nachricht'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';


} else {

	GUI::printNotice('Filterung nach Vermieter oder Mieter über Link aus jeweiliger Übersicht.');

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'" method="post">';
	echo '<table cellpadding="2"><tr><th class="delcol">[&times;]</th><th>Mieter</th><th>Vermieter</th><th>Datum</th><th>Text...</th></tr>';

	$sqlFilter=[];
	foreach ($allowedFilters as $fVar) {
		if (array_key_exists($fVar,$_GET)) {
			$sqlFilter[]='ABS(c.'.$fVar.')='.$_GET[$fVar];
		}
	}


	$sql='SELECT c.mid, c.date, SUBSTRING_INDEX(msg, " ", 10) AS msg,  IF (c.vm_id < 0 AND c.m_id > 0,"Lessor2Tenant","Tenant2Lessor") AS msgdir, m.nname AS vml, v.nname AS mml FROM m_chat AS c JOIN mieter AS m ON m.m_id=ABS(c.m_id) JOIN vermieter AS v ON v.vm_id=ABS(c.vm_id) '.(count($sqlFilter) > 0 ? ' WHERE '.implode(' AND ',$sqlFilter) : '').' ORDER BY c.vm_id, c.m_id, c.date DESC';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_object()) {
		
		echo '<tr><td><input type="checkbox" value="'.$row->mid.'" name="cdel[]"><td>'.$row->vml.'<td>'.$row->mml.'</td>';
		echo '<td>'.$row->date.'</td><td>'.$row->msg.' [...]</td><td><a href="?edit='.$row->mid.'&'.http_build_query($_GET).'">Bearbeiten</a></td></tr>'."\n";
		
	}

	echo '</table><input type="submit" value="Lösche markierte Nachrichten">';
	
	GUI::printInsert('<a href="?edit=new&'.http_build_query($_GET).'">Erstelle neue Chatnachricht</a>');


}

?></body></html>
 
